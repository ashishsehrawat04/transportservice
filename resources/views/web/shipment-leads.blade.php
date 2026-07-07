@include('web.header')

@php
    $statusSteps = ['pending', 'approved', 'dispatched', 'delivered'];
@endphp

<section style="padding: 110px 0 80px; background:#f7f7f7;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <span style="color:#ff7a45; font-weight:600;">Shipment</span>
                <h2 class="mb-0">My Leads</h2>
            </div>
            <a href="{{ route('shipment.track') }}" class="primary-btn1 btn-hover">
                Track & Trace
                <span></span>
            </a>
        </div>

        <div class="row gy-4">
            @forelse($leads as $lead)
                @php
                    $currentStep = match ($lead->admin_status) {
                        'approved', 'reviewed' => 1,
                        'dispatched' => 2,
                        'delivered' => 3,
                        'cancelled', 'rejected' => -1,
                        default => 0,
                    };
                @endphp
                <div class="col-lg-6">
                    <div style="background:#fff; border:1px solid #e7e7e7; border-radius:8px; padding:24px; height:100%;">
                        <div class="d-flex justify-content-between gap-3 mb-3">
                            <div>
                                <h5 class="mb-1">{{ $lead->item_name }}</h5>
                                <small>{{ $lead->tracking_number }}</small>
                            </div>
                            <span class="badge {{ in_array($lead->admin_status, ['approved', 'dispatched', 'delivered']) ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($lead->admin_status) }}
                            </span>
                        </div>

                        <p class="mb-2">{{ optional($lead->cityRoute)->from_city }} to {{ optional($lead->cityRoute)->to_city }}</p>
                        <p class="mb-2">Pickup: {{ optional($lead->confirmed_pickup_date ?: $lead->requested_pickup_date)->format('d M Y') }}</p>
                        <p class="mb-2">Expected Delivery: {{ optional($lead->expected_delivery_date)->format('d M Y') ?? '-' }}</p>
                        <p class="mb-3">Total: {{ number_format($lead->total_payment, 2) }}</p>

                        <div class="shipment-progress">
                            @foreach($statusSteps as $index => $step)
                                <div class="progress-step {{ $currentStep >= $index ? 'active' : '' }}">
                                    <span></span>
                                    <p>{{ ucfirst($step) }}</p>
                                </div>
                            @endforeach
                        </div>

                        @if($lead->admin_description)
                            <div class="alert alert-info mt-4 mb-0">{{ $lead->admin_description }}</div>
                        @endif

                        <a href="{{ route('shipment.track', ['tracking_number' => $lead->tracking_number]) }}" class="btn btn-outline-success btn-sm mt-3">View Tracking</a>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center" style="background:#fff; border:1px solid #e7e7e7; border-radius:8px; padding:40px;">
                        No shipment leads found.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>

<style>
    .shipment-progress {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0;
        position: relative;
        margin-top: 24px;
    }
    .shipment-progress:before {
        content: "";
        position: absolute;
        top: 11px;
        left: 8%;
        right: 8%;
        height: 4px;
        background: #d8ead8;
    }
    .progress-step {
        position: relative;
        text-align: center;
        z-index: 1;
    }
    .progress-step span {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        display: inline-block;
        background: #d8ead8;
        border: 3px solid #fff;
        box-shadow: 0 0 0 1px #d8ead8;
    }
    .progress-step.active span {
        background: #0e8f7a;
        box-shadow: 0 0 0 1px #0e8f7a;
    }
    .progress-step p {
        font-size: 12px;
        margin: 8px 0 0;
    }
</style>

@include('web.footer')
