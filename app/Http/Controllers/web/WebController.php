<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\CityRoute;
use App\Models\PricingSetting;
use App\Models\ShipmentPayment;
use App\Models\TransportCartItem;
use App\Models\TransportAddress;
use App\Models\TransportLead;
use App\Models\TransportQuote;
use App\Models\TransportServicePrice;
use App\Models\WarehouseLead;
use App\Services\GuestCartService;
use App\Services\ShipmentInvoicePdfService;
use App\Services\ShipmentPricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class WebController extends Controller
{
    /**
     * Plain-language labels for shipment item fields, used to turn
     * Laravel's default validation messages (e.g. "The height_cm field is
     * required.") into wording a non-technical customer understands.
     */
    private const ITEM_FIELD_LABELS = [
        'item_name' => 'item name',
        'item_type' => 'item type',
        'quantity' => 'quantity',
        'length_cm' => 'length',
        'width_cm' => 'width',
        'height_cm' => 'height',
        'weight_kg' => 'weight',
    ];

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
        ], [], $this->itemFieldAttributes($request->input('items', [])));

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

            if (auth()->check() && (($validated['pickup_address'] ?? null) || ($validated['delivery_address'] ?? null))) {
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

    public function estimateShipmentItems(Request $request)
    {
        try {
            $validated = $request->validate([
                'from_city' => ['required', 'string', 'max:255'],
                'to_city' => ['required', 'string', 'max:255'],
                'items' => ['required', 'array', 'min:1', 'max:100'],
                'items.*.quantity' => ['required', 'integer', 'min:1'],
                'items.*.length_cm' => ['nullable', 'numeric', 'min:0'],
                'items.*.width_cm' => ['nullable', 'numeric', 'min:0'],
                'items.*.height_cm' => ['required', 'numeric', 'min:0.01'],
                'items.*.weight_kg' => ['required', 'numeric', 'min:0.01'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $this->friendlyEstimateValidationMessage($e),
            ], 422);
        }

        $route = CityRoute::where('is_active', true)
            ->where('from_city', $validated['from_city'])
            ->where('to_city', $validated['to_city'])
            ->first();

        if (!$route) {
            return response()->json([
                'success' => false,
                'message' => 'We don\'t have pricing set up for this route yet. Please pick a different pickup or delivery city.',
            ]);
        }

        $minCharge = round((float) $route->min_charge, 2);
        $itemBreakdowns = [];
        $itemsTotal = 0.0;

        foreach ($validated['items'] as $index => $item) {
            $breakdown = $this->pricingService->calculateFromDimensions($item, $route);
            $itemsTotal += $breakdown['total_payment'];

            $itemBreakdowns[] = [
                'index' => $index,
                'charge_basis' => $breakdown['charge_basis'],
                'charge_weight_kg' => $breakdown['charge_weight_kg'],
                'volumetric_weight_kg' => $breakdown['volumetric_weight_kg'],
                'volume_cft' => $breakdown['volume_cft'],
                'item_charge' => $breakdown['total_payment'],
            ];
        }

        $itemsTotal = round($itemsTotal, 2);
        $grandTotal = $this->pricingService->floorShipmentTotal($itemsTotal, $route);

        return response()->json([
            'success' => true,
            'route' => [
                'from_city' => $route->from_city,
                'to_city' => $route->to_city,
                'transit_days' => $route->transit_days,
            ],
            // Min charge is a shipment-level floor applied once to the
            // combined items_total below (see grand_total), not per item.
            'min_charge' => $minCharge,
            'items' => $itemBreakdowns,
            'items_total' => $itemsTotal,
            'grand_total' => $grandTotal,
        ]);
    }

    /**
     * Turns Laravel's raw validation errors (e.g. "The items.0.height_cm
     * field is required.") into a plain-language message a non-technical
     * customer can act on, naming the item number and field in the UI.
     */
    private function friendlyEstimateValidationMessage(ValidationException $e): string
    {
        $fieldLabels = [
            'quantity' => 'quantity',
            'length_cm' => 'length',
            'width_cm' => 'width',
            'height_cm' => 'height',
            'weight_kg' => 'weight',
        ];

        $firstField = array_key_first($e->errors());
        $firstMessage = $e->errors()[$firstField][0];

        if (preg_match('/^items\.(\d+)\.(\w+)$/', $firstField, $matches)) {
            $itemNumber = ((int) $matches[1]) + 1;
            $label = $fieldLabels[$matches[2]] ?? str_replace('_', ' ', $matches[2]);

            return str_contains($firstMessage, 'required')
                ? "Please enter the {$label} for item {$itemNumber}."
                : "Please double-check the {$label} for item {$itemNumber} — the value entered isn't valid.";
        }

        if (in_array($firstField, ['from_city', 'to_city'], true)) {
            return 'Please select both a pickup city and a delivery city.';
        }

        if ($firstField === 'items') {
            return 'Please add at least one item before calculating an estimate.';
        }

        return 'Some item details are missing or invalid. Please check the form and try again.';
    }

    /**
     * Builds "items.0.height_cm" => "item 1 height" style attribute labels
     * so Laravel's validation error list reads in plain language instead of
     * raw field/array-index names.
     */
    private function itemFieldAttributes(array $items): array
    {
        $attributes = [];

        foreach (array_keys($items) as $index) {
            $itemNumber = ((int) $index) + 1;

            foreach (self::ITEM_FIELD_LABELS as $field => $label) {
                $attributes["items.{$index}.{$field}"] = "item {$itemNumber} {$label}";
            }
        }

        return $attributes;
    }

    /**
     * Cart is a shared page across Shipment and Warehouse — both
     * `shipment.cart` and `warehouse.cart` render this same combined view
     * so a customer sees (and can act on) both kinds of requests together.
     */
    public function shipmentCart()
    {
        $shipment = $this->shipmentCartData();
        $warehouse = app(WarehouseController::class)->warehouseCartData();

        return view('web.cart', [
            'shipmentCartItems' => $shipment['cartItems'],
            'shipmentCartTotal' => $shipment['cartTotal'],
            'warehouseCartItems' => $warehouse['cartItems'],
            'warehouseCartTotal' => $warehouse['cartTotal'],
            'activeCartTab' => request()->routeIs('warehouse.cart') ? 'warehouse' : 'shipment',
        ]);
    }

    public function shipmentCartData(): array
    {
        $cartItems = $this->cartQuery()
            ->with('cityRoute')
            ->latest()
            ->get();

        $cartItems->each(function (TransportCartItem $item) {
            if ($item->booking_status) {
                // Already submitted to a transport lead and awaiting admin approval —
                // show the frozen numbers as-is instead of recalculating live pricing.
                $item->calculated_price = $item->estimated_total;
                return;
            }

            $route = $this->findRoute($item);

            if (!$route) {
                $item->price_error = 'This route is no longer available. Please edit this item and choose a different pickup or delivery city.';
                return;
            }

            $breakdown = $this->pricingService->calculateCartItem($item, $route);
            $calculatedPrice = $breakdown['total_payment'];

            if (
                $item->estimated_total !== $calculatedPrice ||
                $item->charge_basis !== $breakdown['charge_basis'] ||
                $item->charge_weight_kg !== $breakdown['charge_weight_kg']
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
        });

        $cartTotal = $cartItems
            ->filter(fn (TransportCartItem $item) => !$item->price_error && !$item->booking_status)
            ->groupBy(fn (TransportCartItem $item) => $this->shipmentGroupKey($item))
            ->sum(function ($shipmentItems) {
                $route = $shipmentItems->first()->cityRoute;
                $rawTotal = $shipmentItems->sum('calculated_price');

                return $route ? $this->pricingService->floorShipmentTotal($rawTotal, $route) : $rawTotal;
            });

        return ['cartItems' => $cartItems, 'cartTotal' => $cartTotal];
    }

    public function shipmentLeads()
    {
        $leads = TransportLead::with('cityRoute')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('web.shipment-leads', compact('leads'));
    }

    /**
     * Tracking is a shared page across Shipment and Warehouse — both
     * `shipment.track` and `warehouse.track` render this same combined
     * lookup so one tracking number search covers either kind of request.
     */
    public function trackShipment(Request $request)
    {
        $trackingNumber = $request->query('tracking_number');
        $lead = null;
        $leadType = null;
        $userLeads = collect();

        if ($trackingNumber) {
            $lead = TransportLead::with(['cityRoute', 'user', 'latestPayment'])
                ->where('tracking_number', $trackingNumber)
                ->when(auth()->check(), function ($query) {
                    $query->where('user_id', auth()->id());
                })
                ->first();

            if ($lead) {
                $leadType = 'shipment';
            } else {
                $lead = WarehouseLead::with(['warehouse', 'user', 'latestPayment'])
                    ->where('tracking_number', $trackingNumber)
                    ->when(auth()->check(), function ($query) {
                        $query->where('user_id', auth()->id());
                    })
                    ->first();

                if ($lead) {
                    $leadType = 'warehouse';
                }
            }
        }

        if (auth()->check() && !$trackingNumber) {
            $shipments = TransportLead::with('cityRoute')
                ->where('user_id', auth()->id())
                ->latest()
                ->limit(10)
                ->get()
                ->map(fn (TransportLead $item) => (object) ['type' => 'shipment', 'lead' => $item]);

            $warehouses = WarehouseLead::with('warehouse')
                ->where('user_id', auth()->id())
                ->latest()
                ->limit(10)
                ->get()
                ->map(fn (WarehouseLead $item) => (object) ['type' => 'warehouse', 'lead' => $item]);

            $userLeads = $shipments->concat($warehouses)
                ->sortByDesc(fn ($entry) => $entry->lead->created_at)
                ->values()
                ->take(10);
        }

        return view('web.track', compact('lead', 'leadType', 'trackingNumber', 'userLeads'));
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

        if ($cartItem->booking_status) {
            return redirect()->route('shipment.cart')->with('error', 'This item is already submitted and awaiting admin approval.');
        }

        $cartItem->delete();

        return redirect()->route('shipment.cart')->with('success', 'Item removed from cart');
    }

    public function cancelShipment($leadId)
    {
        $lead = TransportLead::where('id', $leadId)
            ->where('user_id', auth()->id())
            ->whereIn('admin_status', ['pending', 'reviewed'])
            ->first();

        if (!$lead) {
            return redirect()->route('shipment.cart')->with('error', 'This shipment can no longer be cancelled.');
        }

        DB::transaction(function () use ($lead) {
            $lead->update([
                'admin_status' => 'cancelled',
                'user_status' => 'cancelled',
            ]);

            TransportCartItem::where('transport_lead_id', $lead->id)->delete();

            TransportQuote::syncFromLead($lead->fresh(['user', 'cityRoute', 'latestPayment']));
        });

        return redirect()->route('shipment.cart')->with('success', 'Shipment cancelled.');
    }

    public function editShipmentCartItem($id)
    {
        $cartItem = $this->cartQuery()
            ->with(['cityRoute', 'transportAddress'])
            ->findOrFail($id);

        if ($cartItem->booking_status) {
            return redirect()->route('shipment.cart')->with('error', 'This item is already submitted and awaiting admin approval.');
        }

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

        if ($cartItem->booking_status) {
            return redirect()->route('shipment.cart')->with('error', 'This item is already submitted and awaiting admin approval.');
        }

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
        ], [], self::ITEM_FIELD_LABELS);

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
        $cartItems = TransportCartItem::with('cityRoute')
            ->with('transportAddress')
            ->where('user_id', auth()->id())
            ->whereNull('booking_status')
            ->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Your cart is empty.');
        }

        foreach ($cartItems as $item) {
            if (!$this->findRoute($item)) {
                return back()->with('error', 'Please add an active city route for all cart items before saving to leads.');
            }
        }

        DB::transaction(function () use ($cartItems) {
            $cartItems->groupBy(fn (TransportCartItem $item) => $this->shipmentGroupKey($item))
                ->each(function ($shipmentItems) {
                    $route = $this->findRoute($shipmentItems->first());
                    $transportLead = $this->createLeadFromShipmentItems($shipmentItems, $route);

                    TransportCartItem::whereIn('id', $shipmentItems->pluck('id'))->update([
                        'transport_lead_id' => $transportLead->id,
                        'booking_status' => 'pending',
                    ]);
                });
        });

        return redirect()->route('shipment.cart')->with('success', 'Cart saved to transport leads successfully.');
    }

    public function checkoutOneShipment(Request $request)
    {
        $validated = $request->validate([
            'item_ids' => ['required', 'array', 'min:1'],
            'item_ids.*' => ['integer'],
        ]);

        $shipmentItems = TransportCartItem::with(['cityRoute', 'transportAddress'])
            ->where('user_id', auth()->id())
            ->whereNull('booking_status')
            ->whereIn('id', $validated['item_ids'])
            ->get();

        if ($shipmentItems->isEmpty()) {
            return redirect()->route('shipment.cart')->with('error', 'These items are no longer in your cart.');
        }

        $route = $this->findRoute($shipmentItems->first());

        if (!$route) {
            return redirect()->route('shipment.cart')->with('error', 'Please add an active city route for this shipment before saving to leads.');
        }

        DB::transaction(function () use ($shipmentItems, $route) {
            $transportLead = $this->createLeadFromShipmentItems($shipmentItems, $route);

            TransportCartItem::whereIn('id', $shipmentItems->pluck('id'))->update([
                'transport_lead_id' => $transportLead->id,
                'booking_status' => 'pending',
            ]);
        });

        return redirect()->route('shipment.cart')->with('success', 'Shipment saved to transport leads successfully.');
    }

    public function cancelFreshShipment(Request $request)
    {
        $validated = $request->validate([
            'item_ids' => ['required', 'array', 'min:1'],
            'item_ids.*' => ['integer'],
        ]);

        $shipmentItems = TransportCartItem::with(['cityRoute', 'transportAddress'])
            ->where('user_id', auth()->id())
            ->whereNull('booking_status')
            ->whereIn('id', $validated['item_ids'])
            ->get();

        if ($shipmentItems->isEmpty()) {
            return redirect()->route('shipment.cart')->with('error', 'These items are no longer in your cart.');
        }

        $route = $this->findRoute($shipmentItems->first());

        DB::transaction(function () use ($shipmentItems, $route) {
            if ($route) {
                $this->createLeadFromShipmentItems($shipmentItems, $route, [
                    'admin_status' => 'cancelled',
                    'user_status' => 'cancelled',
                ]);
            }

            TransportCartItem::whereIn('id', $shipmentItems->pluck('id'))->delete();
        });

        return redirect()->route('shipment.cart')->with('success', 'Shipment cancelled.');
    }

    private function createLeadFromShipmentItems($shipmentItems, CityRoute $route, array $statusOverrides = []): TransportLead
    {
        $firstItem = $shipmentItems->first();
        $breakdowns = $shipmentItems->map(function (TransportCartItem $item) use ($route) {
            return $this->pricingService->calculateCartItem($item, $route);
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
        ], $this->aggregateShipmentBreakdown($breakdowns, $route), $statusOverrides));

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
            'charge_basis' => $item->charge_basis,
            'charge_weight_kg' => $item->charge_weight_kg,
            'volumetric_weight_kg' => $item->volumetric_weight_kg,
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

        return $transportLead;
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
        $item->load('cityRoute');
        $route = $this->findRoute($item);

        if (!$route) {
            return;
        }

        $breakdown = $this->pricingService->calculateCartItem($item, $route);

        $item->update([
            'estimated_total' => $breakdown['total_payment'],
            'charge_basis' => $breakdown['charge_basis'],
            'charge_weight_kg' => $breakdown['charge_weight_kg'],
            'volumetric_weight_kg' => 0,
        ]);
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

    private function aggregateShipmentBreakdown($breakdowns, CityRoute $route): array
    {
        $calculationTypes = $breakdowns->pluck('calculation_type')->filter()->unique();
        $minCharge = round((float) $route->min_charge, 2);
        $itemsTotal = $breakdowns->sum('total_payment');
        $discountAmount = $breakdowns->sum('discount_amount');

        // min_charge is a shipment-level floor applied once to the
        // combined item total, not per individual item.
        $billableSubtotal = $this->pricingService->floorShipmentTotal($itemsTotal, $route);
        $taxAmount = round($billableSubtotal * PricingSetting::gstPercent() / 100, 2);

        return [
            'calculation_type' => $calculationTypes->count() === 1 ? $calculationTypes->first() : 'mixed',
            'base_price' => $minCharge,
            'weight_charge' => $breakdowns->sum('weight_charge'),
            'volume_charge' => $breakdowns->sum('volume_charge'),
            'distance_charge' => $breakdowns->sum('distance_charge'),
            'multiplier_applied' => 1,
            'subtotal' => $billableSubtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total_payment' => max(0.0, round($billableSubtotal + $taxAmount - $discountAmount, 2)),
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
