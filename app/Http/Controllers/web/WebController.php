<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\CityRoute;
use App\Models\ShipmentPayment;
use App\Models\ShipmentAddress;
use App\Models\TransportCartItem;
use App\Models\TransportLead;
use App\Models\TransportQuote;
use App\Models\TransportServicePrice;
use App\Services\GuestCartService;
use App\Services\ShipmentInvoicePdfService;
use App\Services\ShipmentPricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class WebController extends Controller
{
    public function __construct(
        private GuestCartService $guestCartService,
        private ShipmentPricingService $pricingService
    ) {
    }

    public function addShipmentItem()
    {
        $this->guestCartService->guestId();
        $cityRoutes = CityRoute::where('is_active', true)
            ->orderBy('from_city')
            ->orderBy('to_city')
            ->get();

        return view('web.shipment-add-item', compact('cityRoutes'));
    }

    public function saveShipmentItems(Request $request)
    {
        $validated = $request->validate([
            'city_route_id' => ['required', 'exists:city_routes,id'],
            'pickup_date' => ['required', 'date'],
            'delivery_date' => ['required', 'date', 'after_or_equal:pickup_date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_name' => ['required', 'string', 'max:255'],
            'items.*.item_type' => ['nullable', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.length_cm' => ['nullable', 'numeric', 'min:0'],
            'items.*.width_cm' => ['nullable', 'numeric', 'min:0'],
            'items.*.height_cm' => ['required', 'numeric', 'min:0.01'],
            'items.*.weight_kg' => ['required', 'numeric', 'min:0.01'],
        ]);

        foreach ($validated['items'] as $item) {
            $cartItem = TransportCartItem::create([
                'user_id' => auth()->id(),
                'guest_id' => auth()->check() ? null : $this->guestCartService->guestId(),
                'city_route_id' => $validated['city_route_id'],
                'item_name' => $item['item_name'],
                'item_type' => $item['item_type'] ?? null,
                'quantity' => $item['quantity'],
                'length_cm' => $item['length_cm'] ?? null,
                'width_cm' => $item['width_cm'] ?? null,
                'height_cm' => $item['height_cm'],
                'weight_kg' => $item['weight_kg'],
                'pickup_date' => $validated['pickup_date'],
                'delivery_date' => $validated['delivery_date'],
            ]);

            $this->updateCartEstimate($cartItem);
        }

        return redirect()->route('shipment.cart')->with('success', 'Items added to cart successfully');
    }

    public function shipmentCart()
    {
        $price = TransportServicePrice::where('is_active', true)->orderBy('id')->first();
        $cartItems = $this->cartQuery()
            ->with('cityRoute')
            ->latest()
            ->get();
        $cartTotal = 0;

        $cartItems->each(function (TransportCartItem $item) use (&$cartTotal) {
            $item->calculated_price = 0;
            $item->price_error = null;
            $itemPrice = $this->priceForItemType($item->item_type);

            if (!$itemPrice) {
                $item->price_error = 'Price not set';
                return;
            }

            $route = $this->findRoute($item);

            if (!$route) {
                $item->price_error = 'Route not found';
                return;
            }

            $breakdown = $this->pricingService->calculateCartItem($item, $itemPrice, $route);
            $item->price_breakdown = $breakdown;
            $item->calculated_price = $breakdown['total_payment'];
            $cartTotal += $item->calculated_price;
        });

        return view('web.shipment-cart', compact('cartItems', 'cartTotal', 'price'));
    }

    public function shipmentLeads()
    {
        $leads = TransportLead::with('cityRoute')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('web.shipment-leads', compact('leads'));
    }

    public function trackShipment(Request $request)
    {
        $trackingNumber = $request->query('tracking_number');
        $lead = null;
        $userLeads = collect();

        if ($trackingNumber) {
            $lead = TransportLead::with(['cityRoute', 'user', 'latestPayment'])
                ->where('tracking_number', $trackingNumber)
                ->when(auth()->check(), function ($query) {
                    $query->where('user_id', auth()->id());
                })
                ->first();
        }

        if (auth()->check() && !$trackingNumber) {
            $userLeads = TransportLead::with('cityRoute')
                ->where('user_id', auth()->id())
                ->latest()
                ->limit(10)
                ->get();
        }

        return view('web.track-shipment', compact('lead', 'trackingNumber', 'userLeads'));
    }

    public function downloadShipmentInvoice(string $trackingNumber, ShipmentInvoicePdfService $invoicePdfService)
    {
        $lead = TransportLead::with(['cityRoute', 'user', 'latestPayment'])
            ->where('tracking_number', $trackingNumber)
            ->when(auth()->check(), function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->firstOrFail();

        if ($lead->admin_status !== 'delivered') {
            return redirect()
                ->route('shipment.track', ['tracking_number' => $lead->tracking_number])
                ->with('error', 'Invoice will be available after admin marks shipment as delivered.');
        }

        $payment = $lead->latestPayment ?: $this->createInvoicePayment($lead);
        TransportQuote::syncFromLead($lead, $payment->invoice_number);
        $fileName = ($payment->invoice_number ?: $lead->tracking_number) . '.pdf';

        return response($invoicePdfService->output($lead, $payment), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    public function deleteShipmentCartItem($id)
    {
        $cartItem = $this->cartQuery()->findOrFail($id);
        $cartItem->delete();

        return redirect()->route('shipment.cart')->with('success', 'Item removed from cart');
    }

    public function checkoutShipmentCart()
    {
        $price = TransportServicePrice::where('is_active', true)->orderBy('id')->first();

        if (!$price) {
            return back()->with('error', 'Please contact admin. Transport price is not set.');
        }

        $cartItems = TransportCartItem::with('cityRoute')
            ->where('user_id', auth()->id())
            ->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Your cart is empty.');
        }

        foreach ($cartItems as $item) {
            if (!$this->priceForItemType($item->item_type)) {
                return back()->with('error', 'Please add an active transport service price for all cart items before saving to leads.');
            }

            if (!$this->findRoute($item)) {
                return back()->with('error', 'Please add an active city route for all cart items before saving to leads.');
            }
        }

        DB::transaction(function () use ($cartItems) {
            foreach ($cartItems as $item) {
                $price = $this->priceForItemType($item->item_type);
                $route = $this->findRoute($item);
                $breakdown = $this->pricingService->calculateCartItem($item, $price, $route);

                TransportLead::create(array_merge([
                    'user_id' => auth()->id(),
                    'item_name' => $item->item_name,
                    'item_type' => $item->item_type ?: $price->item_type,
                    'quantity' => $item->quantity,
                    'length_cm' => $item->length_cm,
                    'width_cm' => $item->width_cm,
                    'height_cm' => $item->height_cm,
                    'weight_kg' => $item->weight_kg,
                    'city_route_id' => $item->city_route_id,
                    'requested_pickup_date' => $item->pickup_date,
                    'expected_delivery_date' => $item->delivery_date,
                    'admin_status' => 'pending',
                    'user_status' => 'pending',
                    'payment_status' => 'unpaid',
                    'tracking_number' => $this->generateTrackingNumber(),
                ], $breakdown));
            }

            TransportCartItem::where('user_id', auth()->id())->delete();
        });

        return redirect()->route('shipment.cart')->with('success', 'Cart saved to transport leads successfully.');
    }

    private function cartQuery()
    {
        if (auth()->check()) {
            return TransportCartItem::where('user_id', auth()->id());
        }

        return TransportCartItem::where('guest_id', $this->guestCartService->guestId());
    }

    private function updateCartEstimate(TransportCartItem $item): void
    {
        $price = $this->priceForItemType($item->item_type);
        $item->load('cityRoute');
        $route = $this->findRoute($item);

        if (!$price || !$route) {
            return;
        }

        $item->update([
            'estimated_total' => $this->pricingService->calculateCartItem($item, $price, $route)['total_payment'],
        ]);
    }

    private function priceForItemType(?string $itemType): ?TransportServicePrice
    {
        $query = TransportServicePrice::where('is_active', true);

        if ($itemType) {
            $matchedPrice = (clone $query)
                ->where('item_type', $itemType)
                ->orderBy('id')
                ->first();

            if ($matchedPrice) {
                return $matchedPrice;
            }
        }

        return $query->orderBy('id')->first();
    }

    private function findRoute(TransportCartItem $item): ?CityRoute
    {
        if (!$item->city_route_id) {
            return null;
        }

        return CityRoute::where('is_active', true)->find($item->city_route_id);
    }

    private function generateTrackingNumber(): string
    {
        do {
            $trackingNumber = 'TL-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (TransportLead::where('tracking_number', $trackingNumber)->exists());

        return $trackingNumber;
    }

    private function createInvoicePayment(TransportLead $lead): ShipmentPayment
    {
        $payment = ShipmentPayment::create([
            'invoice_number' => $this->generateInvoiceNumber(),
            'user_id' => $lead->user_id,
            'transport_lead_id' => $lead->id,
            'amount' => $lead->total_payment,
            'method' => $lead->payment_method ?: 'cash',
            'status' => match ($lead->payment_status) {
                'paid' => 'success',
                default => 'pending',
            },
            'transaction_id' => $lead->transaction_id,
            'notes' => 'Invoice generated from tracking page.',
        ]);

        TransportQuote::syncFromLead($lead, $payment->invoice_number);

        return $payment;
    }

    private function generateInvoiceNumber(): string
    {
        do {
            $invoiceNumber = 'INV-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (ShipmentPayment::where('invoice_number', $invoiceNumber)->exists());

        return $invoiceNumber;
    }

    public function UserProfile()
    {
        $user = Auth::user();
        $user->load('shipmentAddress');

        $shipmentStats = [
            'total' => TransportLead::where('user_id', $user->id)->count(),
            'delivered' => TransportLead::where('user_id', $user->id)
                ->where('admin_status', 'delivered')
                ->count(),
            'pending' => TransportLead::where('user_id', $user->id)
                ->where('admin_status', '!=', 'delivered')
                ->count(),
        ];

        $recentShipments = TransportLead::with('cityRoute')
            ->where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('web.userporfile', compact('user', 'shipmentStats', 'recentShipments'));
    }

    public function UserProfileEdit()
    {
        $user = Auth::user();
        $user->load('shipmentAddress');

        $shipmentStats = [
            'total' => TransportLead::where('user_id', $user->id)->count(),
            'delivered' => TransportLead::where('user_id', $user->id)
                ->where('admin_status', 'delivered')
                ->count(),
            'pending' => TransportLead::where('user_id', $user->id)
                ->where('admin_status', '!=', 'delivered')
                ->count(),
        ];

        $recentShipments = TransportLead::with('cityRoute')
            ->where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();
        $isEdit = true;

        return view('web.userporfile', compact('user', 'shipmentStats', 'recentShipments', 'isEdit'));
    }

    public function UpdateUserProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'mobile' => ['nullable', 'digits_between:10,15', Rule::unique('users', 'mobile')->ignore($user->id)],
            'address_line_1' => ['nullable', 'string', 'max:1000'],
            'address_line_2' => ['nullable', 'string', 'max:1000'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'pincode' => ['nullable', 'digits_between:5,6'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'mobile' => $validated['mobile'] ?? null,
            'pincode' => $validated['pincode'] ?? null,
        ]);

        $addressInput = collect($validated)
            ->only(['address_line_1', 'address_line_2', 'city', 'state', 'country', 'pincode'])
            ->map(fn ($value) => is_string($value) ? trim($value) : $value)
            ->all();

        $hasAddress = collect($addressInput)
            ->except('country')
            ->filter()
            ->isNotEmpty();

        if ($hasAddress) {
            $address = $user->shipmentAddress ?: new ShipmentAddress();
            $address->fill([
                'address_line_1' => $addressInput['address_line_1'] ?: 'Not provided',
                'address_line_2' => $addressInput['address_line_2'] ?: null,
                'city' => $addressInput['city'] ?: 'Not provided',
                'state' => $addressInput['state'] ?: 'Not provided',
                'country' => $addressInput['country'] ?: 'India',
                'pincode' => $addressInput['pincode'] ?: '000000',
                'status' => 1,
            ]);
            $address->save();

            $user->update(['shipment_address_id' => $address->id]);
        }

        return redirect()->route('user.profile')->with('success', 'Profile updated successfully.');
    }
}
