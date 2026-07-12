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
    $stepCount = count($steps);
    $stepperPercent = $current > 0 ? round(($current / ($stepCount - 1)) * 100, 2) : 0;
    // The connector line spans the middle 76% of the stepper (12% inset on each side).
    $trackFillWidth = round(($stepperPercent / 100) * 76, 2);
    $trackPkgLeft = round(12 + $trackFillWidth, 2);
@endphp

<style>
    .track-section {
        padding: 110px 0 80px;
        background:
            radial-gradient(ellipse 900px 400px at 88% 0%, rgba(47, 143, 224, .06), transparent 60%),
            var(--ot-bg);
    }

    .track-panel {
        background: var(--ot-panel);
        border: 1px solid var(--ot-line);
        border-radius: 16px;
        box-shadow: var(--ot-shadow);
        padding: 30px;
    }

    .track-eyebrow {
        color: var(--ot-amber-dark);
        font-size: 12.5px;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
    }

    .track-panel h2 {
        font-size: 27px;
        font-weight: 700;
        margin: 5px 0 26px;
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
        border: 1px solid var(--ot-line);
        border-radius: 8px;
        padding: 0 16px;
        background: #fbfdfc;
        font-family: var(--ot-mono);
    }

    .track-form input[type="text"]:focus {
        border-color: var(--ot-green);
        box-shadow: 0 0 0 3px rgba(14, 143, 122, .12);
        outline: none;
        background: #fff;
    }

    .track-alert {
        border-radius: 10px;
        padding: 14px 16px;
        margin-bottom: 20px;
        font-size: 14px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 9px;
    }

    .track-alert svg { width: 16px; height: 16px; stroke: currentColor; flex: none; }

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
        padding-bottom: 22px;
        border-bottom: 1px solid var(--ot-line);
    }

    .track-result-head h4 {
        font-size: 19px;
        font-weight: 600;
        margin: 0 0 5px;
    }

    .track-result-head p {
        color: var(--ot-muted);
        font-family: var(--ot-mono);
        font-size: 13px;
        margin: 0;
    }

    .track-status-actions {
        text-align: right;
    }

    .status-badge {
        border-radius: 999px;
        display: inline-block;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .04em;
        padding: 6px 14px;
        text-transform: uppercase;
    }

    .status-badge.ongoing {
        align-items: center;
        background: var(--ot-gold-bg);
        color: var(--ot-gold);
        border: 1px solid var(--ot-gold-line);
        display: inline-flex;
        gap: 6px;
    }

    .status-badge .live-dot {
        background: currentColor;
        border-radius: 50%;
        display: inline-block;
        height: 6px;
        width: 6px;
    }

    @media (prefers-reduced-motion: no-preference) {
        .status-badge.ongoing .live-dot {
            animation: trackPulse 1.6s ease-in-out infinite;
        }
    }

    @keyframes trackPulse {
        0%, 100% { opacity: 1; box-shadow: 0 0 0 0 rgba(181, 117, 12, .35); }
        50% { opacity: .55; box-shadow: 0 0 0 5px rgba(181, 117, 12, 0); }
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
        border-radius: 8px;
        color: #067647;
        display: inline-flex;
        font-family: var(--ot-display);
        font-size: 12.5px;
        font-weight: 600;
        gap: 6px;
        margin-top: 10px;
        padding: 7px 12px;
        text-decoration: none;
    }

    .track-invoice-btn svg { width: 13px; height: 13px; stroke: currentColor; }

    .track-invoice-btn:hover {
        background: #ecfdf3;
        color: #067647;
    }

    .track-invoice-note {
        color: var(--ot-muted);
        font-size: 12px;
        margin-top: 10px;
    }

    /* ===== Tracking stepper ===== */
    .tracking-line {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        position: relative;
        margin-bottom: 30px;
        padding-top: 6px;
    }

    .tracking-line::before {
        content: "";
        position: absolute;
        left: 12%;
        right: 12%;
        top: 19px;
        height: 3px;
        background: var(--ot-line);
    }

    .tracking-fill {
        background: linear-gradient(90deg, var(--ot-green-dark), var(--ot-green));
        border-radius: 2px;
        height: 3px;
        left: 12%;
        position: absolute;
        top: 19px;
        transition: width 1.1s cubic-bezier(.22, .9, .3, 1);
        width: 0%;
    }

    .tracking-pkg {
        align-items: center;
        background: #fff;
        border: 2px solid var(--ot-green);
        border-radius: 50%;
        box-shadow: 0 6px 14px rgba(14, 143, 122, .28);
        display: flex;
        height: 24px;
        justify-content: center;
        left: 12%;
        position: absolute;
        top: 7px;
        transform: translateX(-50%);
        transition: left 1.1s cubic-bezier(.22, .9, .3, 1);
        width: 24px;
        z-index: 2;
    }

    .tracking-pkg svg { width: 13px; height: 13px; stroke: var(--ot-green-dark); }

    .tracking-line.rejected .tracking-fill,
    .tracking-line.rejected .tracking-pkg {
        display: none;
    }

    .tracking-step {
        position: relative;
        text-align: center;
        z-index: 1;
    }

    .tracking-step span {
        background: var(--ot-line);
        border: 4px solid #fff;
        border-radius: 50%;
        box-shadow: 0 0 0 1px var(--ot-line);
        display: inline-block;
        height: 24px;
        margin-bottom: 10px;
        transition: background-color .2s ease, box-shadow .2s ease;
        width: 24px;
    }

    .tracking-step strong {
        color: var(--ot-muted);
        display: block;
        font-size: 12px;
        font-weight: 700;
        transition: color .2s ease;
    }

    .tracking-step.active span {
        background: var(--ot-green);
        box-shadow: 0 0 0 1px var(--ot-green);
    }

    .tracking-step.active strong {
        color: var(--ot-ink);
    }

    .tracking-line.rejected::before {
        background: var(--ot-red);
    }

    .tracking-line.rejected .tracking-step span {
        background: var(--ot-red);
        box-shadow: 0 0 0 1px var(--ot-red);
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
        gap: 10px;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        margin-bottom: 20px;
    }

    .track-meta {
        border-left: 2px solid var(--ot-line);
        padding: 2px 0 2px 12px;
    }

    .track-meta span {
        color: var(--ot-muted);
        display: block;
        font-size: 11px;
        letter-spacing: .04em;
        margin-bottom: 4px;
        text-transform: uppercase;
    }

    .track-meta strong {
        display: block;
        font-size: 13.5px;
        font-weight: 700;
    }

    .track-summary {
        background: var(--ot-panel-tint);
        border: 1px solid rgba(14, 143, 122, .2);
        border-radius: 12px;
        margin-bottom: 20px;
        padding: 18px 20px;
    }

    .track-summary-line {
        align-items: center;
        color: var(--ot-ink-soft);
        display: flex;
        font-size: 13.5px;
        justify-content: space-between;
        padding: 6px 0;
    }

    .track-summary-line + .track-summary-line {
        border-top: 1px dashed rgba(14, 143, 122, .2);
    }

    .track-summary-line strong {
        font-family: var(--ot-mono);
        color: var(--ot-ink);
    }

    .track-summary-line.total {
        margin-top: 4px;
        padding-top: 14px;
    }

    .track-summary-line.total strong {
        font-size: 19px;
        color: var(--ot-green-dark);
    }

    .recent-heading {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 15px;
        font-weight: 600;
        margin-bottom: 14px;
    }

    .recent-list {
        display: grid;
        gap: 10px;
    }

    .recent-item {
        align-items: center;
        background: var(--ot-panel);
        border: 1px solid var(--ot-line);
        border-radius: 12px;
        display: flex;
        gap: 14px;
        justify-content: space-between;
        padding: 14px 16px;
        text-decoration: none;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .recent-item:hover {
        border-color: rgba(14, 143, 122, .35);
        box-shadow: var(--ot-shadow-sm);
    }

    .recent-item-left {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .recent-item-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: var(--ot-panel-tint);
        border: 1px solid var(--ot-line);
        display: grid;
        place-items: center;
        flex: none;
    }

    .recent-item-icon svg { width: 18px; height: 18px; stroke: var(--ot-green-dark); }

    .recent-item strong {
        display: block;
        font-size: 14px;
        overflow-wrap: anywhere;
    }

    .recent-item small {
        color: var(--ot-muted);
        font-family: var(--ot-mono);
        font-size: 11.5px;
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

<svg style="position:absolute; width:0; height:0; overflow:hidden" aria-hidden="true">
    <symbol id="tk-search" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"></circle><path d="M21 21l-4.3-4.3"></path></symbol>
    <symbol id="tk-alert" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4M12 17h.01"></path><circle cx="12" cy="12" r="9"></circle></symbol>
    <symbol id="tk-info" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 16v-4M12 8h.01"></path><circle cx="12" cy="12" r="9"></circle></symbol>
    <symbol id="tk-download" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12M7 10l5 5 5-5"></path><path d="M4 19h16"></path></symbol>
    <symbol id="tk-box" viewBox="0 0 24 24" fill="none" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8l9-4.5L21 8l-9 4.5z"></path><path d="M3 8v9l9 4.5V12.5"></path><path d="M21 8v9l-9 4.5"></path></symbol>
    <symbol id="tk-clock" viewBox="0 0 24 24" fill="none" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8.5"></circle><path d="M12 7.5V12l3 2"></path></symbol>
</svg>

<section class="track-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-9">
                <div class="track-panel">
                    <span class="track-eyebrow">Track &amp; Trace</span>
                    <h2>Track Shipment</h2>

                    @if(session('error'))
                        <div class="track-alert track-alert-danger"><svg viewBox="0 0 24 24"><use href="#tk-alert"></use></svg> {{ session('error') }}</div>
                    @endif

                    <form method="GET" action="{{ route('shipment.track') }}" class="track-form">
                        <input type="text" name="tracking_number" value="{{ $trackingNumber }}" placeholder="Enter tracking number">
                        <button type="submit" class="primary-btn1 btn-hover">
                            Track
                            <span></span>
                        </button>
                    </form>

                    @if($trackingNumber && !$lead)
                        <div class="track-alert track-alert-danger"><svg viewBox="0 0 24 24"><use href="#tk-search"></use></svg> No shipment found for this tracking number.</div>
                    @endif

                    @if($lead)
                        @php
                            $isRejected = in_array($lead->admin_status, ['rejected', 'cancelled']);
                            $isDelivered = $lead->admin_status === 'delivered';

                            $badgeClass = match(true) {
                                $isRejected  => 'stopped',
                                $isDelivered => 'done',
                                default      => 'ongoing',
                            };
                        @endphp

                        <div class="track-result-head">
                            <div>
                                <h4>{{ $lead->item_name }}</h4>
                                <p>{{ $lead->tracking_number }}</p>
                            </div>
                            <div class="track-status-actions">
                                <span class="status-badge {{ $badgeClass }}">
                                    @if($badgeClass === 'ongoing')<span class="live-dot"></span>@endif
                                    {{ ucfirst($lead->admin_status) }}
                                </span>
                                @if($isDelivered)
                                    <div>
                                        <a class="track-invoice-btn" href="{{ route('shipment.invoice.download', $lead->tracking_number) }}">
                                            <svg viewBox="0 0 24 24"><use href="#tk-download"></use></svg> Download Invoice
                                        </a>
                                    </div>
                                @else
                                    <div class="track-invoice-note">Invoice available after delivery</div>
                                @endif
                            </div>
                        </div>

                        @if($isRejected)
                            <div class="track-alert track-alert-danger">
                                <svg viewBox="0 0 24 24"><use href="#tk-alert"></use></svg>
                                <span>This shipment has been {{ strtolower($lead->admin_status) }}.</span>
                            </div>
                        @endif

                        <div class="tracking-line {{ $isRejected ? 'rejected' : '' }}" id="trackingLine">
                            @unless($isRejected)
                                <div class="tracking-fill" id="trackingFill" data-target-width="{{ $trackFillWidth }}"></div>
                                <div class="tracking-pkg" id="trackingPkg" data-target-left="{{ $trackPkgLeft }}"><svg viewBox="0 0 24 24"><use href="#tk-box"></use></svg></div>
                            @endunless
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
                            <!-- <div class="track-meta">
                                <span>Payment</span>
                                <strong>{{ ucfirst($lead->payment_status) }}</strong>
                            </div> -->
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
                            <div class="track-alert track-alert-info mb-0"><svg viewBox="0 0 24 24"><use href="#tk-info"></use></svg> {{ $lead->admin_description }}</div>
                        @endif
                    @elseif($userLeads->isNotEmpty())
                        <div class="recent-heading">Recent Shipments</div>
                        <div class="recent-list">
                            @foreach($userLeads as $item)
                                @php
                                    $itemBadge = match(true) {
                                        in_array($item->admin_status, ['rejected', 'cancelled']) => 'stopped',
                                        $item->admin_status === 'delivered' => 'done',
                                        default => 'ongoing',
                                    };
                                @endphp
                                <a class="recent-item" href="{{ route('shipment.track', ['tracking_number' => $item->tracking_number]) }}">
                                    <div class="recent-item-left">
                                        <span class="recent-item-icon"><svg viewBox="0 0 24 24"><use href="#tk-box"></use></svg></span>
                                        <div>
                                            <strong>{{ $item->item_name }}</strong>
                                            <small>{{ $item->tracking_number }}</small>
                                        </div>
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

<script>
    (function () {
        var fill = document.getElementById('trackingFill');
        var pkg = document.getElementById('trackingPkg');

        if (fill && pkg) {
            var targetWidth = fill.getAttribute('data-target-width') + '%';
            var targetLeft = pkg.getAttribute('data-target-left') + '%';

            requestAnimationFrame(function () {
                requestAnimationFrame(function () {
                    fill.style.width = targetWidth;
                    pkg.style.left = targetLeft;
                });
            });
        }
    })();
</script>

@include('web.footer')
