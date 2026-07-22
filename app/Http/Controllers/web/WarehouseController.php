<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\WarehouseAddress;
use App\Models\WarehouseCartItem;
use App\Models\WarehouseLead;
use App\Models\WarehousePayment;
use App\Models\WarehouseQuote;
use App\Services\GuestCartService;
use App\Services\WarehouseInvoicePdfService;
use App\Services\WarehousePricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class WarehouseController extends Controller
{
    /**
     * Plain-language labels for warehouse item fields, used to turn
     * Laravel's default validation messages into wording a non-technical
     * customer understands.
     */
    private const ITEM_FIELD_LABELS = [
        'item_name' => 'item name',
        'item_type' => 'item type',
        'quantity' => 'quantity',
        'length_cm' => 'length',
        'width_cm' => 'width',
        'height_cm' => 'height',
        'weight_kg' => 'weight',
        'storage_days' => 'storage days',
    ];

    public function __construct(
        private GuestCartService $guestCartService,
        private WarehousePricingService $pricingService
    ) {
    }

    public function addWarehouseItem()
    {
        $this->guestCartService->guestId();
        $warehouses = Warehouse::where('is_active', true)
            ->orderBy('city')
            ->orderBy('name')
            ->get();

        return view('web.warehouse.add-item', compact('warehouses'));
    }

    public function saveWarehouseItems(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'pickup_address' => ['nullable', 'string', 'max:2000'],
            'pickup_date' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_name' => ['required', 'string', 'max:255'],
            'items.*.item_type' => ['nullable', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.length_cm' => ['nullable', 'numeric', 'min:0'],
            'items.*.width_cm' => ['nullable', 'numeric', 'min:0'],
            'items.*.height_cm' => ['required', 'numeric', 'min:0.01'],
            'items.*.weight_kg' => ['required', 'numeric', 'min:0.01'],
            'items.*.storage_days' => ['required', 'integer', 'min:1'],
        ], [], $this->itemFieldAttributes($request->input('items', [])));

        $warehouse = Warehouse::where('is_active', true)->find($validated['warehouse_id']);

        if (!$warehouse) {
            return back()
                ->withErrors(['warehouse_id' => 'Please select a valid active warehouse.'])
                ->withInput();
        }

        foreach ($validated['items'] as $item) {
            $cartItem = WarehouseCartItem::create([
                'user_id' => auth()->id(),
                'guest_id' => auth()->check() ? null : $this->guestCartService->guestId(),
                'warehouse_id' => $warehouse->id,
                'item_name' => $item['item_name'],
                'item_type' => $item['item_type'] ?? null,
                'quantity' => $item['quantity'],
                'length_cm' => $item['length_cm'] ?? null,
                'width_cm' => $item['width_cm'] ?? null,
                'height_cm' => $item['height_cm'],
                'weight_kg' => $item['weight_kg'],
                'pickup_date' => $validated['pickup_date'],
                'storage_days' => $item['storage_days'],
            ]);

            $this->updateCartEstimate($cartItem);

            if (auth()->check() && ($validated['pickup_address'] ?? null)) {
                WarehouseAddress::create([
                    'user_id' => auth()->id(),
                    'item_id' => $cartItem->id,
                    'pickup_address' => $validated['pickup_address'],
                    'status' => 1,
                ]);
            }
        }

        return redirect()->route('warehouse.cart')->with('success', 'Items added to storage cart successfully');
    }

    public function estimateWarehouseItems(Request $request)
    {
        try {
            $validated = $request->validate([
                'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
                'items' => ['required', 'array', 'min:1', 'max:100'],
                'items.*.quantity' => ['required', 'integer', 'min:1'],
                'items.*.length_cm' => ['nullable', 'numeric', 'min:0'],
                'items.*.width_cm' => ['nullable', 'numeric', 'min:0'],
                'items.*.height_cm' => ['required', 'numeric', 'min:0.01'],
                'items.*.weight_kg' => ['required', 'numeric', 'min:0.01'],
                'items.*.storage_days' => ['required', 'integer', 'min:1'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $this->friendlyEstimateValidationMessage($e),
            ], 422);
        }

        $warehouse = Warehouse::where('is_active', true)->find($validated['warehouse_id']);

        if (!$warehouse) {
            return response()->json([
                'success' => false,
                'message' => 'We don\'t have this warehouse available right now. Please pick a different warehouse.',
            ]);
        }

        $minCharge = round((float) $warehouse->min_charge, 2);
        $itemBreakdowns = [];
        $itemsTotal = 0.0;

        foreach ($validated['items'] as $index => $item) {
            $breakdown = $this->pricingService->calculateFromDimensions($item, $warehouse);
            $itemsTotal += $breakdown['total_payment'];

            $itemBreakdowns[] = [
                'index' => $index,
                'charge_basis' => $breakdown['charge_basis'],
                'charge_weight_kg' => $breakdown['charge_weight_kg'],
                'volumetric_weight_kg' => $breakdown['volumetric_weight_kg'],
                'volume_cft' => $breakdown['volume_cft'],
                'storage_days' => $breakdown['storage_days'],
                'item_charge' => $breakdown['total_payment'],
            ];
        }

        $itemsTotal = round($itemsTotal, 2);
        $grandTotal = $this->pricingService->floorShipmentTotal($itemsTotal, $warehouse);

        return response()->json([
            'success' => true,
            'warehouse' => [
                'name' => $warehouse->name,
                'city' => $warehouse->city,
                'price_per_day_per_kg' => (float) $warehouse->price_per_day_per_kg,
            ],
            'min_charge' => $minCharge,
            'items' => $itemBreakdowns,
            'items_total' => $itemsTotal,
            'grand_total' => $grandTotal,
        ]);
    }

    /**
     * Turns Laravel's raw validation errors into a plain-language message a
     * non-technical customer can act on, naming the item number and field.
     */
    private function friendlyEstimateValidationMessage(ValidationException $e): string
    {
        $fieldLabels = [
            'quantity' => 'quantity',
            'length_cm' => 'length',
            'width_cm' => 'width',
            'height_cm' => 'height',
            'weight_kg' => 'weight',
            'storage_days' => 'storage days',
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

        if ($firstField === 'warehouse_id') {
            return 'Please select a warehouse.';
        }

        if ($firstField === 'items') {
            return 'Please add at least one item before calculating an estimate.';
        }

        return 'Some item details are missing or invalid. Please check the form and try again.';
    }

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
     * Cart is a shared page across Shipment and Warehouse — delegate to
     * WebController::shipmentCart(), which renders both datasets together.
     */
    public function warehouseCart()
    {
        return app(WebController::class)->shipmentCart();
    }

    public function warehouseCartData(): array
    {
        $cartItems = $this->cartQuery()
            ->with('warehouse')
            ->latest()
            ->get();

        $cartItems->each(function (WarehouseCartItem $item) {
            if ($item->booking_status) {
                // Already submitted to a warehouse lead and awaiting admin approval —
                // show the frozen numbers as-is instead of recalculating live pricing.
                $item->calculated_price = $item->estimated_total;
                return;
            }

            $warehouse = $this->findWarehouse($item);

            if (!$warehouse) {
                $item->price_error = 'This warehouse is no longer available. Please edit this item and choose a different warehouse.';
                return;
            }

            $breakdown = $this->pricingService->calculateCartItem($item, $warehouse);
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
            ->filter(fn (WarehouseCartItem $item) => !$item->price_error && !$item->booking_status)
            ->groupBy(fn (WarehouseCartItem $item) => $this->warehouseGroupKey($item))
            ->sum(function ($warehouseItems) {
                $warehouse = $warehouseItems->first()->warehouse;
                $rawTotal = $warehouseItems->sum('calculated_price');

                return $warehouse ? $this->pricingService->floorShipmentTotal($rawTotal, $warehouse) : $rawTotal;
            });

        return ['cartItems' => $cartItems, 'cartTotal' => $cartTotal];
    }

    public function warehouseLeads()
    {
        $leads = WarehouseLead::with('warehouse')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('web.warehouse.leads', compact('leads'));
    }

    /**
     * Tracking is a shared page across Shipment and Warehouse — delegate to
     * WebController::trackShipment(), which looks up either kind of request.
     */
    public function trackWarehouse(Request $request)
    {
        return app(WebController::class)->trackShipment($request);
    }

    public function downloadWarehouseInvoice(string $trackingNumber, WarehouseInvoicePdfService $invoicePdfService)
    {
        $lead = WarehouseLead::with(['warehouse', 'user', 'latestPayment'])
            ->where('tracking_number', $trackingNumber)
            ->when(auth()->check(), function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->firstOrFail();

        if ($lead->admin_status !== 'delivered') {
            return redirect()
                ->route('warehouse.track', ['tracking_number' => $lead->tracking_number])
                ->with('error', 'Invoice will be available after admin marks the storage request as stored.');
        }

        $payment = $lead->latestPayment ?: $this->createInvoicePayment($lead);
        WarehouseQuote::syncFromLead($lead, $payment->invoice_number);
        $fileName = ($payment->invoice_number ?: $lead->tracking_number) . '.pdf';

        return response($invoicePdfService->output($lead, $payment), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    public function deleteWarehouseCartItem($id)
    {
        $cartItem = $this->cartQuery()->findOrFail($id);

        if ($cartItem->booking_status) {
            return redirect()->route('warehouse.cart')->with('error', 'This item is already submitted and awaiting admin approval.');
        }

        $cartItem->delete();

        return redirect()->route('warehouse.cart')->with('success', 'Item removed from cart');
    }

    public function cancelWarehouse($leadId)
    {
        $lead = WarehouseLead::where('id', $leadId)
            ->where('user_id', auth()->id())
            ->whereIn('admin_status', ['pending', 'reviewed'])
            ->first();

        if (!$lead) {
            return redirect()->route('warehouse.cart')->with('error', 'This storage request can no longer be cancelled.');
        }

        DB::transaction(function () use ($lead) {
            $lead->update([
                'admin_status' => 'cancelled',
                'user_status' => 'cancelled',
            ]);

            WarehouseCartItem::where('warehouse_lead_id', $lead->id)->delete();
        });

        return redirect()->route('warehouse.cart')->with('success', 'Storage request cancelled.');
    }

    public function editWarehouseCartItem($id)
    {
        $cartItem = $this->cartQuery()
            ->with(['warehouse', 'warehouseAddress'])
            ->findOrFail($id);

        if ($cartItem->booking_status) {
            return redirect()->route('warehouse.cart')->with('error', 'This item is already submitted and awaiting admin approval.');
        }

        $warehouses = Warehouse::where('is_active', true)
            ->orderBy('city')
            ->orderBy('name')
            ->get();

        return view('web.warehouse.edit-cart-item', compact('cartItem', 'warehouses'));
    }

    public function updateWarehouseCartItem(Request $request, $id)
    {
        $cartItem = $this->cartQuery()
            ->with('warehouseAddress')
            ->findOrFail($id);

        if ($cartItem->booking_status) {
            return redirect()->route('warehouse.cart')->with('error', 'This item is already submitted and awaiting admin approval.');
        }

        $validated = $request->validate([
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'pickup_address' => ['nullable', 'string', 'max:2000'],
            'pickup_date' => ['required', 'date'],
            'item_name' => ['required', 'string', 'max:255'],
            'item_type' => ['nullable', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'length_cm' => ['nullable', 'numeric', 'min:0'],
            'width_cm' => ['nullable', 'numeric', 'min:0'],
            'height_cm' => ['required', 'numeric', 'min:0.01'],
            'weight_kg' => ['required', 'numeric', 'min:0.01'],
            'storage_days' => ['required', 'integer', 'min:1'],
        ], [], self::ITEM_FIELD_LABELS);

        $warehouse = Warehouse::where('is_active', true)->find($validated['warehouse_id']);

        if (!$warehouse) {
            return back()
                ->withErrors(['warehouse_id' => 'Please select a valid active warehouse.'])
                ->withInput();
        }

        $cartItem->update([
            'warehouse_id' => $warehouse->id,
            'item_name' => $validated['item_name'],
            'item_type' => $validated['item_type'] ?? null,
            'quantity' => $validated['quantity'],
            'length_cm' => $validated['length_cm'] ?? null,
            'width_cm' => $validated['width_cm'] ?? null,
            'height_cm' => $validated['height_cm'],
            'weight_kg' => $validated['weight_kg'],
            'pickup_date' => $validated['pickup_date'],
            'storage_days' => $validated['storage_days'],
        ]);

        $this->updateCartEstimate($cartItem);

        if (auth()->check()) {
            if ($validated['pickup_address'] ?? null) {
                WarehouseAddress::updateOrCreate(
                    ['item_id' => $cartItem->id],
                    [
                        'user_id' => auth()->id(),
                        'pickup_address' => $validated['pickup_address'],
                        'status' => 1,
                    ]
                );
            } elseif ($cartItem->warehouseAddress) {
                $cartItem->warehouseAddress->delete();
            }
        }

        return redirect()->route('warehouse.cart')->with('success', 'Cart item updated and price recalculated successfully.');
    }

    public function checkoutWarehouseCart()
    {
        $cartItems = WarehouseCartItem::with('warehouse')
            ->with('warehouseAddress')
            ->where('user_id', auth()->id())
            ->whereNull('booking_status')
            ->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Your storage cart is empty.');
        }

        foreach ($cartItems as $item) {
            if (!$this->findWarehouse($item)) {
                return back()->with('error', 'Please add an active warehouse for all cart items before saving to requests.');
            }
        }

        DB::transaction(function () use ($cartItems) {
            $cartItems->groupBy(fn (WarehouseCartItem $item) => $this->warehouseGroupKey($item))
                ->each(function ($warehouseItems) {
                    $warehouse = $this->findWarehouse($warehouseItems->first());
                    $warehouseLead = $this->createLeadFromWarehouseItems($warehouseItems, $warehouse);

                    WarehouseCartItem::whereIn('id', $warehouseItems->pluck('id'))->update([
                        'warehouse_lead_id' => $warehouseLead->id,
                        'booking_status' => 'pending',
                    ]);
                });
        });

        return redirect()->route('warehouse.cart')->with('success', 'Cart saved to storage requests successfully.');
    }

    public function checkoutOneWarehouse(Request $request)
    {
        $validated = $request->validate([
            'item_ids' => ['required', 'array', 'min:1'],
            'item_ids.*' => ['integer'],
        ]);

        $warehouseItems = WarehouseCartItem::with(['warehouse', 'warehouseAddress'])
            ->where('user_id', auth()->id())
            ->whereNull('booking_status')
            ->whereIn('id', $validated['item_ids'])
            ->get();

        if ($warehouseItems->isEmpty()) {
            return redirect()->route('warehouse.cart')->with('error', 'These items are no longer in your cart.');
        }

        $warehouse = $this->findWarehouse($warehouseItems->first());

        if (!$warehouse) {
            return redirect()->route('warehouse.cart')->with('error', 'Please add an active warehouse for this request before saving.');
        }

        DB::transaction(function () use ($warehouseItems, $warehouse) {
            $warehouseLead = $this->createLeadFromWarehouseItems($warehouseItems, $warehouse);

            WarehouseCartItem::whereIn('id', $warehouseItems->pluck('id'))->update([
                'warehouse_lead_id' => $warehouseLead->id,
                'booking_status' => 'pending',
            ]);
        });

        return redirect()->route('warehouse.cart')->with('success', 'Storage request saved successfully.');
    }

    public function cancelFreshWarehouse(Request $request)
    {
        $validated = $request->validate([
            'item_ids' => ['required', 'array', 'min:1'],
            'item_ids.*' => ['integer'],
        ]);

        $warehouseItems = WarehouseCartItem::with(['warehouse', 'warehouseAddress'])
            ->where('user_id', auth()->id())
            ->whereNull('booking_status')
            ->whereIn('id', $validated['item_ids'])
            ->get();

        if ($warehouseItems->isEmpty()) {
            return redirect()->route('warehouse.cart')->with('error', 'These items are no longer in your cart.');
        }

        $warehouse = $this->findWarehouse($warehouseItems->first());

        DB::transaction(function () use ($warehouseItems, $warehouse) {
            if ($warehouse) {
                $this->createLeadFromWarehouseItems($warehouseItems, $warehouse, [
                    'admin_status' => 'cancelled',
                    'user_status' => 'cancelled',
                ]);
            }

            WarehouseCartItem::whereIn('id', $warehouseItems->pluck('id'))->delete();
        });

        return redirect()->route('warehouse.cart')->with('success', 'Storage request cancelled.');
    }

    private function createLeadFromWarehouseItems($warehouseItems, Warehouse $warehouse, array $statusOverrides = []): WarehouseLead
    {
        $firstItem = $warehouseItems->first();
        $breakdowns = $warehouseItems->map(function (WarehouseCartItem $item) use ($warehouse) {
            return $this->pricingService->calculateCartItem($item, $warehouse);
        });

        $warehouseLead = WarehouseLead::create(array_merge([
            'user_id' => auth()->id(),
            'item_name' => $this->warehouseItemName($warehouseItems),
            'item_type' => $warehouseItems->pluck('item_type')->filter()->unique()->count() === 1
                ? $warehouseItems->first()->item_type
                : 'multiple',
            'quantity' => $warehouseItems->sum('quantity'),
            'length_cm' => $warehouseItems->max('length_cm'),
            'width_cm' => $warehouseItems->max('width_cm'),
            'height_cm' => $warehouseItems->max('height_cm'),
            'weight_kg' => $warehouseItems->sum('weight_kg'),
            'volume_cft' => $breakdowns->sum('volume_cft'),
            'warehouse_id' => $firstItem->warehouse_id,
            'requested_pickup_date' => $firstItem->pickup_date,
            'storage_days' => $warehouseItems->max('storage_days'),
            'admin_status' => 'pending',
            'user_status' => 'pending',
            'payment_status' => 'unpaid',
            'tracking_number' => $this->generateTrackingNumber(),
        ], $this->aggregateWarehouseBreakdown($breakdowns, $warehouse), $statusOverrides));

        $quote = WarehouseQuote::syncFromLead($warehouseLead->fresh(['user', 'warehouse', 'latestPayment']));
        $quoteData = $quote->quote_data ?: [];
        $quoteData['warehouse_items'] = $warehouseItems->map(fn (WarehouseCartItem $item) => [
            'item_name' => $item->item_name,
            'item_type' => $item->item_type,
            'quantity' => $item->quantity,
            'length_cm' => $item->length_cm,
            'width_cm' => $item->width_cm,
            'height_cm' => $item->height_cm,
            'weight_kg' => $item->weight_kg,
            'storage_days' => $item->storage_days,
            'charge_basis' => $item->charge_basis,
            'charge_weight_kg' => $item->charge_weight_kg,
            'volumetric_weight_kg' => $item->volumetric_weight_kg,
            'estimated_total' => $item->estimated_total,
        ])->values()->all();

        $address = $warehouseItems->first(fn (WarehouseCartItem $item) => $item->warehouseAddress);
        if ($address?->warehouseAddress) {
            $quoteData['warehouse_address'] = [
                'pickup_address' => $address->warehouseAddress->pickup_address,
                'status' => $address->warehouseAddress->status,
            ];
        }

        $quote->update(['quote_data' => $quoteData]);

        return $warehouseLead;
    }

    private function cartQuery()
    {
        if (auth()->check()) {
            return WarehouseCartItem::where('user_id', auth()->id());
        }

        return WarehouseCartItem::where('guest_id', $this->guestCartService->guestId());
    }

    private function updateCartEstimate(WarehouseCartItem $item): void
    {
        $item->load('warehouse');
        $warehouse = $this->findWarehouse($item);

        if (!$warehouse) {
            return;
        }

        $breakdown = $this->pricingService->calculateCartItem($item, $warehouse);

        $item->update([
            'estimated_total' => $breakdown['total_payment'],
            'charge_basis' => $breakdown['charge_basis'],
            'charge_weight_kg' => $breakdown['charge_weight_kg'],
            'volumetric_weight_kg' => $breakdown['volumetric_weight_kg'],
        ]);
    }

    private function findWarehouse(WarehouseCartItem $item): ?Warehouse
    {
        if (!$item->warehouse_id) {
            return null;
        }

        return Warehouse::where('is_active', true)->find($item->warehouse_id);
    }

    private function warehouseGroupKey(WarehouseCartItem $item): string
    {
        return implode('|', [
            $item->warehouse_id,
            optional($item->pickup_date)->format('Y-m-d'),
        ]);
    }

    private function warehouseItemName($items): string
    {
        $names = $items->pluck('item_name')->filter()->unique()->values();

        if ($names->count() === 1) {
            return (string) $names->first();
        }

        return Str::limit($names->join(', '), 250, '...');
    }

    private function aggregateWarehouseBreakdown($breakdowns, Warehouse $warehouse): array
    {
        $calculationTypes = $breakdowns->pluck('calculation_type')->filter()->unique();
        $minCharge = round((float) $warehouse->min_charge, 2);
        $itemsTotal = $breakdowns->sum('total_payment');

        return [
            'calculation_type' => $calculationTypes->count() === 1 ? $calculationTypes->first() : 'mixed',
            'base_price' => $minCharge,
            'weight_charge' => $breakdowns->sum('weight_charge'),
            'volume_charge' => $breakdowns->sum('volume_charge'),
            'multiplier_applied' => 1,
            'subtotal' => $itemsTotal,
            'tax_amount' => $breakdowns->sum('tax_amount'),
            'discount_amount' => $breakdowns->sum('discount_amount'),
            // min_charge is a request-level floor applied once to the
            // combined item total, not per individual item.
            'total_payment' => $this->pricingService->floorShipmentTotal($itemsTotal, $warehouse),
        ];
    }

    private function generateTrackingNumber(): string
    {
        do {
            $trackingNumber = 'WH-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (WarehouseLead::where('tracking_number', $trackingNumber)->exists());

        return $trackingNumber;
    }

    private function createInvoicePayment(WarehouseLead $lead): WarehousePayment
    {
        $payment = WarehousePayment::create([
            'invoice_number' => $this->generateInvoiceNumber(),
            'user_id' => $lead->user_id,
            'warehouse_lead_id' => $lead->id,
            'amount' => $lead->total_payment,
            'method' => $lead->payment_method ?: 'cash',
            'status' => match ($lead->payment_status) {
                'paid' => 'success',
                default => 'pending',
            },
            'transaction_id' => $lead->transaction_id,
            'notes' => 'Invoice generated from tracking page.',
        ]);

        WarehouseQuote::syncFromLead($lead, $payment->invoice_number);

        return $payment;
    }

    private function generateInvoiceNumber(): string
    {
        do {
            $invoiceNumber = 'WINV-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (WarehousePayment::where('invoice_number', $invoiceNumber)->exists());

        return $invoiceNumber;
    }
}
