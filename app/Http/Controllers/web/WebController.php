<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\CityRoute;
use App\Models\TransportCartItem;
use App\Models\TransportLead;
use App\Models\TransportServicePrice;
use App\Services\GuestCartService;
use App\Services\ShipmentPricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
        $this->syncCitiesFromRoutes();
        $cities = City::where('is_active', true)->orderBy('name')->get();

        return view('web.shipment-add-item', compact('cities'));
    }

    public function saveShipmentItems(Request $request)
    {
        $validated = $request->validate([
            'from_city_id' => ['required', 'exists:cities,id'],
            'to_city_id' => ['required', 'different:from_city_id', 'exists:cities,id'],
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
                'from_city_id' => $validated['from_city_id'],
                'to_city_id' => $validated['to_city_id'],
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
            ->with(['fromCity', 'toCity'])
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
        $leads = TransportLead::with(['fromCity', 'toCity'])
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
            $lead = TransportLead::with(['fromCity', 'toCity', 'user'])
                ->where('tracking_number', $trackingNumber)
                ->when(auth()->check(), function ($query) {
                    $query->where('user_id', auth()->id());
                })
                ->first();
        }

        if (auth()->check() && !$trackingNumber) {
            $userLeads = TransportLead::with(['fromCity', 'toCity'])
                ->where('user_id', auth()->id())
                ->latest()
                ->limit(10)
                ->get();
        }

        return view('web.track-shipment', compact('lead', 'trackingNumber', 'userLeads'));
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

        $cartItems = TransportCartItem::with(['fromCity', 'toCity'])
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
                    'from_city_id' => $item->from_city_id,
                    'to_city_id' => $item->to_city_id,
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
        $item->load(['fromCity', 'toCity']);
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
        if (!$item->fromCity || !$item->toCity) {
            return null;
        }

        return CityRoute::where('is_active', true)
            ->where(function ($query) use ($item) {
                $query->where(function ($inner) use ($item) {
                    $inner->where('from_city', $item->fromCity->name)->where('to_city', $item->toCity->name);
                })->orWhere(function ($inner) use ($item) {
                    $inner->where('from_city', $item->toCity->name)->where('to_city', $item->fromCity->name);
                });
            })
            ->first();
    }

    private function generateTrackingNumber(): string
    {
        do {
            $trackingNumber = 'TL-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (TransportLead::where('tracking_number', $trackingNumber)->exists());

        return $trackingNumber;
    }

    private function syncCitiesFromRoutes(): void
    {
        CityRoute::query()
            ->select('from_city', 'to_city')
            ->get()
            ->each(function (CityRoute $route) {
                City::firstOrCreate(['name' => $route->from_city], ['is_active' => true]);
                City::firstOrCreate(['name' => $route->to_city], ['is_active' => true]);
            });
    }
}
