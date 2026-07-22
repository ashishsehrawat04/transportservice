@php
    $isShipment = $mode === 'shipment';
    $addItemRoute = $isShipment ? 'shipment.add_item' : 'warehouse.add_item';
    $editRoute = $isShipment ? 'shipment.cart.edit' : 'warehouse.cart.edit';
    $deleteRoute = $isShipment ? 'shipment.cart.delete' : 'warehouse.cart.delete';
    $checkoutRoute = $isShipment ? 'shipment.cart.checkout' : 'warehouse.cart.checkout';
    $checkoutOneRoute = $isShipment ? 'shipment.cart.checkout_one' : 'warehouse.cart.checkout_one';
    $cancelRoute = $isShipment ? 'shipment.cart.cancel' : 'warehouse.cart.cancel';
    $cancelFreshRoute = $isShipment ? 'shipment.cart.cancel_fresh' : 'warehouse.cart.cancel_fresh';
    $addItemLabel = $isShipment ? 'Add Shipment' : 'Store an Item';
    $groupNoun = $isShipment ? 'Shipment' : 'Storage Request';
    $cancelLabel = $isShipment ? 'Cancel Shipment' : 'Cancel Request';

    $itemCount = $items->count();
    $totalQuantity = $items->sum('quantity');
    $hasPriceIssue = $items->contains(fn ($item) => !empty($item->price_error));
    $hasCheckoutableItems = $items->contains(fn ($item) => empty($item->booking_status));

    $groups = $items->groupBy(function ($item) use ($isShipment) {
        return $isShipment
            ? implode('|', [$item->city_route_id, optional($item->pickup_date)->format('Y-m-d'), optional($item->delivery_date)->format('Y-m-d')])
            : implode('|', [$item->warehouse_id, optional($item->pickup_date)->format('Y-m-d')]);
    });

    $itemIcon = function ($type) {
        return match ($type) {
            'bike' => 'ico-bike',
            'electronics' => 'ico-electronics',
            default => 'ico-box',
        };
    };
@endphp

<div class="cart-section-head">
    <h3>{{ $isShipment ? 'Shipment' : 'Warehouse' }}</h3>
    @if($items->isNotEmpty())
        <span class="cart-section-count">{{ $items->count() }} item{{ $items->count() > 1 ? 's' : '' }}</span>
    @endif
</div>

@if($items->isEmpty())
    <div class="cart-section-empty">
        <p>{{ $isShipment ? 'No shipment items in your cart yet.' : 'No storage items in your cart yet.' }}</p>
        <a href="{{ route($addItemRoute) }}" class="cart-secondary-btn">
            <svg viewBox="0 0 24 24"><use href="#ico-plus"></use></svg>
            {{ $addItemLabel }}
        </a>
    </div>
@else
    <div class="cart-grid">
        <div class="cart-list">
            @foreach($groups as $groupItems)
                @php
                    $firstItem = $groupItems->first();
                    $relation = $isShipment ? $firstItem->cityRoute : $firstItem->warehouse;
                    $pickupDate = optional($firstItem->pickup_date)->format('d M Y') ?? '-';
                    $groupMinCharge = round((float) optional($relation)->min_charge, 2);
                    $groupRawTotal = $groupItems->sum('calculated_price');
                    $groupTotal = max($groupRawTotal, $groupMinCharge);
                    $groupHasError = $groupItems->contains(fn ($item) => !empty($item->price_error));
                    $groupIsPending = (bool) optional($firstItem)->booking_status;
                    $groupLeadId = $isShipment ? optional($firstItem)->transport_lead_id : optional($firstItem)->warehouse_lead_id;
                @endphp

                <article class="cart-item-card ship-group {{ $groupIsPending ? 'is-pending' : '' }}">
                    <div class="cart-item-top">
                        <div class="cart-item-title">
                            <span class="cart-shipment-name-row">
                                <h5>{{ $groupNoun }} {{ $loop->iteration }}</h5>
                                @if($groupIsPending)
                                    <span class="cart-pending-badge">
                                        <svg><use href="#ico-clock"></use></svg>
                                        Pending Approval
                                    </span>
                                @endif
                            </span>
                            <span class="cart-route-chip">
                                @if($isShipment)
                                    <svg viewBox="0 0 24 24"><use href="#ico-pin"></use></svg>
                                    {{ optional($relation)->from_city ?? '-' }} &rarr; {{ optional($relation)->to_city ?? '-' }}
                                @else
                                    <svg viewBox="0 0 24 24"><use href="#ico-warehouse"></use></svg>
                                    {{ optional($relation)->name ?? '-' }} ({{ optional($relation)->city ?? '-' }})
                                @endif
                            </span>
                            <div class="cart-shipment-count" style="width:100%;">{{ $groupItems->count() }} item{{ $groupItems->count() > 1 ? 's' : '' }} in this {{ strtolower($groupNoun) }}</div>
                        </div>

                        <div class="cart-shipment-side">
                            @if($groupHasError)
                                <div class="cart-error"><svg viewBox="0 0 24 24" style="width:13px;height:13px;stroke:currentColor"><use href="#ico-alert"></use></svg> Needs Review</div>
                            @else
                                <div class="cart-price">
                                    <span>{{ $groupNoun }} Total</span>
                                    ₹{{ number_format($groupTotal, 2) }}
                                    @if($groupMinCharge > 0)
                                        <small class="cart-min-note">min ₹{{ number_format($groupMinCharge, 2) }} per {{ strtolower($groupNoun) }}</small>
                                    @endif
                                </div>
                            @endif

                            <div class="cart-actions-row">
                                @if($groupIsPending && $groupLeadId)
                                    <form action="{{ route($cancelRoute, $groupLeadId) }}" method="POST" data-confirm="Cancel this entire request? This cannot be undone." data-confirm-ok="Yes, cancel it" data-confirm-danger>
                                        @csrf
                                        <button type="submit" class="cart-btn cart-cancel-shipment-btn">
                                            <svg><use href="#ico-x"></use></svg> {{ $cancelLabel }}
                                        </button>
                                    </form>
                                @elseif(!$groupIsPending)
                                    @if(!$groupHasError)
                                        <form action="{{ route($checkoutOneRoute) }}" method="POST" data-confirm="Proceed to booking for this request only? The other requests in your cart will stay untouched." data-confirm-ok="Yes, proceed">
                                            @csrf
                                            @foreach($groupItems as $groupItem)
                                                <input type="hidden" name="item_ids[]" value="{{ $groupItem->id }}">
                                            @endforeach
                                            <button type="submit" class="cart-btn cart-book-shipment-btn">
                                                <svg><use href="#ico-check"></use></svg> Proceed to Booking
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route($cancelFreshRoute) }}" method="POST" data-confirm="Cancel this entire request? All items in it will be removed from your cart." data-confirm-ok="Yes, cancel it" data-confirm-danger>
                                        @csrf
                                        @foreach($groupItems as $groupItem)
                                            <input type="hidden" name="item_ids[]" value="{{ $groupItem->id }}">
                                        @endforeach
                                        <button type="submit" class="cart-btn cart-cancel-shipment-btn">
                                            <svg><use href="#ico-x"></use></svg> {{ $cancelLabel }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($isShipment)
                        <div class="cart-route-row">
                            <div>
                                <small class="text-muted d-block" style="font-size:11px; color:var(--ot-muted);">Pickup City</small>
                                <div class="cart-city">{{ optional($relation)->from_city ?? '-' }}</div>
                            </div>
                            <span class="cart-route-arrow"><svg viewBox="0 0 24 24"><use href="#ico-arrow"></use></svg></span>
                            <div class="text-lg-end">
                                <small class="text-muted d-block" style="font-size:11px; color:var(--ot-muted);">Deliver To</small>
                                <div class="cart-city">{{ optional($relation)->to_city ?? '-' }}</div>
                            </div>
                        </div>
                    @else
                        <div class="cart-route-row">
                            <div>
                                <small class="text-muted d-block" style="font-size:11px; color:var(--ot-muted);">Warehouse Address</small>
                                <div class="cart-city">{{ optional($relation)->address ?? '-' }}</div>
                            </div>
                        </div>
                    @endif

                    <div class="cart-meta-grid">
                        <div class="cart-meta">
                            <span>Pickup Date</span>
                            <strong>{{ $pickupDate }}</strong>
                        </div>
                        @if($isShipment)
                            <div class="cart-meta">
                                <span>Delivery Date</span>
                                <strong>{{ optional($firstItem->delivery_date)->format('d M Y') ?? '-' }}</strong>
                            </div>
                            <div class="cart-meta">
                                <span>Transit Time</span>
                                <strong>{{ optional($relation)->transit_days ? $relation->transit_days . ' Day' . ($relation->transit_days > 1 ? 's' : '') : '-' }}</strong>
                            </div>
                        @else
                            <div class="cart-meta">
                                <span>Storage Days</span>
                                <strong>{{ $firstItem->storage_days }} Day{{ $firstItem->storage_days > 1 ? 's' : '' }}</strong>
                            </div>
                            <div class="cart-meta">
                                <span>Rate</span>
                                <strong>{{ optional($relation)->price_per_day_per_kg ? '₹' . number_format($relation->price_per_day_per_kg, 2) . '/kg/day' : '-' }}</strong>
                            </div>
                        @endif
                        <div class="cart-meta">
                            <span>Status</span>
                            <strong class="{{ $groupHasError ? 'text-review' : 'text-ready' }}">
                                {{ $groupHasError ? 'Needs Review' : 'Ready' }}
                            </strong>
                        </div>
                    </div>

                    <div class="cart-item-list">
                        @foreach($groupItems as $item)
                            @php
                                $volumetricWeight = $item->price_breakdown['volumetric_weight_kg'] ?? $item->volumetric_weight_kg;
                                $actualTotalWeight = round((float) $item->weight_kg * (int) $item->quantity, 2);
                                $isVolumeBasis = $item->charge_basis === 'volume';
                            @endphp
                            <div class="item-row">
                                <div class="item-thumb"><svg viewBox="0 0 32 32"><use href="#{{ $itemIcon($item->item_type) }}"></use></svg></div>

                                <div class="item-info">
                                    <div class="item-name">{{ $item->item_name }}</div>
                                    <div class="item-type">{{ $item->item_type ? str_replace('_', ' ', $item->item_type) : ($isShipment ? 'Shipment item' : 'Storage item') }}</div>
                                    <div class="item-specs">
                                        <span class="mono">{{ $item->length_cm ? number_format($item->length_cm, 0) : '-' }}×{{ $item->width_cm ? number_format($item->width_cm, 0) : '-' }}×{{ number_format($item->height_cm, 0) }} cm</span>
                                        <span class="sep"></span>
                                        <span class="mono">Qty {{ $item->quantity }}</span>
                                        @if(!$isShipment)
                                            <span class="sep"></span>
                                            <span class="mono">{{ $item->storage_days }} day{{ $item->storage_days > 1 ? 's' : '' }}</span>
                                        @endif
                                    </div>
                                </div>

                                @if($item->price_error)
                                    <span class="cart-row-error" title="{{ $item->price_error }}" style="grid-column: span 3;">
                                        <svg viewBox="0 0 24 24" style="width:13px;height:13px;stroke:currentColor"><use href="#ico-alert"></use></svg> {{ $item->price_error }}
                                    </span>
                                @else
                                    <div class="weight-compare">
                                        <div class="metric {{ !$isVolumeBasis ? 'winner' : '' }}"><span>Actual</span><b>{{ number_format($actualTotalWeight, 2) }} kg</b></div>
                                        <div class="metric {{ $isVolumeBasis ? 'winner' : '' }}"><span>Volumetric</span><b>{{ $volumetricWeight !== null ? number_format($volumetricWeight, 2) : '0.00' }} kg</b></div>
                                    </div>

                                    <span class="basis-chip {{ $item->charge_basis }}">{{ $isVolumeBasis ? 'Volume' : 'Weight' }}</span>

                                    <div class="item-price">
                                        <span class="amount">₹{{ number_format($item->calculated_price, 2) }}</span>
                                        <span class="rate">{{ number_format($item->charge_weight_kg, 2) }} kg billed</span>
                                    </div>
                                @endif

                                <div class="item-actions">
                                    @unless($item->booking_status)
                                        <a href="{{ route($editRoute, $item->id) }}" class="icon-btn" title="Edit item">
                                            <svg viewBox="0 0 24 24"><use href="#ico-edit"></use></svg>
                                        </a>
                                        <a href="{{ route($deleteRoute, $item->id) }}" class="icon-btn danger" title="Remove item" data-confirm="Remove this item from your cart?" data-confirm-ok="Yes, remove it" data-confirm-danger>
                                            <svg viewBox="0 0 24 24"><use href="#ico-trash"></use></svg>
                                        </a>
                                    @endunless
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <footer class="ship-group-foot">
                        <span>{{ $groupIsPending ? 'Awaiting admin review — locked from edits' : 'Min charge applies once per ' . strtolower($groupNoun) . ', only if the combined item total falls below it' }}</span>
                        <span>Subtotal <b>₹{{ number_format($groupTotal, 2) }}</b></span>
                    </footer>
                </article>
            @endforeach
        </div>

        <div class="cart-summary-panel">
            <h4>{{ $isShipment ? 'Shipment Summary' : 'Storage Summary' }}</h4>

            <div class="cart-summary-line">
                <span>{{ $isShipment ? 'Shipment items' : 'Storage items' }}</span>
                <strong>{{ $itemCount }}</strong>
            </div>
            <div class="cart-summary-line">
                <span>Total quantity</span>
                <strong>{{ $totalQuantity }}</strong>
            </div>
            <div class="cart-summary-line">
                <span>Pricing status</span>
                <strong>{{ $hasPriceIssue ? 'Review needed' : 'Ready' }}</strong>
            </div>

            <div class="cart-summary-total">
                <span>Estimated total amount</span>
                <strong>₹{{ number_format($total, 2) }}</strong>
            </div>

            <div class="cart-summary-actions">
                <form action="{{ route($checkoutRoute) }}" method="POST" data-confirm="Are you sure you want to proceed with the checkout?" data-confirm-ok="Yes, proceed">
                    @csrf
                    <button type="submit" class="btn-cta" {{ !$hasCheckoutableItems || $hasPriceIssue ? 'disabled' : '' }}>
                        Proceed to Booking
                        <svg viewBox="0 0 24 24"><use href="#ico-arrow"></use></svg>
                    </button>
                </form>
                <a href="{{ route($addItemRoute) }}" class="cart-secondary-btn">
                    <svg viewBox="0 0 24 24"><use href="#ico-plus"></use></svg>
                    Add More Items
                </a>
            </div>

            <p class="cart-checkout-note">
                @if($isShipment)
                    Each item is billed on whichever is higher — actual weight or volumetric weight — at the route's per-kg rate, with a per-shipment minimum applied automatically to the combined total.
                @else
                    Each item is billed on whichever is higher — actual weight or volumetric weight — at the warehouse's per-kg-per-day rate, multiplied by storage days, with a per-request minimum applied automatically to the combined total.
                @endif
            </p>
        </div>
    </div>
@endif
