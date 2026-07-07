@include('web.header')

<style>
    .shipment-cart-section {
        padding: 110px 0 80px;
        background: #f5f7fb;
    }

    .cart-page-head {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 24px;
    }

    .cart-eyebrow {
        color: #ff7a45;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .cart-page-head h2 {
        margin: 4px 0 0;
        color: #101820;
        font-size: 34px;
        font-weight: 800;
    }

    .cart-subtitle {
        color: #6b7280;
        margin: 8px 0 0;
    }

    .cart-stat-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 22px;
    }

    .cart-stat {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 16px;
    }

    .cart-stat span {
        color: #6b7280;
        display: block;
        font-size: 13px;
        margin-bottom: 7px;
    }

    .cart-stat strong {
        color: #101820;
        display: block;
        font-size: 22px;
        line-height: 1.1;
    }

    .cart-list {
        display: grid;
        gap: 14px;
    }

    .cart-shipment-count {
        color: #6b7280;
        font-size: 13px;
        font-weight: 700;
        margin-top: 4px;
    }

    .cart-subitem-list {
        display: grid;
        gap: 12px;
    }

    .cart-subitem {
        border: 1px solid #eef2f7;
        border-radius: 8px;
        box-shadow: 0 8px 20px rgba(15, 23, 42, .035);
        padding: 14px;
    }

    .cart-subitem .cart-item-top {
        background: #fbfdff;
        border-bottom: 1px solid #eef2f7;
        margin: -14px -14px 14px;
        padding: 14px;
    }

    .cart-item-card,
    .cart-summary-panel,
    .cart-empty-state {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 14px 35px rgba(15, 23, 42, .06);
    }

    .cart-item-card {
        padding: 18px;
    }

    .cart-item-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 16px;
    }

    .cart-item-title {
        align-items: center;
        display: flex;
        gap: 12px;
        min-width: 0;
    }

    .cart-item-icon {
        align-items: center;
        background: #fff4e8;
        border-radius: 8px;
        color: #ff7a45;
        display: inline-flex;
        flex: 0 0 46px;
        font-size: 22px;
        height: 46px;
        justify-content: center;
        width: 46px;
    }

    .cart-item-title h5 {
        color: #101820;
        font-size: 18px;
        font-weight: 800;
        margin: 0;
        overflow-wrap: anywhere;
    }

    .cart-item-title small {
        color: #6b7280;
        display: block;
        margin-top: 3px;
    }

    .cart-price {
        color: #101820;
        font-size: 20px;
        font-weight: 800;
        text-align: right;
        white-space: nowrap;
    }

    .cart-price span {
        color: #6b7280;
        display: block;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 2px;
    }

    .cart-error {
        color: #dc2626;
        font-size: 13px;
        font-weight: 700;
        text-align: right;
    }

    .cart-route-row {
        align-items: center;
        background: #f8fafc;
        border: 1px solid #eef2f7;
        border-radius: 8px;
        display: flex;
        gap: 10px;
        justify-content: space-between;
        margin-bottom: 16px;
        padding: 12px 14px;
    }

    .cart-city {
        color: #101820;
        font-weight: 800;
        min-width: 0;
        overflow-wrap: anywhere;
    }

    .cart-route-arrow {
        color: #ff7a45;
        flex: 0 0 auto;
        font-size: 20px;
    }

    .cart-meta-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
    }

    .cart-meta {
        border-left: 2px solid #e5e7eb;
        padding: 2px 0 2px 10px;
    }

    .cart-meta span {
        color: #6b7280;
        display: block;
        font-size: 12px;
        margin-bottom: 4px;
    }

    .cart-meta strong {
        color: #101820;
        display: block;
        font-size: 14px;
        font-weight: 800;
        overflow-wrap: anywhere;
    }

    .cart-item-actions {
        align-items: center;
        border-top: 1px solid #eef2f7;
        display: flex;
        justify-content: space-between;
        gap: 12px;
        margin-top: 16px;
        padding-top: 14px;
    }

    .cart-item-actions small {
        color: #6b7280;
    }

    .cart-delete-link {
        align-items: center;
        border: 1px solid #fecaca;
        border-radius: 6px;
        color: #dc2626;
        display: inline-flex;
        font-size: 13px;
        font-weight: 800;
        gap: 6px;
        padding: 8px 12px;
        text-decoration: none;
    }

    .cart-delete-link:hover {
        background: #fef2f2;
        color: #b91c1c;
    }

    .cart-edit-link {
        align-items: center;
        border: 1px solid #bfdbfe;
        border-radius: 6px;
        color: #2563eb;
        display: inline-flex;
        font-size: 13px;
        font-weight: 800;
        gap: 6px;
        padding: 8px 12px;
        text-decoration: none;
    }

    .cart-edit-link:hover {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .cart-action-group {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: flex-end;
    }

    .cart-summary-panel {
        padding: 20px;
        position: sticky;
        top: 96px;
    }

    .cart-summary-panel h4 {
        color: #101820;
        font-size: 20px;
        font-weight: 800;
        margin-bottom: 18px;
    }

    .cart-summary-line {
        align-items: center;
        color: #4b5563;
        display: flex;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 12px;
    }

    .cart-summary-line strong {
        color: #101820;
    }

    .cart-summary-total {
        border-top: 1px solid #e5e7eb;
        margin-top: 16px;
        padding-top: 16px;
    }

    .cart-summary-total strong {
        color: #101820;
        display: block;
        font-size: 28px;
        line-height: 1.1;
    }

    .cart-summary-total span {
        color: #6b7280;
        display: block;
        font-size: 13px;
        margin-top: 4px;
    }

    .cart-summary-actions {
        display: grid;
        gap: 10px;
        margin-top: 20px;
    }

    .cart-secondary-btn {
        align-items: center;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        color: #101820;
        display: inline-flex;
        font-weight: 800;
        gap: 8px;
        justify-content: center;
        min-height: 46px;
        text-decoration: none;
    }

    .cart-secondary-btn:hover {
        background: #f9fafb;
        color: #101820;
    }

    .cart-checkout-note {
        background: #f8fafc;
        border-radius: 8px;
        color: #6b7280;
        font-size: 13px;
        line-height: 1.5;
        margin-top: 16px;
        padding: 12px;
    }

    .cart-empty-state {
        padding: 48px 24px;
        text-align: center;
    }

    .cart-empty-state i {
        align-items: center;
        background: #fff4e8;
        border-radius: 50%;
        color: #ff7a45;
        display: inline-flex;
        font-size: 36px;
        height: 76px;
        justify-content: center;
        margin-bottom: 18px;
        width: 76px;
    }

    .cart-empty-state h4 {
        color: #101820;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .cart-empty-state p {
        color: #6b7280;
        margin: 0 auto 22px;
        max-width: 460px;
    }

    @media (max-width: 991px) {
        .cart-stat-grid,
        .cart-meta-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .cart-summary-panel {
            position: static;
        }
    }

    @media (max-width: 575px) {
        .cart-page-head,
        .cart-item-top,
        .cart-item-actions {
            align-items: stretch;
            flex-direction: column;
        }

        .cart-page-head h2 {
            font-size: 28px;
        }

        .cart-stat-grid,
        .cart-meta-grid {
            grid-template-columns: 1fr;
        }

        .cart-route-row {
            align-items: flex-start;
            flex-direction: column;
        }

        .cart-route-arrow {
            transform: rotate(90deg);
        }

        .cart-price,
        .cart-error {
            text-align: left;
        }
    }
</style>

@php
    $itemCount = $cartItems->count();
    $totalQuantity = $cartItems->sum('quantity');
    $hasPriceIssue = $cartItems->contains(fn ($item) => !empty($item->price_error));
    $hasCheckoutableItems = $cartItems->contains(fn ($item) => empty($item->booking_status));
    $cartShipments = $cartItems->groupBy(fn ($item) => implode('|', [
        $item->city_route_id,
        optional($item->pickup_date)->format('Y-m-d'),
        optional($item->delivery_date)->format('Y-m-d'),
    ]));
@endphp

<section class="shipment-cart-section">
    <div class="container">
        <div class="cart-page-head">
            <div>
                <span class="cart-eyebrow">Shipment Cart</span>
                <h2>Review Your Shipment</h2>
                <p class="cart-subtitle">Check route, dimensions and estimated pricing before saving items to leads.</p>
            </div>
            <a href="{{ route('shipment.add_item') }}" class="primary-btn1 btn-hover">
                Add Item
                <span></span>
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if($cartItems->isEmpty())
            <div class="cart-empty-state">
                <i class="bi bi-box-seam"></i>
                <h4>Your cart is empty</h4>
                <p>Add shipment items with pickup and delivery cities to see pricing here.</p>
                <a href="{{ route('shipment.add_item') }}" class="primary-btn1 btn-hover">
                    Add First Item
                    <span></span>
                </a>
            </div>
        @else
            <div class="cart-stat-grid">
                <div class="cart-stat">
                    <span>Shipments</span>
                    <strong>{{ $cartShipments->count() }}</strong>
                </div>
                <div class="cart-stat">
                    <span>Total Items</span>
                    <strong>{{ $itemCount }}</strong>
                </div>
                <div class="cart-stat">
                    <span>Estimated Total</span>
                    <strong>{{ number_format($cartTotal, 2) }}</strong>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
    <div class="cart-list">
        @foreach($cartShipments as $shipmentItems)
            @php
                $firstItem = $shipmentItems->first();
                $route = $firstItem->cityRoute;
                $pickupDate = optional($firstItem->pickup_date)->format('d M Y') ?? '-';
                $deliveryDate = optional($firstItem->delivery_date)->format('d M Y') ?? '-';
                $shipmentMinCharge = round((float) optional($route)->min_charge, 2);
                $shipmentTotal = $shipmentMinCharge + $shipmentItems->sum('calculated_price');
                $shipmentHasError = $shipmentItems->contains(fn ($item) => !empty($item->price_error));
            @endphp

            <div class="cart-item-card">
                {{-- Shipment header --}}
                <div class="cart-item-top">
                    <div class="cart-item-title">
                        <span class="cart-item-icon">
                            <i class="bi bi-box2"></i>
                        </span>
                        <div>
                            <h5>Shipment {{ $loop->iteration }}</h5>
                            <small>{{ optional($route)->from_city ?? '-' }} to {{ optional($route)->to_city ?? '-' }}</small>
                            <div class="cart-shipment-count">{{ $shipmentItems->count() }} item{{ $shipmentItems->count() > 1 ? 's' : '' }} in this shipment</div>
                        </div>
                    </div>

                    @if($shipmentHasError)
                        <div class="cart-error"><i class="bi bi-exclamation-triangle"></i> Needs Review</div>
                    @else
                        <div class="cart-price">
                            <span>Shipment Total</span>
                            ₹{{ number_format($shipmentTotal, 2) }}
                            @if($shipmentMinCharge > 0)
                                <small class="d-block text-muted" style="font-size:11px; font-weight:600;">incl. ₹{{ number_format($shipmentMinCharge, 2) }} min charge</small>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Route + meta strip --}}
                <div class="cart-route-row">
                    <div>
                        <small class="text-muted d-block">Pickup City</small>
                        <div class="cart-city">{{ optional($route)->from_city ?? '-' }}</div>
                    </div>
                    <span class="cart-route-arrow"><i class="bi bi-arrow-right"></i></span>
                    <div class="text-lg-end">
                        <small class="text-muted d-block">Deliver To</small>
                        <div class="cart-city">{{ optional($route)->to_city ?? '-' }}</div>
                    </div>
                </div>

                <div class="cart-meta-grid mb-3">
                    <div class="cart-meta">
                        <span>Pickup Date</span>
                        <strong>{{ $pickupDate }}</strong>
                    </div>
                    <div class="cart-meta">
                        <span>Delivery Date</span>
                        <strong>{{ $deliveryDate }}</strong>
                    </div>
                    <div class="cart-meta">
                        <span>Distance</span>
                        <strong>{{ $route ? number_format($route->distance_km, 2) . ' KM' : '-' }}</strong>
                    </div>
                    <div class="cart-meta">
                        <span>Status</span>
                        <strong class="{{ $shipmentHasError ? 'text-danger' : 'text-success' }}">
                            {{ $shipmentHasError ? 'Needs Review' : 'Ready' }}
                        </strong>
                    </div>
                </div>

                {{-- Items — single row per item --}}
                <div class="cart-item-list">
                    <div class="cart-item-list-head">
                        <span class="col-item">Item</span>
                        <span class="col-dim">Dimensions</span>
                        <span class="col-qty">Qty</span>
                        <span class="col-weight">Weight</span>
                        <span class="col-volume">Volume</span>
                        <span class="col-price">Price</span>
                        <span class="col-actions">Actions</span>
                    </div>

                    @foreach($shipmentItems as $item)
                        @php $volume = $item->price_breakdown['volume_cft'] ?? null; @endphp
                        <div class="cart-item-row">
                            <span class="col-item" data-label="Item">
                                <span class="cart-row-icon"><i class="bi bi-box2"></i></span>
                                <span class="cart-row-text">
                                    <strong>{{ $item->item_name }}</strong>
                                    <small>{{ $item->item_type ?: 'Shipment item' }}</small>
                                </span>
                            </span>

                            <span class="col-dim" data-label="Dimensions">
                                {{ $item->length_cm ? number_format($item->length_cm, 1) : '-' }}
                                × {{ $item->width_cm ? number_format($item->width_cm, 1) : '-' }}
                                × {{ number_format($item->height_cm, 1) }} cm
                            </span>

                            <span class="col-qty" data-label="Qty">{{ $item->quantity }}</span>

                            <span class="col-weight" data-label="Weight">{{ number_format($item->weight_kg, 2) }} kg</span>

                            <span class="col-volume" data-label="Volume">
                                {{ $volume !== null ? number_format($volume, 2) . ' cft' : '-' }}
                            </span>

                            <span class="col-price" data-label="Price">
                                @if($item->price_error)
                                    <span class="cart-row-error" title="{{ $item->price_error }}">
                                        <i class="bi bi-exclamation-circle"></i> Error
                                    </span>
                                @else
                                    ₹{{ number_format($item->calculated_price, 2) }}
                                    <div class="charge-line">
                                        <span class="basis-chip {{ $item->charge_basis }}">{{ $item->charge_basis === 'volume' ? 'Volume' : 'Weight' }}</span>
                                        <span class="charge-kg">{{ number_format($item->charge_weight_kg, 2) }} kg</span>
                                    </div>
                                @endif
                            </span>

                            <span class="col-actions" data-label="Actions">
                                @if($item->booking_status)
                                    <span class="cart-pending-badge" title="Submitted — awaiting admin approval">
                                        <i class="bi bi-hourglass-split"></i> Pending Approval
                                    </span>
                                @else
                                    <a href="{{ route('shipment.cart.edit', $item->id) }}" class="cart-row-btn cart-row-btn-edit" title="Edit item">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="{{ route('shipment.cart.delete', $item->id) }}" class="cart-row-btn cart-row-btn-delete" title="Remove item" onclick="return confirm('Remove this item?')">
                                        <i class="bi bi-trash3"></i>
                                    </a>
                                @endif
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>

<style>
    /* ===== Shipment card ===== */
    .cart-item-card {
        background: #ffffff;
        border: 1px solid #E4E8F0;
        border-radius: 14px;
        padding: 22px 24px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(18, 33, 60, 0.04);
    }

    .cart-item-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 16px;
    }

    .cart-item-title {
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }

    .cart-item-icon {
        width: 40px;
        height: 40px;
        min-width: 40px;
        border-radius: 10px;
        background: #101820;
        color: #ff7a45;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .cart-item-title h5 {
        margin: 0 0 2px;
        font-weight: 700;
        color: #101820;
        font-size: 16px;
    }

    .cart-item-title small {
        color: #667085;
        font-size: 13px;
    }

    .cart-shipment-count {
        font-size: 12px;
        color: #98A2B3;
        margin-top: 4px;
    }

    .cart-price {
        text-align: right;
        font-weight: 700;
        font-size: 18px;
        color: #101820;
        white-space: nowrap;
    }

    .cart-price span {
        display: block;
        font-weight: 500;
        font-size: 11px;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: #98A2B3;
        margin-bottom: 2px;
    }

    .cart-error {
        background: #FEF3F2;
        color: #B42318;
        border: 1px solid #FECDCA;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    /* ===== Route strip ===== */
    .cart-route-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #F8F9FC;
        border: 1px solid #EEF1F6;
        border-radius: 10px;
        padding: 12px 18px;
        margin-bottom: 14px;
    }

    .cart-city {
        font-weight: 700;
        color: #101820;
        font-size: 15px;
    }

    .cart-route-arrow {
        color: #ff7a45;
        font-size: 16px;
    }

    /* ===== Meta grid ===== */
    .cart-meta-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
    }

    .cart-meta {
        background: #FBFBFD;
        border: 1px solid #EEF1F6;
        border-radius: 8px;
        padding: 8px 12px;
    }

    .cart-meta span {
        display: block;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .03em;
        color: #98A2B3;
        margin-bottom: 3px;
    }

    .cart-meta strong {
        font-size: 13px;
        color: #101820;
    }

    .text-success { color: #067647 !important; }
    .text-danger { color: #B42318 !important; }

    /* ===== Item list ===== */
    .cart-item-list {
        margin-top: 18px;
        border-top: 1px dashed #E4E8F0;
        padding-top: 14px;
    }

    .cart-item-list-head {
        display: grid;
        grid-template-columns: 2.2fr 1.4fr .7fr 1fr 1fr 1fr .9fr;
        gap: 10px;
        padding: 0 14px 8px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .03em;
        color: #98A2B3;
    }

    .cart-item-row {
        display: grid;
        grid-template-columns: 2.2fr 1.4fr .7fr 1fr 1fr 1fr .9fr;
        gap: 10px;
        align-items: center;
        background: #FBFBFD;
        border: 1px solid #EEF1F6;
        border-radius: 10px;
        padding: 12px 14px;
        margin-bottom: 8px;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .cart-item-row:hover {
        border-color: #D6DCE8;
        box-shadow: 0 2px 8px rgba(18, 33, 60, 0.06);
    }

    .cart-item-row:last-child {
        margin-bottom: 0;
    }

    .col-item {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    .cart-row-icon {
        width: 30px;
        height: 30px;
        min-width: 30px;
        border-radius: 8px;
        background: #EEF1F6;
        color: #101820;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }

    .cart-row-text {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .cart-row-text strong {
        font-size: 13.5px;
        color: #101820;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .cart-row-text small {
        font-size: 11.5px;
        color: #98A2B3;
    }

    .col-dim,
    .col-qty,
    .col-weight,
    .col-volume {
        font-size: 13px;
        color: #344054;
    }

    .col-price {
        font-weight: 700;
        color: #101820;
        font-size: 13.5px;
    }

    .charge-line {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 5px;
    }

    .basis-chip {
        display: inline-flex;
        align-items: center;
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .02em;
        padding: 2px 7px;
        border-radius: 20px;
        border: 1px solid;
        white-space: nowrap;
    }

    .basis-chip.weight {
        color: #175CD3;
        background: #EAF2FF;
        border-color: #C8DDFF;
    }

    .basis-chip.volume {
        color: #6941C6;
        background: #F4EBFF;
        border-color: #E2D2FB;
    }

    .charge-kg {
        font-size: 11.5px;
        color: #667085;
    }

    .cart-pending-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11.5px;
        font-weight: 700;
        color: #b5750c;
        background: #fff6e8;
        border: 1px solid #ffe1ab;
        padding: 5px 10px;
        border-radius: 20px;
        white-space: nowrap;
    }

    .cart-row-error {
        color: #B42318;
        font-weight: 600;
        font-size: 12px;
        cursor: help;
    }

    .col-actions {
        display: flex;
        gap: 6px;
        justify-content: flex-end;
    }

    .cart-row-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        border: 1px solid transparent;
        text-decoration: none;
        transition: all .15s ease;
    }

    .cart-row-btn-edit {
        background: #EAF2FF;
        color: #175CD3;
    }

    .cart-row-btn-edit:hover {
        background: #175CD3;
        color: #fff;
    }

    .cart-row-btn-delete {
        background: #FEF3F2;
        color: #B42318;
    }

    .cart-row-btn-delete:hover {
        background: #B42318;
        color: #fff;
    }

    /* ===== Responsive: stack rows on small screens ===== */
    @media (max-width: 767px) {
        .cart-item-list-head {
            display: none;
        }

        .cart-item-row {
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .cart-item-row > span:not(.col-item) {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            border-top: 1px dashed #EEF1F6;
            padding-top: 6px;
        }

        .cart-item-row > span[data-label]:not(.col-item)::before {
            content: attr(data-label);
            font-weight: 600;
            color: #98A2B3;
            text-transform: uppercase;
            font-size: 10.5px;
            letter-spacing: .03em;
        }

        .col-actions {
            justify-content: flex-start;
            border-top: 1px dashed #EEF1F6;
            padding-top: 8px;
        }

        .cart-meta-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

                <div class="col-lg-4">
                    <div class="cart-summary-panel">
                        <h4>Order Summary</h4>

                        <div class="cart-summary-line">
                            <span>Shipment items</span>
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
                            <strong>{{ number_format($cartTotal, 2) }}</strong>
                            <span>Estimated total amount</span>
                        </div>

                        <div class="cart-summary-actions">
                            <form action="{{ route('shipment.cart.checkout') }}" method="POST">
                                @csrf
                                <button type="submit" class="primary-btn1 btn-hover w-100 justify-content-center" {{ !$hasCheckoutableItems || $hasPriceIssue ? 'disabled' : '' }} onclick="return confirm('Save cart items to transport leads?')">
                                    Proceed to booking
                                    <span></span>
                                </button>
                            </form>
                            <a href="{{ route('shipment.add_item') }}" class="cart-secondary-btn">
                                <i class="bi bi-plus-circle"></i>
                                Add More Items
                            </a>
                        </div>

                        <div class="cart-checkout-note">
                            After saving to leads, admin can review pickup, dispatch, delivery and payment updates.
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

@include('web.footer')
