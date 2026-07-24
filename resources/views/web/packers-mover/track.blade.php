@include('web.header')

@php
    $steps = [
        'pending' => 'Request Received',
        'approved' => 'Move Confirmed',
        'dispatched' => 'In Transit',
        'delivered' => 'Delivered',
    ];
@endphp

<style>
    .track-section {
        padding: 110px 0 80px;
        background:
            radial-gradient(ellipse 900px 400px at 88% 0%, rgba(255, 122, 69, .06), transparent 60%),
            var(--ot-bg);
    }

    .track-panel {
        background: var(--ot-panel);
        border: 1px solid var(--ot-line);
        border-radius: 16px;
        box-shadow: var(--ot-shadow);
        padding: 30px;
        margin-bottom: 24px;
    }

    .track-eyebrow {
        color: var(--ot-amber-dark);
        display: block;
        font-size: 12.5px;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
    }

    .track-panel h2 {
        font-size: 27px;
        font-weight: 700;
        margin: 5px 0 20px;
    }

    .track-form {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .track-form input {
        border: 1px solid var(--ot-line);
        border-radius: 8px;
        flex: 1;
        min-height: 48px;
        min-width: 220px;
        padding: 0 14px;
    }

    .lead-result {
        background: var(--ot-panel);
        border: 1px solid var(--ot-line);
        border-radius: 16px;
        box-shadow: var(--ot-shadow-sm);
        overflow: hidden;
    }

    .lead-result-head {
        align-items: flex-start;
        background: linear-gradient(180deg, var(--ot-panel-tint), transparent);
        border-bottom: 1px solid var(--ot-line);
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        justify-content: space-between;
        padding: 20px 24px;
    }

    .lead-result-body {
        padding: 20px 24px 24px;
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

    .status-badge.ongoing { background: var(--ot-gold-bg); color: var(--ot-gold); border: 1px solid var(--ot-gold-line); }
    .status-badge.done { background: #ecfdf3; color: #067647; border: 1px solid #abefc6; }
    .status-badge.stopped { background: #fef3f2; color: #b42318; border: 1px solid #fecdca; }

    .lead-meta-grid {
        display: grid;
        gap: 12px;
        grid-template-columns: repeat(4, 1fr);
        margin-bottom: 20px;
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

    .lead-stepper {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        margin-bottom: 20px;
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

    .lead-step.active span { background: var(--ot-green); box-shadow: 0 0 0 1px var(--ot-green); }
    .lead-step.active strong { color: var(--ot-ink); }

    .lead-stepper.rejected::before { background: var(--ot-red); }
    .lead-stepper.rejected .lead-step span { background: var(--ot-red); box-shadow: 0 0 0 1px var(--ot-red); }
    .lead-stepper.rejected .lead-step strong { color: #b42318; }

    .lead-total-row {
        align-items: center;
        border-top: 1px dashed var(--ot-line);
        display: flex;
        justify-content: space-between;
        padding-top: 16px;
    }

    .lead-total-row strong {
        color: var(--ot-green-dark);
        font-family: var(--ot-mono);
        font-size: 20px;
    }

    .recent-list-item {
        align-items: center;
        border: 1px solid var(--ot-line);
        border-radius: 12px;
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        padding: 14px 16px;
    }

    .recent-list-item a {
        color: var(--ot-green-dark);
        font-weight: 700;
        text-decoration: none;
    }
</style>

<section class="track-section">
    <div class="container">
        <div class="track-panel">
            <span class="track-eyebrow">Packers &amp; Movers</span>
            <h2>Track Your Move</h2>

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ route('packers_movers.track') }}" method="GET" class="track-form">
                <input type="text" name="tracking_number" placeholder="Enter your tracking number (e.g. PM-20260724-ABC123)" value="{{ $trackingNumber }}" required>
                <button type="submit" class="primary-btn1 btn-hover">
                    Track
                    <span></span>
                </button>
            </form>
        </div>

        @if($trackingNumber && !$lead)
            <div class="alert alert-warning">No move request found for tracking number <strong>{{ $trackingNumber }}</strong>.</div>
        @endif

        @if($lead)
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
            <div class="lead-result">
                <div class="lead-result-head">
                    <div>
                        <h5 class="mb-1">{{ $lead->item_name }}</h5>
                        <small class="text-muted">{{ $lead->tracking_number }}</small>
                    </div>
                    <span class="status-badge {{ $badgeClass }}">{{ ucfirst($lead->admin_status) }}</span>
                </div>
                <div class="lead-result-body">
                    <div class="lead-meta-grid">
                        <div class="lead-meta">
                            <span>Branch</span>
                            <strong>{{ optional($lead->packersMover)->name ?? '-' }} ({{ optional($lead->packersMover)->city ?? '-' }})</strong>
                        </div>
                        <div class="lead-meta">
                            <span>Pickup Date</span>
                            <strong>{{ optional($lead->requested_pickup_date)->format('d M Y') ?? '-' }}</strong>
                        </div>
                        <div class="lead-meta">
                            <span>Distance</span>
                            <strong>{{ number_format($lead->distance_km, 1) }} KM</strong>
                        </div>
                        <div class="lead-meta">
                            <span>Payment</span>
                            <strong>{{ ucfirst($lead->payment_status) }}</strong>
                        </div>
                    </div>

                    @if($isRejected)
                        <div class="alert alert-danger">This move request has been {{ strtolower($lead->admin_status) }}.</div>
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
                        <div class="alert alert-info">{{ $lead->admin_description }}</div>
                    @endif

                    <div class="lead-total-row">
                        <span>Total Payable</span>
                        <strong>₹{{ number_format($lead->total_payment, 2) }}</strong>
                    </div>

                    @if($isDelivered)
                        <div class="mt-3">
                            <a href="{{ route('packers_movers.invoice.download', $lead->tracking_number) }}" class="primary-btn1 btn-hover">
                                Download Invoice
                                <span></span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @elseif($userLeads->isNotEmpty())
            <h5 class="mb-3">Your Recent Move Requests</h5>
            @foreach($userLeads as $userLead)
                <div class="recent-list-item">
                    <div>
                        <a href="{{ route('packers_movers.track', ['tracking_number' => $userLead->tracking_number]) }}">{{ $userLead->tracking_number }}</a>
                        <div class="text-muted small">{{ $userLead->item_name }} &middot; {{ optional($userLead->packersMover)->name ?? '-' }}</div>
                    </div>
                    <span class="status-badge ongoing">{{ ucfirst($userLead->admin_status) }}</span>
                </div>
            @endforeach
        @endif
    </div>
</section>

@include('web.footer')
