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
        color: #ff7a00;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .cart-page-head h2 {
        margin: 4px 0 0;
        color: #111827;
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
        color: #111827;
        display: block;
        font-size: 22px;
        line-height: 1.1;
    }

    .cart-list {
        display: grid;
        gap: 14px;
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
        color: #ff7a00;
        display: inline-flex;
        flex: 0 0 46px;
        font-size: 22px;
        height: 46px;
        justify-content: center;
        width: 46px;
    }

    .cart-item-title h5 {
        color: #111827;
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
        color: #111827;
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
        color: #111827;
        font-weight: 800;
        min-width: 0;
        overflow-wrap: anywhere;
    }

    .cart-route-arrow {
        color: #ff7a00;
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
        color: #111827;
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

    .cart-summary-panel {
        padding: 20px;
        position: sticky;
        top: 96px;
    }

    .cart-summary-panel h4 {
        color: #111827;
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
        color: #111827;
    }

    .cart-summary-total {
        border-top: 1px solid #e5e7eb;
        margin-top: 16px;
        padding-top: 16px;
    }

    .cart-summary-total strong {
        color: #111827;
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
        color: #111827;
        display: inline-flex;
        font-weight: 800;
        gap: 8px;
        justify-content: center;
        min-height: 46px;
        text-decoration: none;
    }

    .cart-secondary-btn:hover {
        background: #f9fafb;
        color: #111827;
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
        color: #ff7a00;
        display: inline-flex;
        font-size: 36px;
        height: 76px;
        justify-content: center;
        margin-bottom: 18px;
        width: 76px;
    }

    .cart-empty-state h4 {
        color: #111827;
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
                    <span>Total Items</span>
                    <strong>{{ $itemCount }}</strong>
                </div>
                <div class="cart-stat">
                    <span>Total Quantity</span>
                    <strong>{{ $totalQuantity }}</strong>
                </div>
                <div class="cart-stat">
                    <span>Estimated Total</span>
                    <strong>{{ number_format($cartTotal, 2) }}</strong>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="cart-list">
                        @foreach($cartItems as $item)
                            @php
                                $route = $item->cityRoute;
                                $pickupDate = optional($item->pickup_date)->format('d M Y') ?? '-';
                                $deliveryDate = optional($item->delivery_date)->format('d M Y') ?? '-';
                                $volume = $item->price_breakdown['volume_cft'] ?? null;
                            @endphp

                            <div class="cart-item-card">
                                <div class="cart-item-top">
                                    <div class="cart-item-title">
                                        <span class="cart-item-icon">
                                            <i class="bi bi-box2"></i>
                                        </span>
                                        <div>
                                            <h5>{{ $item->item_name }}</h5>
                                            <small>{{ $item->item_type ?: 'Shipment item' }}</small>
                                        </div>
                                    </div>

                                    @if($item->price_error)
                                        <div class="cart-error">{{ $item->price_error }}</div>
                                    @else
                                        <div class="cart-price">
                                            <span>Estimated Price</span>
                                            {{ number_format($item->calculated_price, 2) }}
                                        </div>
                                    @endif
                                </div>

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

                                <div class="cart-meta-grid">
                                    <div class="cart-meta">
                                        <span>Quantity</span>
                                        <strong>{{ $item->quantity }}</strong>
                                    </div>
                                    <div class="cart-meta">
                                        <span>Dimensions</span>
                                        <strong>
                                            {{ $item->length_cm ? number_format($item->length_cm, 1) : '-' }}
                                            x {{ $item->width_cm ? number_format($item->width_cm, 1) : '-' }}
                                            x {{ number_format($item->height_cm, 1) }} CM
                                        </strong>
                                    </div>
                                    <div class="cart-meta">
                                        <span>Weight</span>
                                        <strong>{{ number_format($item->weight_kg, 2) }} KG</strong>
                                    </div>
                                    <div class="cart-meta">
                                        <span>Volume</span>
                                        <strong>{{ $volume !== null ? number_format($volume, 2) . ' CFT' : '-' }}</strong>
                                    </div>
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
                                        <strong>{{ $item->price_error ? 'Needs Review' : 'Ready' }}</strong>
                                    </div>
                                </div>

                                <div class="cart-item-actions">
                                    <small>Item #{{ $loop->iteration }}</small>
                                    <a href="{{ route('shipment.cart.delete', $item->id) }}" class="cart-delete-link" onclick="return confirm('Remove this item?')">
                                        <i class="bi bi-trash3"></i>
                                        Delete
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

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
                                <button type="submit" class="primary-btn1 btn-hover w-100 justify-content-center" {{ $cartItems->isEmpty() || !$price || $hasPriceIssue ? 'disabled' : '' }} onclick="return confirm('Save cart items to transport leads?')">
                                    Save To Leads
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
