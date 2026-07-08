@include('web.header')

@php
    $steps = [
        'pending' => 'Request Received',
        'approved' => 'Pickup Approved',
        'dispatched' => 'In Transit',
        'delivered' => 'Delivered',
    ];
    $current = match (optional($lead)->admin_status) {
        'approved', 'reviewed' => 1,
        'dispatched' => 2,
        'delivered' => 3,
        'cancelled', 'rejected' => -1,
        default => 0,
    };
    $hasDiscount = (float) optional($lead)->discount_amount > 0;
@endphp

<style>
    .track-section {
        background: #f5f7fb;
        padding: 110px 0 80px;
    }

    .track-panel {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 14px 35px rgba(15, 23, 42, .06);
        padding: 28px;
    }

    .track-eyebrow {
        color: #ff7a45;
        font-size: 13px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .track-panel h2 {
        color: #101820;
        font-size: 28px;
        font-weight: 800;
        margin: 4px 0 24px;
    }

    .track-form {
        display: flex;
        gap: 12px;
        margin-bottom: 28px;
        flex-wrap: wrap;
    }

    .track-form input[type="text"] {
        flex: 1 1 320px;
        min-height: 48px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        padding: 0 16px;
    }

    .track-form input[type="text"]:focus {
        border-color: #0e8f7a;
        box-shadow: 0 0 0 3px rgba(14, 143, 122, .12);
        outline: none;
    }

    .track-alert {
        border-radius: 8px;
        padding: 14px 16px;
        margin-bottom: 20px;
        font-size: 14px;
        font-weight: 600;
    }

    .track-alert-danger {
        background: #fef3f2;
        color: #b42318;
        border: 1px solid #fecdca;
    }

    .track-alert-info {
        background: #eaf6fc;
        color: #1d6687;
        border: 1px solid #bfe3f2;
    }

    .track-result-head {
        align-items: flex-start;
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        justify-content: space-between;
        margin-bottom: 24px;
    }

    .track-result-head h4 {
        color: #101820;
        font-size: 20px;
        font-weight: 800;
        margin: 0 0 4px;
    }

    .track-result-head p {
        color: #667085;
        font-size: 13.5px;
        margin: 0;
    }

    .track-status-actions {
        text-align: right;
    }

    .status-badge {
        border-radius: 20px;
        display: inline-block;
        font-size: 11.5px;
        font-weight: 800;
        letter-spacing: .02em;
        padding: 6px 14px;
        text-transform: uppercase;
    }

    .status-badge.progress {
        background: #fff6e8;
        color: #b5750c;
        border: 1px solid #ffe1ab;
    }

    .status-badge.done {
        background: #ecfdf3;
        color: #067647;
        border: 1px solid #abefc6;
    }

    .status-badge.stopped {
        background: #fef3f2;
        color: #b42318;
        border: 1px solid #fecdca;
    }

    .track-invoice-btn {
        align-items: center;
        border: 1px solid #abefc6;
        border-radius: 6px;
        color: #067647;
        display: inline-flex;
        font-size: 12.5px;
        font-weight: 800;
        gap: 6px;
        margin-top: 10px;
        padding: 7px 12px;
        text-decoration: none;
    }

    .track-invoice-btn:hover {
        background: #ecfdf3;
        color: #067647;
    }

    .track-invoice-note {
        color: #98a2b3;
        font-size: 12px;
        margin-top: 10px;
    }

    /* ===== Tracking stepper ===== */
    .tracking-line {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        position: relative;
        margin-bottom: 28px;
        padding-top: 6px;
    }

    .tracking-line::before {
        content: "";
        position: absolute;
        left: 12%;
        right: 12%;
        top: 19px;
        height: 3px;
        background: #e4e8f0;
    }

    .tracking-step {
        position: relative;
        text-align: center;
        z-index: 1;
    }

    .tracking-step span {
        background: #e4e8f0;
        border: 4px solid #fff;
        border-radius: 50%;
        box-shadow: 0 0 0 1px #e4e8f0;
        display: inline-block;
        height: 24px;
        margin-bottom: 10px;
        transition: background-color .2s ease, box-shadow .2s ease;
        width: 24px;
    }

    .tracking-step strong {
        color: #98a2b3;
        display: block;
        font-size: 12.5px;
        font-weight: 700;
        transition: color .2s ease;
    }

    .tracking-step.active span {
        background: #0e8f7a;
        box-shadow: 0 0 0 1px #0e8f7a;
    }

    .tracking-step.active strong {
        color: #101820;
    }

    .tracking-line.rejected::before {
        background: #d94c43;
    }

    .tracking-line.rejected .tracking-step span {
        background: #d94c43;
        box-shadow: 0 0 0 1px #d94c43;
    }

    .tracking-line.rejected .tracking-step strong {
        color: #b42318;
    }

    @media (max-width: 576px) {
        .tracking-step strong {
            font-size: 10.5px;
        }
    }

    .track-meta-grid {
        display: grid;
        gap: 12px;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        margin-bottom: 20px;
    }

    .track-meta {
        background: #fbfbfd;
        border: 1px solid #eef1f6;
        border-radius: 8px;
        padding: 12px 14px;
    }

    .track-meta span {
        color: #98a2b3;
        display: block;
        font-size: 11px;
        letter-spacing: .03em;
        margin-bottom: 4px;
        text-transform: uppercase;
    }

    .track-meta strong {
        color: #101820;
        display: block;
        font-size: 14px;
    }

    .track-summary {
        background: #fbfbfd;
        border: 1px solid #eef1f6;
        border-radius: 8px;
        margin-bottom: 20px;
        padding: 18px 20px;
    }

    .track-summary-line {
        align-items: center;
        color: #4b5563;
        display: flex;
        font-size: 14px;
        justify-content: space-between;
        padding: 6px 0;
    }

    .track-summary-line + .track-summary-line {
        border-top: 1px dashed #eef1f6;
    }

    .track-summary-line strong {
        color: #101820;
    }

    .track-summary-line.total {
        margin-top: 6px;
        padding-top: 14px;
    }

    .track-summary-line.total strong {
        font-size: 18px;
    }

    .recent-list {
        display: grid;
        gap: 10px;
    }

    .recent-item {
        align-items: center;
        background: #fbfbfd;
        border: 1px solid #eef1f6;
        border-radius: 8px;
        display: flex;
        gap: 12px;
        justify-content: space-between;
        padding: 14px 16px;
        text-decoration: none;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .recent-item:hover {
        border-color: #d6dce8;
        box-shadow: 0 4px 14px rgba(18, 33, 60, .06);
    }

    .recent-item strong {
        color: #101820;
        display: block;
        font-size: 14px;
    }

    .recent-item small {
        color: #98a2b3;
        font-size: 12px;
    }

    @media (max-width: 767px) {
        .track-meta-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 575px) {
        .track-result-head {
            flex-direction: column;
        }

        .track-status-actions {
            text-align: left;
        }

        .track-meta-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<section class="track-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-9">
                <div class="track-panel">
                    <span class="track-eyebrow">Track &amp; Trace</span>
                    <h2>Track Shipment</h2>

                    @if(session('error'))
                        <div class="track-alert track-alert-danger">{{ session('error') }}</div>
                    @endif

                    <form method="GET" action="{{ route('shipment.track') }}" class="track-form">
                        <input type="text" name="tracking_number" value="{{ $trackingNumber }}" placeholder="Enter tracking number">
                        <button type="submit" class="primary-btn1 btn-hover">
                            Track
                            <span></span>
                        </button>
                    </form>

                    @if($trackingNumber && !$lead)
                        <div class="track-alert track-alert-danger">No shipment found for this tracking number.</div>
                    @endif

                    @if($lead)
                        @php
                            $isRejected = in_array($lead->admin_status, ['rejected', 'cancelled']);
                            $isDelivered = $lead->admin_status === 'delivered';

                            $badgeClass = match(true) {
                                $isRejected  => 'stopped',
                                $isDelivered => 'done',
                                default      => 'progress',
                            };
                        @endphp

                        <div class="track-result-head">
                            <div>
                                <h4>{{ $lead->item_name }}</h4>
                                <p>{{ $lead->tracking_number }}</p>
                            </div>
                            <div class="track-status-actions">
                                <span class="status-badge {{ $badgeClass }}">{{ ucfirst($lead->admin_status) }}</span>
                                @if($isDelivered)
                                    <div>
                                        <a class="track-invoice-btn" href="{{ route('shipment.invoice.download', $lead->tracking_number) }}">
                                            <i class="bi bi-download"></i> Download Invoice
                                        </a>
                                    </div>
                                @else
                                    <div class="track-invoice-note">Invoice available after delivery</div>
                                @endif
                            </div>
                        </div>

                        @if($isRejected)
                            <div class="track-alert track-alert-danger" style="display:flex; align-items:center; gap:8px;">
                                <i class="bi bi-x-circle-fill"></i>
                                <span>This shipment has been {{ strtolower($lead->admin_status) }}.</span>
                            </div>
                        @endif

                        <div class="tracking-line {{ $isRejected ? 'rejected' : '' }}">
                            @foreach($steps as $key => $label)
                                @php $index = $loop->index; @endphp
                                <div class="tracking-step {{ !$isRejected && $current >= $index ? 'active' : '' }}">
                                    <span></span>
                                    <strong>{{ $label }}</strong>
                                </div>
                            @endforeach
                        </div>

                        <div class="track-meta-grid">
                            <div class="track-meta">
                                <span>From</span>
                                <strong>{{ optional($lead->cityRoute)->from_city ?? '-' }}</strong>
                            </div>
                            <div class="track-meta">
                                <span>To</span>
                                <strong>{{ optional($lead->cityRoute)->to_city ?? '-' }}</strong>
                            </div>
                            <div class="track-meta">
                                <span>Payment</span>
                                <strong>{{ ucfirst($lead->payment_status) }}</strong>
                            </div>
                            <div class="track-meta">
                                <span>Pickup</span>
                                <strong>{{ optional($lead->confirmed_pickup_date ?: $lead->requested_pickup_date)->format('d M Y') }}</strong>
                            </div>
                            <div class="track-meta">
                                <span>Expected Delivery</span>
                                <strong>{{ optional($lead->expected_delivery_date)->format('d M Y') ?? '-' }}</strong>
                            </div>
                            <div class="track-meta">
                                <span>Actual Delivery</span>
                                <strong>{{ optional($lead->actual_delivery_date)->format('d M Y') ?? '-' }}</strong>
                            </div>
                        </div>

                        <div class="track-summary">
                            @if($hasDiscount)
                                <div class="track-summary-line">
                                    <span>Subtotal</span>
                                    <strong>{{ number_format($lead->subtotal, 2) }}</strong>
                                </div>
                                <div class="track-summary-line">
                                    <span>Tax Amount</span>
                                    <strong>{{ number_format($lead->tax_amount, 2) }}</strong>
                                </div>
                                <div class="track-summary-line">
                                    <span>Discount Amount</span>
                                    <strong>-{{ number_format($lead->discount_amount, 2) }}</strong>
                                </div>
                                <div class="track-summary-line total">
                                    <span>Total Payable</span>
                                    <strong>₹{{ number_format($lead->total_payment, 2) }}</strong>
                                </div>
                            @else
                                <div class="track-summary-line total">
                                    <span>Amount</span>
                                    <strong>₹{{ number_format($lead->total_payment, 2) }}</strong>
                                </div>
                            @endif
                        </div>

                        @if($lead->admin_description)
                            <div class="track-alert track-alert-info mb-0">{{ $lead->admin_description }}</div>
                        @endif
                    @elseif($userLeads->isNotEmpty())
                        <h5 class="mb-3" style="color:#101820; font-weight:800;">Recent Shipments</h5>
                        <div class="recent-list">
                            @foreach($userLeads as $item)
                                @php
                                    $itemBadge = match(true) {
                                        in_array($item->admin_status, ['rejected', 'cancelled']) => 'stopped',
                                        $item->admin_status === 'delivered' => 'done',
                                        default => 'progress',
                                    };
                                @endphp
                                <a class="recent-item" href="{{ route('shipment.track', ['tracking_number' => $item->tracking_number]) }}">
                                    <div>
                                        <strong>{{ $item->item_name }}</strong>
                                        <small>{{ $item->tracking_number }}</small>
                                    </div>
                                    <span class="status-badge {{ $itemBadge }}">{{ ucfirst($item->admin_status) }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

@include('web.footer')
