@include('web.header')

@php
    $steps = [
        'pending' => 'Request Received',
        'approved' => 'Pickup Approved',
        'dispatched' => 'In Transit',
        'delivered' => 'Delivered',
    ];
@endphp

<style>
    .leads-section {
        padding: 110px 0 80px;
        background:
            radial-gradient(ellipse 900px 400px at 12% 0%, rgba(14, 143, 122, .05), transparent 60%),
            var(--ot-bg);
    }

    .leads-head {
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        justify-content: space-between;
        margin-bottom: 26px;
    }

    .leads-eyebrow {
        color: var(--ot-amber-dark);
        display: block;
        font-size: 12.5px;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
    }

    .leads-head h2 {
        font-size: 30px;
        font-weight: 700;
        margin: 5px 0 0;
    }

    .lead-card {
        background: var(--ot-panel);
        border: 1px solid var(--ot-line);
        border-radius: 16px;
        box-shadow: var(--ot-shadow-sm);
        height: 100%;
        overflow: hidden;
        transition: box-shadow .2s ease;
    }

    .lead-card:hover { box-shadow: var(--ot-shadow); }

    .lead-card-head {
        align-items: flex-start;
        display: flex;
        gap: 14px;
        justify-content: space-between;
        padding: 18px 20px;
        background: linear-gradient(180deg, var(--ot-panel-tint), transparent);
        border-bottom: 1px solid var(--ot-line);
    }

    .lead-card-title {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .lead-card-icon {
        width: 40px;
        height: 40px;
        border-radius: 11px;
        background: var(--ot-panel);
        border: 1px solid var(--ot-line);
        display: grid;
        place-items: center;
        flex: none;
    }

    .lead-card-icon svg { width: 20px; height: 20px; stroke: var(--ot-green-dark); }

    .lead-card-head h5 {
        font-size: 15.5px;
        font-weight: 600;
        margin: 0 0 3px;
        overflow-wrap: anywhere;
    }

    .lead-card-head small {
        color: var(--ot-muted);
        font-family: var(--ot-mono);
        font-size: 11.5px;
    }

    .status-badge {
        border-radius: 999px;
        display: inline-block;
        font-size: 10.5px;
        font-weight: 800;
        letter-spacing: .03em;
        padding: 5px 12px;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .status-badge.ongoing {
        background: var(--ot-gold-bg);
        color: var(--ot-gold);
        border: 1px solid var(--ot-gold-line);
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

    .lead-card-body {
        padding: 18px 20px 20px;
    }

    .lead-meta-grid {
        display: grid;
        gap: 10px;
        grid-template-columns: repeat(2, 1fr);
        margin-bottom: 16px;
    }

    .lead-meta {
        border-left: 2px solid var(--ot-line);
        padding: 1px 0 1px 10px;
    }

    .lead-meta span {
        color: var(--ot-muted);
        display: block;
        font-size: 10px;
        letter-spacing: .04em;
        margin-bottom: 3px;
        text-transform: uppercase;
    }

    .lead-meta strong {
        display: block;
        font-size: 13px;
        font-weight: 700;
    }

    .lead-total {
        align-items: baseline;
        display: flex;
        justify-content: space-between;
        margin-bottom: 18px;
        padding-bottom: 16px;
        border-bottom: 1px dashed var(--ot-line);
    }

    .lead-total span {
        color: var(--ot-muted);
        font-size: 12px;
        font-weight: 600;
    }

    .lead-total strong {
        font-family: var(--ot-mono);
        font-size: 19px;
        font-weight: 700;
        color: var(--ot-green-dark);
    }

    /* ===== Tracking stepper (mirrors track-and-trace) ===== */
    .lead-stepper {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        margin-bottom: 16px;
        position: relative;
    }

    .lead-stepper::before {
        background: var(--ot-line);
        content: "";
        height: 3px;
        left: 10%;
        position: absolute;
        right: 10%;
        top: 12px;
    }

    .lead-step {
        position: relative;
        text-align: center;
        z-index: 1;
    }

    .lead-step span {
        background: var(--ot-line);
        border: 3px solid #fff;
        border-radius: 50%;
        box-shadow: 0 0 0 1px var(--ot-line);
        display: inline-block;
        height: 18px;
        margin-bottom: 6px;
        width: 18px;
    }

    .lead-step strong {
        color: var(--ot-muted);
        display: block;
        font-size: 10px;
        font-weight: 700;
    }

    .lead-step.active span {
        background: var(--ot-green);
        box-shadow: 0 0 0 1px var(--ot-green);
    }

    .lead-step.active strong {
        color: var(--ot-ink);
    }

    .lead-stepper.rejected::before {
        background: var(--ot-red);
    }

    .lead-stepper.rejected .lead-step span {
        background: var(--ot-red);
        box-shadow: 0 0 0 1px var(--ot-red);
    }

    .lead-stepper.rejected .lead-step strong {
        color: #b42318;
    }

    .lead-note {
        background: #eaf6fc;
        border: 1px solid #bfe3f2;
        border-radius: 10px;
        color: #1d6687;
        font-size: 12.5px;
        margin-bottom: 14px;
        padding: 10px 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .lead-note svg { width: 14px; height: 14px; stroke: currentColor; flex: none; }

    .lead-note.danger {
        background: #fef3f2;
        border-color: #fecdca;
        color: #b42318;
    }

    .lead-track-link {
        align-items: center;
        border: 1px solid #abefc6;
        border-radius: 9px;
        color: #067647;
        display: inline-flex;
        font-family: var(--ot-display);
        font-size: 12.5px;
        font-weight: 600;
        gap: 7px;
        padding: 8px 14px;
        text-decoration: none;
    }

    .lead-track-link svg { width: 13px; height: 13px; stroke: currentColor; }

    .lead-track-link:hover {
        background: #ecfdf3;
        color: #067647;
    }

    .leads-empty {
        background: var(--ot-panel);
        border: 1px solid var(--ot-line);
        border-radius: 16px;
        box-shadow: var(--ot-shadow-sm);
        padding: 56px 24px;
        text-align: center;
    }

    .leads-empty i {
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

    .leads-empty h4 {
        font-weight: 600;
        margin-bottom: 6px;
    }

    .leads-empty p {
        color: var(--ot-muted);
        margin: 0 auto;
        max-width: 420px;
        font-size: 14px;
    }

    @media (max-width: 575px) {
        .leads-head {
            align-items: stretch;
            flex-direction: column;
        }
    }
</style>

<svg style="position:absolute; width:0; height:0; overflow:hidden" aria-hidden="true">
    <symbol id="lb-box" viewBox="0 0 24 24" fill="none" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8l9-4.5L21 8l-9 4.5z"></path><path d="M3 8v9l9 4.5V12.5"></path><path d="M21 8v9l-9 4.5"></path></symbol>
    <symbol id="lb-pin" viewBox="0 0 24 24" fill="none" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s6.5-6.2 6.5-11A6.5 6.5 0 1 0 5.5 10c0 4.8 6.5 11 6.5 11z"></path><circle cx="12" cy="10" r="2.2"></circle></symbol>
    <symbol id="lb-alert" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4M12 17h.01"></path><circle cx="12" cy="12" r="9"></circle></symbol>
    <symbol id="lb-info" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 16v-4M12 8h.01"></path><circle cx="12" cy="12" r="9"></circle></symbol>
</svg>

<section class="leads-section">
    <div class="container">
        <div class="leads-head">
            <div>
                <span class="leads-eyebrow">Shipment</span>
                <h2>Booking Requests</h2>
            </div>
            <a href="{{ route('shipment.track') }}" class="primary-btn1 btn-hover">
                Track & Trace
                <span></span>
            </a>
        </div>

        @if($leads->isEmpty())
            <div class="leads-empty">
                <i class="bi bi-box-seam"></i>
                <h4>No booking requests yet</h4>
                <p>Once you submit items from your cart, they'll show up here with live status.</p>
            </div>
        @else
            <div class="row gy-4">
                @foreach($leads as $lead)
                    @php
                        $isRejected = in_array($lead->admin_status, ['rejected', 'cancelled']);
                        $isDelivered = $lead->admin_status === 'delivered';
                        $currentStep = match ($lead->admin_status) {
                            'approved', 'reviewed' => 1,
                            'dispatched' => 2,
                            'delivered' => 3,
                            'cancelled', 'rejected' => -1,
                            default => 0,
                        };
                        $badgeClass = match(true) {
                            $isRejected  => 'stopped',
                            $isDelivered => 'done',
                            default      => 'ongoing',
                        };
                    @endphp
                    <div class="col-lg-6">
                        <div class="lead-card">
                            <div class="lead-card-head">
                                <div class="lead-card-title">
                                    <span class="lead-card-icon"><svg viewBox="0 0 24 24"><use href="#lb-box"></use></svg></span>
                                    <div>
                                        <h5>{{ $lead->item_name }}</h5>
                                        <small>{{ $lead->tracking_number }}</small>
                                    </div>
                                </div>
                                <span class="status-badge {{ $badgeClass }}">{{ ucfirst($lead->admin_status) }}</span>
                            </div>

                            <div class="lead-card-body">
                                <div class="lead-meta-grid">
                                    <div class="lead-meta">
                                        <span>Route</span>
                                        <strong>{{ optional($lead->cityRoute)->from_city ?? '-' }} &rarr; {{ optional($lead->cityRoute)->to_city ?? '-' }}</strong>
                                    </div>
                                    <div class="lead-meta">
                                        <span>Pickup</span>
                                        <strong>{{ optional($lead->confirmed_pickup_date ?: $lead->requested_pickup_date)->format('d M Y') }}</strong>
                                    </div>
                                    <div class="lead-meta">
                                        <span>Expected Delivery</span>
                                        <strong>{{ optional($lead->expected_delivery_date)->format('d M Y') ?? '-' }}</strong>
                                    </div>
                                    <div class="lead-meta">
                                        <span>Payment</span>
                                        <strong>{{ ucfirst($lead->payment_status) }}</strong>
                                    </div>
                                </div>

                                <div class="lead-total">
                                    <span>Total Payable</span>
                                    <strong>₹{{ number_format($lead->total_payment, 2) }}</strong>
                                </div>

                                @if($isRejected)
                                    <div class="lead-note danger">
                                        <svg viewBox="0 0 24 24"><use href="#lb-alert"></use></svg>
                                        This shipment has been {{ strtolower($lead->admin_status) }}.
                                    </div>
                                @else
                                    <div class="lead-stepper">
                                        @foreach($steps as $key => $label)
                                            <div class="lead-step {{ $currentStep >= $loop->index ? 'active' : '' }}">
                                                <span></span>
                                                <strong>{{ $label }}</strong>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                @if($lead->admin_description)
                                    <div class="lead-note"><svg viewBox="0 0 24 24"><use href="#lb-info"></use></svg> {{ $lead->admin_description }}</div>
                                @endif

                                <a href="{{ route('shipment.track', ['tracking_number' => $lead->tracking_number]) }}" class="lead-track-link">
                                    <svg viewBox="0 0 24 24"><use href="#lb-pin"></use></svg> View Tracking
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

@include('web.footer')
