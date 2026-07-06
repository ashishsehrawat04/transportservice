<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\CityRoute;
use App\Models\ShipmentPayment;
use App\Models\TransportCartItem;
use App\Models\TransportAddress;
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
        $fromCities = $cityRoutes->pluck('from_city')->unique()->values();
        $toCities = $cityRoutes->pluck('to_city')->unique()->values();
        $itemTypes = TransportServicePrice::where('is_active', true)
            ->orderBy('item_type')
            ->pluck('item_type');

        return view('web.shipment-add-item', compact('cityRoutes', 'fromCities', 'toCities', 'itemTypes'));
    }

    public function saveShipmentItems(Request $request)
    {
        $validated = $request->validate([
            'from_city' => ['required', 'string', 'max:255'],
            'to_city' => ['required', 'string', 'max:255'],
            'pickup_address' => ['nullable', 'string', 'max:2000'],
            'delivery_address' => ['nullable', 'string', 'max:2000'],
            'pickup_date' => ['required', 'date'],
            // 'delivery_date' => ['required', 'date', 'after_or_equal:pickup_date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_name' => ['required', 'string', 'max:255'],
            'items.*.item_type' => [
                'required',
                'string',
                'max:255',
                Rule::exists('transport_service_prices', 'item_type')->where('is_active', true),
            ],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.length_cm' => ['nullable', 'numeric', 'min:0'],
            'items.*.width_cm' => ['nullable', 'numeric', 'min:0'],
            'items.*.height_cm' => ['required', 'numeric', 'min:0.01'],
            'items.*.weight_kg' => ['required', 'numeric', 'min:0.01'],
        ]);

        $cityRoute = CityRoute::where('is_active', true)
            ->where('from_city', $validated['from_city'])
            ->where('to_city', $validated['to_city'])
            ->first();

        if (!$cityRoute) {
            return back()
                ->withErrors(['to_city' => 'Please select a valid active city route.'])
                ->withInput();
        }

        foreach ($validated['items'] as $item) {
            $cartItem = TransportCartItem::create([
                'user_id' => auth()->id(),
                'guest_id' => auth()->check() ? null : $this->guestCartService->guestId(),
                'city_route_id' => $cityRoute->id,
                'item_name' => $item['item_name'],
                'item_type' => $item['item_type'] ?? null,
                'quantity' => $item['quantity'],
                'length_cm' => $item['length_cm'] ?? null,
                'width_cm' => $item['width_cm'] ?? null,
                'height_cm' => $item['height_cm'],
                'weight_kg' => $item['weight_kg'],
                'pickup_date' => $validated['pickup_date'],
                // 'delivery_date' => $validated['delivery_date'],
            ]);

            $this->updateCartEstimate($cartItem);

            if (auth()->check() && ($validated['pickup_address'] || $validated['delivery_address'])) {
                TransportAddress::create([
                    'user_id' => auth()->id(),
                    'item_id' => $cartItem->id,
                    'pickup_address' => $validated['pickup_address'] ?? null,
                    'delivery_address' => $validated['delivery_address'] ?? null,
                    'status' => 1,
                ]);
            }
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
            $itemPrice = $this->priceForItemType($item->item_type);
            $priceError = null;

            if (!$itemPrice) {
                $priceError = 'Price not set';
                $item->price_error = $priceError;
                return;
            }

            $route = $this->findRoute($item);

            if (!$route) {
                $priceError = 'Route not found';
                $item->price_error = $priceError;
                return;
            }

            $breakdown = $this->pricingService->calculateCartItem($item, $itemPrice, $route);
            $calculatedPrice = $breakdown['total_payment'];

            if (
                $item->estimated_total !== $calculatedPrice ||
                $item->charge_basis !== $breakdown['charge_basis'] ||
                $item->charge_weight_kg !== $breakdown['charge_weight_kg'] ||
                $item->volumetric_weight_kg !== $breakdown['volumetric_weight_kg']
            ) {
                $item->update([
                    'estimated_total' => $calculatedPrice,
                    'charge_basis' => $breakdown['charge_basis'],
                    'charge_weight_kg' => $breakdown['charge_weight_kg'],
                    'volumetric_weight_kg' => $breakdown['volumetric_weight_kg'],
                ]);
            }

            $item->price_breakdown = $breakdown;
            $item->calculated_price = $calculatedPrice;
            $cartTotal += $calculatedPrice;
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

    public function editShipmentCartItem($id)
    {
        $cartItem = $this->cartQuery()
            ->with(['cityRoute', 'transportAddress'])
            ->findOrFail($id);
        $cityRoutes = CityRoute::where('is_active', true)
            ->orderBy('from_city')
            ->orderBy('to_city')
            ->get();
        $fromCities = $cityRoutes->pluck('from_city')->unique()->values();
        $toCities = $cityRoutes->pluck('to_city')->unique()->values();
        $itemTypes = TransportServicePrice::where('is_active', true)
            ->orderBy('item_type')
            ->pluck('item_type');

        return view('web.shipment-edit-cart-item', compact('cartItem', 'cityRoutes', 'fromCities', 'toCities', 'itemTypes'));
    }

    public function updateShipmentCartItem(Request $request, $id)
    {
        $cartItem = $this->cartQuery()
            ->with('transportAddress')
            ->findOrFail($id);

        $validated = $request->validate([
            'from_city' => ['required', 'string', 'max:255'],
            'to_city' => ['required', 'string', 'max:255'],
            'pickup_address' => ['nullable', 'string', 'max:2000'],
            'delivery_address' => ['nullable', 'string', 'max:2000'],
            'pickup_date' => ['required', 'date'],
            'item_name' => ['required', 'string', 'max:255'],
            'item_type' => [
                'required',
                'string',
                'max:255',
                Rule::exists('transport_service_prices', 'item_type')->where('is_active', true),
            ],
            'quantity' => ['required', 'integer', 'min:1'],
            'length_cm' => ['nullable', 'numeric', 'min:0'],
            'width_cm' => ['nullable', 'numeric', 'min:0'],
            'height_cm' => ['required', 'numeric', 'min:0.01'],
            'weight_kg' => ['required', 'numeric', 'min:0.01'],
        ]);

        $cityRoute = CityRoute::where('is_active', true)
            ->where('from_city', $validated['from_city'])
            ->where('to_city', $validated['to_city'])
            ->first();

        if (!$cityRoute) {
            return back()
                ->withErrors(['to_city' => 'Please select a valid active city route.'])
                ->withInput();
        }

        $cartItem->update([
            'city_route_id' => $cityRoute->id,
            'item_name' => $validated['item_name'],
            'item_type' => $validated['item_type'],
            'quantity' => $validated['quantity'],
            'length_cm' => $validated['length_cm'] ?? null,
            'width_cm' => $validated['width_cm'] ?? null,
            'height_cm' => $validated['height_cm'],
            'weight_kg' => $validated['weight_kg'],
            'pickup_date' => $validated['pickup_date'],
        ]);

        $this->updateCartEstimate($cartItem);

        if (auth()->check()) {
            $hasAddress = ($validated['pickup_address'] ?? null) || ($validated['delivery_address'] ?? null);

            if ($hasAddress) {
                TransportAddress::updateOrCreate(
                    ['item_id' => $cartItem->id],
                    [
                        'user_id' => auth()->id(),
                        'pickup_address' => $validated['pickup_address'] ?? null,
                        'delivery_address' => $validated['delivery_address'] ?? null,
                        'status' => 1,
                    ]
                );
            } elseif ($cartItem->transportAddress) {
                $cartItem->transportAddress->delete();
            }
        }

        return redirect()->route('shipment.cart')->with('success', 'Cart item updated and price recalculated successfully.');
    }

    public function checkoutShipmentCart()
    {
        $price = TransportServicePrice::where('is_active', true)->orderBy('id')->first();

        if (!$price) {
            return back()->with('error', 'Please contact admin. Transport price is not set.');
        }

        $cartItems = TransportCartItem::with('cityRoute')
            ->with('transportAddress')
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
            $cartItems->groupBy(fn (TransportCartItem $item) => $this->shipmentGroupKey($item))
                ->each(function ($shipmentItems) {
                    $firstItem = $shipmentItems->first();
                    $price = $this->priceForItemType($firstItem->item_type);
                    $breakdowns = $shipmentItems->map(function (TransportCartItem $item) {
                        return $this->pricingService->calculateCartItem(
                            $item,
                            $this->priceForItemType($item->item_type),
                            $this->findRoute($item)
                        );
                    });

                    $transportLead = TransportLead::create(array_merge([
                        'user_id' => auth()->id(),
                        'item_name' => $this->shipmentItemName($shipmentItems),
                        'item_type' => $shipmentItems->pluck('item_type')->filter()->unique()->count() === 1
                            ? $shipmentItems->first()->item_type
                            : 'multiple',
                        'quantity' => $shipmentItems->sum('quantity'),
                        'length_cm' => $shipmentItems->max('length_cm'),
                        'width_cm' => $shipmentItems->max('width_cm'),
                        'height_cm' => $shipmentItems->max('height_cm'),
                        'weight_kg' => $shipmentItems->sum('weight_kg'),
                        'volume_cft' => $breakdowns->sum('volume_cft'),
                        'city_route_id' => $firstItem->city_route_id,
                        'requested_pickup_date' => $firstItem->pickup_date,
                        'expected_delivery_date' => $firstItem->delivery_date,
                        'admin_status' => 'pending',
                        'user_status' => 'pending',
                        'payment_status' => 'unpaid',
                        'tracking_number' => $this->generateTrackingNumber(),
                    ], $this->aggregateShipmentBreakdown($breakdowns, $price)));

                    $quote = TransportQuote::syncFromLead($transportLead->fresh(['user', 'cityRoute', 'latestPayment']));
                    $quoteData = $quote->quote_data ?: [];
                    $quoteData['shipment_items'] = $shipmentItems->map(fn (TransportCartItem $item) => [
                        'item_name' => $item->item_name,
                        'item_type' => $item->item_type,
                        'quantity' => $item->quantity,
                        'length_cm' => $item->length_cm,
                        'width_cm' => $item->width_cm,
                        'height_cm' => $item->height_cm,
                        'weight_kg' => $item->weight_kg,
                        'estimated_total' => $item->estimated_total,
                    ])->values()->all();

                    $address = $shipmentItems->first(fn (TransportCartItem $item) => $item->transportAddress);
                    if ($address?->transportAddress) {
                        $quoteData['transport_address'] = [
                            'pickup_address' => $address->transportAddress->pickup_address,
                            'delivery_address' => $address->transportAddress->delivery_address,
                            'status' => $address->transportAddress->status,
                        ];
                    }

                    $quote->update(['quote_data' => $quoteData]);
                });

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

        $breakdown = $this->pricingService->calculateCartItem($item, $price, $route);

        $item->update([
            'estimated_total' => $breakdown['total_payment'],
            'charge_basis' => $breakdown['charge_basis'],
            'charge_weight_kg' => $breakdown['charge_weight_kg'],
            'volumetric_weight_kg' => $breakdown['volumetric_weight_kg'],
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

    private function shipmentGroupKey(TransportCartItem $item): string
    {
        return implode('|', [
            $item->city_route_id,
            optional($item->pickup_date)->format('Y-m-d'),
            optional($item->delivery_date)->format('Y-m-d'),
        ]);
    }

    private function shipmentItemName($items): string
    {
        $names = $items->pluck('item_name')->filter()->unique()->values();

        if ($names->count() === 1) {
            return (string) $names->first();
        }

        return Str::limit($names->join(', '), 250, '...');
    }

    private function aggregateShipmentBreakdown($breakdowns, ?TransportServicePrice $price): array
    {
        $calculationTypes = $breakdowns->pluck('calculation_type')->filter()->unique();

        return [
            'distance_km' => $breakdowns->max('distance_km'),
            'calculation_type' => $calculationTypes->count() === 1 ? $calculationTypes->first() : 'mixed',
            'base_price' => $breakdowns->sum('base_price'),
            'weight_charge' => $breakdowns->sum('weight_charge'),
            'volume_charge' => $breakdowns->sum('volume_charge'),
            'distance_charge' => $breakdowns->sum('distance_charge'),
            'multiplier_applied' => $breakdowns->max('multiplier_applied') ?: ($price?->multiplier ?: 1),
            'subtotal' => $breakdowns->sum('subtotal'),
            'tax_amount' => $breakdowns->sum('tax_amount'),
            'discount_amount' => $breakdowns->sum('discount_amount'),
            'total_payment' => $breakdowns->sum('total_payment'),
        ];
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
            'address_line_1' => $validated['address_line_1'] ?? null,
            'address_line_2' => $validated['address_line_2'] ?? null,
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
            'country' => $validated['country'] ?? 'India',
            'pincode' => $validated['pincode'] ?? null,
        ]);

        return redirect()->route('user.profile')->with('success', 'Profile updated successfully.');
    }
}
