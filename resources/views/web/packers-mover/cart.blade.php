@include('web.header')

<style>
    .pm-cart-section {
        padding: 110px 0 80px;
        background:
            radial-gradient(ellipse 900px 400px at 12% 0%, rgba(14, 143, 122, .05), transparent 60%),
            var(--ot-bg);
    }

    .pm-cart-head {
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .pm-cart-eyebrow {
        color: var(--ot-amber-dark);
        display: block;
        font-size: 12.5px;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
    }

    .pm-cart-head h2 {
        font-size: 30px;
        font-weight: 700;
        margin: 5px 0 0;
    }

    .pm-cart-empty {
        background: var(--ot-panel);
        border: 1px solid var(--ot-line);
        border-radius: 16px;
        box-shadow: var(--ot-shadow-sm);
        padding: 56px 24px;
        text-align: center;
    }

    .pm-cart-empty i {
        align-items: center;
        background: var(--ot-panel-tint);
        border-radius: 50%;
        color: var(--ot-green-dark);
        display: inline-flex;
        font-size: 32px;
        height: 68px;
        justify-content: center;
        margin-bottom: 16px;
        width: 68px;
    }

    .pm-stat-grid {
        display: grid;
        gap: 14px;
        grid-template-columns: repeat(3, 1fr);
        margin-bottom: 24px;
    }

    .pm-stat {
        background: var(--ot-panel);
        border: 1px solid var(--ot-line);
        border-radius: 14px;
        box-shadow: var(--ot-shadow-sm);
        padding: 16px 18px;
    }

    .pm-stat span {
        color: var(--ot-muted);
        display: block;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        margin-bottom: 6px;
    }

    .pm-stat strong {
        font-family: var(--ot-mono);
        font-size: 20px;
    }

    .pm-group-card {
        background: var(--ot-panel);
        border: 1px solid var(--ot-line);
        border-radius: 16px;
        box-shadow: var(--ot-shadow-sm);
        margin-bottom: 20px;
        overflow: hidden;
    }

    .pm-group-head {
        align-items: flex-start;
        background: linear-gradient(180deg, var(--ot-panel-tint), transparent);
        border-bottom: 1px solid var(--ot-line);
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        justify-content: space-between;
        padding: 18px 22px;
    }

    .pm-group-title h5 {
        font-size: 16px;
        font-weight: 700;
        margin: 0 0 4px;
    }

    .pm-group-title small {
        color: var(--ot-muted);
        font-size: 12px;
    }

    .pm-pending-badge {
        background: var(--ot-gold-bg);
        border: 1px solid var(--ot-gold-line);
        border-radius: 999px;
        color: var(--ot-gold);
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 10.5px;
        font-weight: 800;
        letter-spacing: .03em;
        padding: 4px 10px;
        text-transform: uppercase;
        margin-left: 8px;
    }

    .pm-group-total {
        text-align: right;
    }

    .pm-group-total span {
        color: var(--ot-muted);
        display: block;
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .pm-group-total strong {
        color: var(--ot-green-dark);
        font-family: var(--ot-mono);
        font-size: 20px;
    }

    .pm-group-body {
        padding: 18px 22px;
    }

    .pm-meta-grid {
        display: grid;
        gap: 12px;
        grid-template-columns: repeat(4, 1fr);
        margin-bottom: 18px;
    }

    .pm-meta {
        border-left: 2px solid var(--ot-line);
        padding: 1px 0 1px 10px;
    }

    .pm-meta span {
        color: var(--ot-muted);
        display: block;
        font-size: 10px;
        letter-spacing: .04em;
        margin-bottom: 3px;
        text-transform: uppercase;
    }

    .pm-meta strong {
        display: block;
        font-size: 13px;
        font-weight: 700;
    }

    .pm-item-row {
        align-items: center;
        border-top: 1px dashed var(--ot-line);
        display: grid;
        gap: 12px;
        grid-template-columns: minmax(0,2fr) auto auto auto;
        padding: 12px 0;
    }

    .pm-item-row:first-child {
        border-top: none;
    }

    .pm-item-name {
        font-weight: 700;
        font-size: 13.5px;
    }

    .pm-item-specs {
        color: var(--ot-muted);
        font-family: var(--ot-mono);
        font-size: 12px;
    }

    .pm-item-price {
        font-family: var(--ot-mono);
        font-weight: 700;
        text-align: right;
    }

    .pm-item-actions {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
    }

    .pm-icon-btn {
        align-items: center;
        border: 1px solid var(--ot-line);
        border-radius: 8px;
        color: var(--ot-ink);
        display: inline-flex;
        height: 32px;
        justify-content: center;
        width: 32px;
    }

    .pm-icon-btn.danger {
        border-color: #fecdca;
        color: #b42318;
    }

    .pm-icon-btn:hover {
        background: var(--ot-panel-tint);
    }

    .pm-group-actions {
        border-top: 1px solid var(--ot-line);
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: flex-end;
        padding: 16px 22px;
    }

    .pm-btn {
        align-items: center;
        border-radius: 9px;
        display: inline-flex;
        font-family: var(--ot-display);
        font-size: 12.5px;
        font-weight: 700;
        gap: 7px;
        padding: 9px 16px;
    }

    .pm-btn-primary {
        background: var(--ot-green);
        border: 1px solid var(--ot-green);
        color: #fff;
    }

    .pm-btn-danger {
        background: #fef3f2;
        border: 1px solid #fecdca;
        color: #b42318;
    }

    .pm-error-banner {
        background: #fef3f2;
        border: 1px solid #fecdca;
        border-radius: 8px;
        color: #b42318;
        font-size: 12.5px;
        padding: 10px 14px;
        margin-bottom: 14px;
    }
</style>

<section class="pm-cart-section">
    <div class="container">
        <div class="pm-cart-head">
            <div>
                <span class="pm-cart-eyebrow">Packers &amp; Movers</span>
                <h2>Move Requests Cart</h2>
            </div>
            <a href="{{ route('packers_movers.add_item') }}" class="primary-btn1 btn-hover">
                Add More Items
                <span></span>
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @php
            $groups = $cartItems->groupBy(function ($item) {
                return implode('|', [$item->packers_mover_id, optional($item->pickup_date)->format('Y-m-d')]);
            });
        @endphp

        @if($cartItems->isEmpty())
            <div class="pm-cart-empty">
                <i class="bi bi-truck"></i>
                <h4>Your moving cart is empty</h4>
                <p>Add an item to see your move request here.</p>
                <a href="{{ route('packers_movers.add_item') }}" class="primary-btn1 btn-hover">
                    Book Your Move
                    <span></span>
                </a>
            </div>
        @else
            <div class="pm-stat-grid">
                <div class="pm-stat">
                    <span>Requests</span>
                    <strong>{{ $groups->count() }}</strong>
                </div>
                <div class="pm-stat">
                    <span>Total Items</span>
                    <strong>{{ $cartItems->count() }}</strong>
                </div>
                <div class="pm-stat">
                    <span>Estimated Total</span>
                    <strong>₹{{ number_format($cartTotal, 2) }}</strong>
                </div>
            </div>

            @foreach($groups as $groupItems)
                @php
                    $firstItem = $groupItems->first();
                    $packersMover = $firstItem->packersMover;
                    $groupMinCharge = round((float) optional($packersMover)->min_charge, 2);
                    $groupRawTotal = $groupItems->sum('calculated_price');
                    $groupTotal = max($groupRawTotal, $groupMinCharge);
                    $groupHasError = $groupItems->contains(fn ($item) => !empty($item->price_error));
                    $groupIsPending = (bool) optional($firstItem)->booking_status;
                    $groupLeadId = optional($firstItem)->packers_mover_lead_id;
                @endphp
                <div class="pm-group-card">
                    <div class="pm-group-head">
                        <div class="pm-group-title">
                            <h5>
                                {{ optional($packersMover)->name ?? '-' }} ({{ optional($packersMover)->city ?? '-' }})
                                @if($groupIsPending)
                                    <span class="pm-pending-badge"><i class="bi bi-clock"></i> Pending Approval</span>
                                @endif
                            </h5>
                            <small>{{ $groupItems->count() }} item{{ $groupItems->count() > 1 ? 's' : '' }} in this move</small>
                        </div>
                        @unless($groupHasError)
                            <div class="pm-group-total">
                                <span>Move Total</span>
                                <strong>₹{{ number_format($groupTotal, 2) }}</strong>
                            </div>
                        @endunless
                    </div>

                    <div class="pm-group-body">
                        @if($groupHasError)
                            <div class="pm-error-banner"><i class="bi bi-exclamation-triangle"></i> Some items in this request need review.</div>
                        @endif

                        <div class="pm-meta-grid">
                            <div class="pm-meta">
                                <span>Pickup Date</span>
                                <strong>{{ optional($firstItem->pickup_date)->format('d M Y') ?? '-' }}</strong>
                            </div>
                            <div class="pm-meta">
                                <span>Distance</span>
                                <strong>{{ number_format($firstItem->distance_km, 1) }} KM</strong>
                            </div>
                            <div class="pm-meta">
                                <span>Rate</span>
                                <strong>{{ optional($packersMover)->price_per_km_per_kg ? '₹' . number_format($packersMover->price_per_km_per_kg, 2) . '/kg/km' : '-' }}</strong>
                            </div>
                            <div class="pm-meta">
                                <span>Status</span>
                                <strong>{{ $groupHasError ? 'Needs Review' : 'Ready' }}</strong>
                            </div>
                        </div>

                        @foreach($groupItems as $item)
                            @php
                                $isVolumeBasis = $item->charge_basis === 'volume';
                            @endphp
                            <div class="pm-item-row">
                                <div>
                                    <div class="pm-item-name">{{ $item->item_name }}</div>
                                    <div class="pm-item-specs">{{ $item->length_cm ? number_format($item->length_cm, 0) : '-' }}×{{ $item->width_cm ? number_format($item->width_cm, 0) : '-' }}×{{ number_format($item->height_cm, 0) }} cm &middot; Qty {{ $item->quantity }}</div>
                                </div>

                                @if($item->price_error)
                                    <div class="pm-error-banner" style="grid-column: span 3; margin-bottom: 0;">{{ $item->price_error }}</div>
                                @else
                                    <span class="basis-chip {{ $item->charge_basis }}" style="display:inline-flex;align-items:center;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.04em;padding:3px 9px;border-radius:999px;white-space:nowrap;{{ $isVolumeBasis ? 'color:var(--ot-green-dark);background:rgba(14,143,122,.1);border:1px solid rgba(14,143,122,.3);' : 'color:var(--ot-sky);background:rgba(47,143,224,.12);border:1px solid rgba(47,143,224,.3);' }}">{{ $isVolumeBasis ? 'Volume' : 'Weight' }}</span>
                                    <div class="pm-item-price">
                                        ₹{{ number_format($item->calculated_price, 2) }}
                                    </div>
                                @endif

                                <div class="pm-item-actions">
                                    @unless($item->booking_status)
                                        <a href="{{ route('packers_movers.cart.edit', $item->id) }}" class="pm-icon-btn" title="Edit item"><i class="bi bi-pencil"></i></a>
                                        <a href="{{ route('packers_movers.cart.delete', $item->id) }}" class="pm-icon-btn danger" title="Remove item" onclick="return confirm('Remove this item from your cart?')"><i class="bi bi-trash"></i></a>
                                    @endunless
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="pm-group-actions">
                        @if($groupIsPending && $groupLeadId)
                            <form action="{{ route('packers_movers.cart.cancel', $groupLeadId) }}" method="POST" onsubmit="return confirm('Cancel this entire request? This cannot be undone.')">
                                @csrf
                                <button type="submit" class="pm-btn pm-btn-danger"><i class="bi bi-x-lg"></i> Cancel Request</button>
                            </form>
                        @elseif(!$groupIsPending)
                            @unless($groupHasError)
                                <form action="{{ route('packers_movers.cart.checkout_one') }}" method="POST" onsubmit="return confirm('Proceed to booking for this request only?')">
                                    @csrf
                                    @foreach($groupItems as $groupItem)
                                        <input type="hidden" name="item_ids[]" value="{{ $groupItem->id }}">
                                    @endforeach
                                    <button type="submit" class="pm-btn pm-btn-primary"><i class="bi bi-check-lg"></i> Proceed to Booking</button>
                                </form>
                            @endunless
                            <form action="{{ route('packers_movers.cart.cancel_fresh') }}" method="POST" onsubmit="return confirm('Cancel this entire request? All items in it will be removed from your cart.')">
                                @csrf
                                @foreach($groupItems as $groupItem)
                                    <input type="hidden" name="item_ids[]" value="{{ $groupItem->id }}">
                                @endforeach
                                <button type="submit" class="pm-btn pm-btn-danger"><i class="bi bi-x-lg"></i> Cancel Request</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach

            <form action="{{ route('packers_movers.cart.checkout') }}" method="POST" onsubmit="return confirm('Proceed with checkout for all requests in your cart?')">
                @csrf
                <button type="submit" class="primary-btn1 btn-hover" {{ $cartItems->contains(fn($i) => empty($i->booking_status)) ? '' : 'disabled' }}>
                    Proceed to Booking (All)
                    <span></span>
                </button>
            </form>
        @endif
    </div>
</section>

@include('web.footer')
