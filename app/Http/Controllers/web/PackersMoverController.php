<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\PackersMover;
use App\Models\PackersMoverAddress;
use App\Models\PackersMoverCartItem;
use App\Models\PackersMoverLead;
use App\Models\PackersMoverPayment;
use App\Models\PackersMoverQuote;
use App\Models\PricingSetting;
use App\Services\GuestCartService;
use App\Services\PackersMoverInvoicePdfService;
use App\Services\PackersMoverPricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PackersMoverController extends Controller
{
    /**
     * Plain-language labels for packers & movers item fields, used to turn
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
    ];

    public function __construct(
        private GuestCartService $guestCartService,
        private PackersMoverPricingService $pricingService
    ) {
    }

    public function addPackersMoverItem()
    {
        $this->guestCartService->guestId();
        $packersMovers = PackersMover::where('is_active', true)
            ->orderBy('city')
            ->orderBy('name')
            ->get();

        return view('web.packers-mover.add-item', compact('packersMovers'));
    }

    public function savePackersMoverItems(Request $request)
    {
        $validated = $request->validate([
            'packers_mover_id' => ['required', 'integer', 'exists:packers_movers,id'],
            'pickup_address' => ['nullable', 'string', 'max:2000'],
            'drop_address' => ['nullable', 'string', 'max:2000'],
            'pickup_date' => ['required', 'date'],
            'distance_km' => ['required', 'numeric', 'min:0.1'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_name' => ['required', 'string', 'max:255'],
            'items.*.item_type' => ['nullable', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.length_cm' => ['nullable', 'numeric', 'min:0'],
            'items.*.width_cm' => ['nullable', 'numeric', 'min:0'],
            'items.*.height_cm' => ['required', 'numeric', 'min:0.01'],
            'items.*.weight_kg' => ['required', 'numeric', 'min:0.01'],
        ], [], $this->itemFieldAttributes($request->input('items', [])));

        $packersMover = PackersMover::where('is_active', true)->find($validated['packers_mover_id']);

        if (!$packersMover) {
            return back()
                ->withErrors(['packers_mover_id' => 'Please select a valid active packers & movers branch.'])
                ->withInput();
        }

        foreach ($validated['items'] as $item) {
            $cartItem = PackersMoverCartItem::create([
                'user_id' => auth()->id(),
                'guest_id' => auth()->check() ? null : $this->guestCartService->guestId(),
                'packers_mover_id' => $packersMover->id,
                'item_name' => $item['item_name'],
                'item_type' => $item['item_type'] ?? null,
                'quantity' => $item['quantity'],
                'length_cm' => $item['length_cm'] ?? null,
                'width_cm' => $item['width_cm'] ?? null,
                'height_cm' => $item['height_cm'],
                'weight_kg' => $item['weight_kg'],
                'pickup_date' => $validated['pickup_date'],
                'distance_km' => $validated['distance_km'],
            ]);

            $this->updateCartEstimate($cartItem);

            if (auth()->check() && (($validated['pickup_address'] ?? null) || ($validated['drop_address'] ?? null))) {
                PackersMoverAddress::create([
                    'user_id' => auth()->id(),
                    'item_id' => $cartItem->id,
                    'pickup_address' => $validated['pickup_address'] ?? null,
                    'drop_address' => $validated['drop_address'] ?? null,
                    'status' => 1,
                ]);
            }
        }

        return redirect()->route('packers_movers.cart')->with('success', 'Items added to moving cart successfully');
    }

    public function estimatePackersMoverItems(Request $request)
    {
        try {
            $validated = $request->validate([
                'packers_mover_id' => ['required', 'integer', 'exists:packers_movers,id'],
                'distance_km' => ['required', 'numeric', 'min:0.1'],
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

        $packersMover = PackersMover::where('is_active', true)->find($validated['packers_mover_id']);

        if (!$packersMover) {
            return response()->json([
                'success' => false,
                'message' => 'We don\'t have this packers & movers branch available right now. Please pick a different branch.',
            ]);
        }

        $minCharge = round((float) $packersMover->min_charge, 2);
        $itemBreakdowns = [];
        $itemsTotal = 0.0;

        foreach ($validated['items'] as $index => $item) {
            $item['distance_km'] = $validated['distance_km'];
            $breakdown = $this->pricingService->calculateFromDimensions($item, $packersMover);
            $itemsTotal += $breakdown['total_payment'];

            $itemBreakdowns[] = [
                'index' => $index,
                'charge_basis' => $breakdown['charge_basis'],
                'charge_weight_kg' => $breakdown['charge_weight_kg'],
                'volumetric_weight_kg' => $breakdown['volumetric_weight_kg'],
                'volume_cft' => $breakdown['volume_cft'],
                'distance_km' => $breakdown['distance_km'],
                'item_charge' => $breakdown['total_payment'],
            ];
        }

        $itemsTotal = round($itemsTotal, 2);
        $grandTotal = $this->pricingService->floorShipmentTotal($itemsTotal, $packersMover);

        return response()->json([
            'success' => true,
            'packers_mover' => [
                'name' => $packersMover->name,
                'city' => $packersMover->city,
                'price_per_km_per_kg' => (float) $packersMover->price_per_km_per_kg,
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

        if ($firstField === 'packers_mover_id') {
            return 'Please select a packers & movers branch.';
        }

        if ($firstField === 'distance_km') {
            return 'Please enter the moving distance in KM.';
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

    public function packersMoverCart()
    {
        $data = $this->packersMoverCartData();

        return view('web.packers-mover.cart', [
            'cartItems' => $data['cartItems'],
            'cartTotal' => $data['cartTotal'],
        ]);
    }

    public function packersMoverCartData(): array
    {
        $cartItems = $this->cartQuery()
            ->with('packersMover')
            ->latest()
            ->get();

        $cartItems->each(function (PackersMoverCartItem $item) {
            if ($item->booking_status) {
                // Already submitted to a packers & movers lead and awaiting admin
                // approval — show the frozen numbers as-is instead of recalculating.
                $item->calculated_price = $item->estimated_total;
                return;
            }

            $packersMover = $this->findPackersMover($item);

            if (!$packersMover) {
                $item->price_error = 'This packers & movers branch is no longer available. Please edit this item and choose a different branch.';
                return;
            }

            $breakdown = $this->pricingService->calculateCartItem($item, $packersMover);
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
            ->filter(fn (PackersMoverCartItem $item) => !$item->price_error && !$item->booking_status)
            ->groupBy(fn (PackersMoverCartItem $item) => $this->packersMoverGroupKey($item))
            ->sum(function ($groupItems) {
                $packersMover = $groupItems->first()->packersMover;
                $rawTotal = $groupItems->sum('calculated_price');

                return $packersMover ? $this->pricingService->floorShipmentTotal($rawTotal, $packersMover) : $rawTotal;
            });

        return ['cartItems' => $cartItems, 'cartTotal' => $cartTotal];
    }

    public function packersMoverLeads()
    {
        $leads = PackersMoverLead::with('packersMover')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('web.packers-mover.leads', compact('leads'));
    }

    public function trackPackersMover(Request $request)
    {
        $trackingNumber = $request->query('tracking_number');
        $lead = null;
        $userLeads = collect();

        if ($trackingNumber) {
            $lead = PackersMoverLead::with(['packersMover', 'user', 'latestPayment'])
                ->where('tracking_number', $trackingNumber)
                ->when(auth()->check(), function ($query) {
                    $query->where('user_id', auth()->id());
                })
                ->first();
        }

        if (auth()->check() && !$trackingNumber) {
            $userLeads = PackersMoverLead::with('packersMover')
                ->where('user_id', auth()->id())
                ->latest()
                ->limit(10)
                ->get();
        }

        return view('web.packers-mover.track', compact('lead', 'trackingNumber', 'userLeads'));
    }

    public function downloadPackersMoverInvoice(string $trackingNumber, PackersMoverInvoicePdfService $invoicePdfService)
    {
        $lead = PackersMoverLead::with(['packersMover', 'user', 'latestPayment'])
            ->where('tracking_number', $trackingNumber)
            ->when(auth()->check(), function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->firstOrFail();

        if ($lead->admin_status !== 'delivered') {
            return redirect()
                ->route('packers_movers.track', ['tracking_number' => $lead->tracking_number])
                ->with('error', 'Invoice will be available after admin marks the move as delivered.');
        }

        $payment = $lead->latestPayment ?: $this->createInvoicePayment($lead);
        PackersMoverQuote::syncFromLead($lead, $payment->invoice_number);
        $fileName = ($payment->invoice_number ?: $lead->tracking_number) . '.pdf';

        return response($invoicePdfService->output($lead, $payment), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    public function deletePackersMoverCartItem($id)
    {
        $cartItem = $this->cartQuery()->findOrFail($id);

        if ($cartItem->booking_status) {
            return redirect()->route('packers_movers.cart')->with('error', 'This item is already submitted and awaiting admin approval.');
        }

        $cartItem->delete();

        return redirect()->route('packers_movers.cart')->with('success', 'Item removed from cart');
    }

    public function cancelPackersMover($leadId)
    {
        $lead = PackersMoverLead::where('id', $leadId)
            ->where('user_id', auth()->id())
            ->whereIn('admin_status', ['pending', 'reviewed'])
            ->first();

        if (!$lead) {
            return redirect()->route('packers_movers.cart')->with('error', 'This move request can no longer be cancelled.');
        }

        DB::transaction(function () use ($lead) {
            $lead->update([
                'admin_status' => 'cancelled',
                'user_status' => 'cancelled',
            ]);

            PackersMoverCartItem::where('packers_mover_lead_id', $lead->id)->delete();
        });

        return redirect()->route('packers_movers.cart')->with('success', 'Move request cancelled.');
    }

    public function editPackersMoverCartItem($id)
    {
        $cartItem = $this->cartQuery()
            ->with(['packersMover', 'packersMoverAddress'])
            ->findOrFail($id);

        if ($cartItem->booking_status) {
            return redirect()->route('packers_movers.cart')->with('error', 'This item is already submitted and awaiting admin approval.');
        }

        $packersMovers = PackersMover::where('is_active', true)
            ->orderBy('city')
            ->orderBy('name')
            ->get();

        return view('web.packers-mover.edit-cart-item', compact('cartItem', 'packersMovers'));
    }

    public function updatePackersMoverCartItem(Request $request, $id)
    {
        $cartItem = $this->cartQuery()
            ->with('packersMoverAddress')
            ->findOrFail($id);

        if ($cartItem->booking_status) {
            return redirect()->route('packers_movers.cart')->with('error', 'This item is already submitted and awaiting admin approval.');
        }

        $validated = $request->validate([
            'packers_mover_id' => ['required', 'integer', 'exists:packers_movers,id'],
            'pickup_address' => ['nullable', 'string', 'max:2000'],
            'drop_address' => ['nullable', 'string', 'max:2000'],
            'pickup_date' => ['required', 'date'],
            'distance_km' => ['required', 'numeric', 'min:0.1'],
            'item_name' => ['required', 'string', 'max:255'],
            'item_type' => ['nullable', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'length_cm' => ['nullable', 'numeric', 'min:0'],
            'width_cm' => ['nullable', 'numeric', 'min:0'],
            'height_cm' => ['required', 'numeric', 'min:0.01'],
            'weight_kg' => ['required', 'numeric', 'min:0.01'],
        ], [], self::ITEM_FIELD_LABELS);

        $packersMover = PackersMover::where('is_active', true)->find($validated['packers_mover_id']);

        if (!$packersMover) {
            return back()
                ->withErrors(['packers_mover_id' => 'Please select a valid active packers & movers branch.'])
                ->withInput();
        }

        $cartItem->update([
            'packers_mover_id' => $packersMover->id,
            'item_name' => $validated['item_name'],
            'item_type' => $validated['item_type'] ?? null,
            'quantity' => $validated['quantity'],
            'length_cm' => $validated['length_cm'] ?? null,
            'width_cm' => $validated['width_cm'] ?? null,
            'height_cm' => $validated['height_cm'],
            'weight_kg' => $validated['weight_kg'],
            'pickup_date' => $validated['pickup_date'],
            'distance_km' => $validated['distance_km'],
        ]);

        $this->updateCartEstimate($cartItem);

        if (auth()->check()) {
            if (($validated['pickup_address'] ?? null) || ($validated['drop_address'] ?? null)) {
                PackersMoverAddress::updateOrCreate(
                    ['item_id' => $cartItem->id],
                    [
                        'user_id' => auth()->id(),
                        'pickup_address' => $validated['pickup_address'] ?? null,
                        'drop_address' => $validated['drop_address'] ?? null,
                        'status' => 1,
                    ]
                );
            } elseif ($cartItem->packersMoverAddress) {
                $cartItem->packersMoverAddress->delete();
            }
        }

        return redirect()->route('packers_movers.cart')->with('success', 'Cart item updated and price recalculated successfully.');
    }

    public function checkoutPackersMoverCart()
    {
        $cartItems = PackersMoverCartItem::with('packersMover')
            ->with('packersMoverAddress')
            ->where('user_id', auth()->id())
            ->whereNull('booking_status')
            ->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Your moving cart is empty.');
        }

        foreach ($cartItems as $item) {
            if (!$this->findPackersMover($item)) {
                return back()->with('error', 'Please add an active packers & movers branch for all cart items before saving to requests.');
            }
        }

        DB::transaction(function () use ($cartItems) {
            $cartItems->groupBy(fn (PackersMoverCartItem $item) => $this->packersMoverGroupKey($item))
                ->each(function ($groupItems) {
                    $packersMover = $this->findPackersMover($groupItems->first());
                    $lead = $this->createLeadFromPackersMoverItems($groupItems, $packersMover);

                    PackersMoverCartItem::whereIn('id', $groupItems->pluck('id'))->update([
                        'packers_mover_lead_id' => $lead->id,
                        'booking_status' => 'pending',
                    ]);
                });
        });

        return redirect()->route('packers_movers.cart')->with('success', 'Cart saved to move requests successfully.');
    }

    public function checkoutOnePackersMover(Request $request)
    {
        $validated = $request->validate([
            'item_ids' => ['required', 'array', 'min:1'],
            'item_ids.*' => ['integer'],
        ]);

        $groupItems = PackersMoverCartItem::with(['packersMover', 'packersMoverAddress'])
            ->where('user_id', auth()->id())
            ->whereNull('booking_status')
            ->whereIn('id', $validated['item_ids'])
            ->get();

        if ($groupItems->isEmpty()) {
            return redirect()->route('packers_movers.cart')->with('error', 'These items are no longer in your cart.');
        }

        $packersMover = $this->findPackersMover($groupItems->first());

        if (!$packersMover) {
            return redirect()->route('packers_movers.cart')->with('error', 'Please add an active packers & movers branch for this request before saving.');
        }

        DB::transaction(function () use ($groupItems, $packersMover) {
            $lead = $this->createLeadFromPackersMoverItems($groupItems, $packersMover);

            PackersMoverCartItem::whereIn('id', $groupItems->pluck('id'))->update([
                'packers_mover_lead_id' => $lead->id,
                'booking_status' => 'pending',
            ]);
        });

        return redirect()->route('packers_movers.cart')->with('success', 'Move request saved successfully.');
    }

    public function cancelFreshPackersMover(Request $request)
    {
        $validated = $request->validate([
            'item_ids' => ['required', 'array', 'min:1'],
            'item_ids.*' => ['integer'],
        ]);

        $groupItems = PackersMoverCartItem::with(['packersMover', 'packersMoverAddress'])
            ->where('user_id', auth()->id())
            ->whereNull('booking_status')
            ->whereIn('id', $validated['item_ids'])
            ->get();

        if ($groupItems->isEmpty()) {
            return redirect()->route('packers_movers.cart')->with('error', 'These items are no longer in your cart.');
        }

        $packersMover = $this->findPackersMover($groupItems->first());

        DB::transaction(function () use ($groupItems, $packersMover) {
            if ($packersMover) {
                $this->createLeadFromPackersMoverItems($groupItems, $packersMover, [
                    'admin_status' => 'cancelled',
                    'user_status' => 'cancelled',
                ]);
            }

            PackersMoverCartItem::whereIn('id', $groupItems->pluck('id'))->delete();
        });

        return redirect()->route('packers_movers.cart')->with('success', 'Move request cancelled.');
    }

    private function createLeadFromPackersMoverItems($groupItems, PackersMover $packersMover, array $statusOverrides = []): PackersMoverLead
    {
        $firstItem = $groupItems->first();
        $breakdowns = $groupItems->map(function (PackersMoverCartItem $item) use ($packersMover) {
            return $this->pricingService->calculateCartItem($item, $packersMover);
        });

        $lead = PackersMoverLead::create(array_merge([
            'user_id' => auth()->id(),
            'item_name' => $this->packersMoverItemName($groupItems),
            'item_type' => $groupItems->pluck('item_type')->filter()->unique()->count() === 1
                ? $groupItems->first()->item_type
                : 'multiple',
            'quantity' => $groupItems->sum('quantity'),
            'length_cm' => $groupItems->max('length_cm'),
            'width_cm' => $groupItems->max('width_cm'),
            'height_cm' => $groupItems->max('height_cm'),
            'weight_kg' => $groupItems->sum('weight_kg'),
            'volume_cft' => $breakdowns->sum('volume_cft'),
            'packers_mover_id' => $firstItem->packers_mover_id,
            'requested_pickup_date' => $firstItem->pickup_date,
            'distance_km' => $groupItems->max('distance_km'),
            'admin_status' => 'pending',
            'user_status' => 'pending',
            'payment_status' => 'unpaid',
            'tracking_number' => $this->generateTrackingNumber(),
        ], $this->aggregatePackersMoverBreakdown($breakdowns, $packersMover), $statusOverrides));

        $quote = PackersMoverQuote::syncFromLead($lead->fresh(['user', 'packersMover', 'latestPayment']));
        $quoteData = $quote->quote_data ?: [];
        $quoteData['packers_mover_items'] = $groupItems->map(fn (PackersMoverCartItem $item) => [
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

        $address = $groupItems->first(fn (PackersMoverCartItem $item) => $item->packersMoverAddress);
        if ($address?->packersMoverAddress) {
            $quoteData['packers_mover_address'] = [
                'pickup_address' => $address->packersMoverAddress->pickup_address,
                'drop_address' => $address->packersMoverAddress->drop_address,
                'status' => $address->packersMoverAddress->status,
            ];
        }

        $quote->update(['quote_data' => $quoteData]);

        return $lead;
    }

    private function cartQuery()
    {
        if (auth()->check()) {
            return PackersMoverCartItem::where('user_id', auth()->id());
        }

        return PackersMoverCartItem::where('guest_id', $this->guestCartService->guestId());
    }

    private function updateCartEstimate(PackersMoverCartItem $item): void
    {
        $item->load('packersMover');
        $packersMover = $this->findPackersMover($item);

        if (!$packersMover) {
            return;
        }

        $breakdown = $this->pricingService->calculateCartItem($item, $packersMover);

        $item->update([
            'estimated_total' => $breakdown['total_payment'],
            'charge_basis' => $breakdown['charge_basis'],
            'charge_weight_kg' => $breakdown['charge_weight_kg'],
            'volumetric_weight_kg' => $breakdown['volumetric_weight_kg'],
        ]);
    }

    private function findPackersMover(PackersMoverCartItem $item): ?PackersMover
    {
        if (!$item->packers_mover_id) {
            return null;
        }

        return PackersMover::where('is_active', true)->find($item->packers_mover_id);
    }

    private function packersMoverGroupKey(PackersMoverCartItem $item): string
    {
        return implode('|', [
            $item->packers_mover_id,
            optional($item->pickup_date)->format('Y-m-d'),
        ]);
    }

    private function packersMoverItemName($items): string
    {
        $names = $items->pluck('item_name')->filter()->unique()->values();

        if ($names->count() === 1) {
            return (string) $names->first();
        }

        return Str::limit($names->join(', '), 250, '...');
    }

    private function aggregatePackersMoverBreakdown($breakdowns, PackersMover $packersMover): array
    {
        $calculationTypes = $breakdowns->pluck('calculation_type')->filter()->unique();
        $minCharge = round((float) $packersMover->min_charge, 2);
        $itemsTotal = $breakdowns->sum('total_payment');
        $discountAmount = $breakdowns->sum('discount_amount');

        // min_charge is a request-level floor applied once to the
        // combined item total, not per individual item.
        $billableSubtotal = $this->pricingService->floorShipmentTotal($itemsTotal, $packersMover);
        $taxAmount = round($billableSubtotal * PricingSetting::gstPercent() / 100, 2);

        return [
            'calculation_type' => $calculationTypes->count() === 1 ? $calculationTypes->first() : 'mixed',
            'base_price' => $minCharge,
            'weight_charge' => $breakdowns->sum('weight_charge'),
            'volume_charge' => $breakdowns->sum('volume_charge'),
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
            $trackingNumber = 'PM-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (PackersMoverLead::where('tracking_number', $trackingNumber)->exists());

        return $trackingNumber;
    }

    private function createInvoicePayment(PackersMoverLead $lead): PackersMoverPayment
    {
        $payment = PackersMoverPayment::create([
            'invoice_number' => $this->generateInvoiceNumber(),
            'user_id' => $lead->user_id,
            'packers_mover_lead_id' => $lead->id,
            'amount' => $lead->total_payment,
            'method' => $lead->payment_method ?: 'cash',
            'status' => match ($lead->payment_status) {
                'paid' => 'success',
                default => 'pending',
            },
            'transaction_id' => $lead->transaction_id,
            'notes' => 'Invoice generated from tracking page.',
        ]);

        PackersMoverQuote::syncFromLead($lead, $payment->invoice_number);

        return $payment;
    }

    private function generateInvoiceNumber(): string
    {
        do {
            $invoiceNumber = 'PMINV-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (PackersMoverPayment::where('invoice_number', $invoiceNumber)->exists());

        return $invoiceNumber;
    }
}
