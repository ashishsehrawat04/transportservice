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

<section style="padding: 110px 0 80px; background:#f7f7f7;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-9">
                <div style="background:#fff; border:1px solid #e7e7e7; border-radius:8px; padding:28px;">
                    <span style="color:#ff7a00; font-weight:600;">Track & Trace</span>
                    <h2 class="mb-4">Track Shipment</h2>

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form method="GET" action="{{ route('shipment.track') }}" class="row g-3 mb-4">
                        <div class="col-md-9">
                            <input type="text" name="tracking_number" class="form-control" value="{{ $trackingNumber }}" placeholder="Enter tracking number">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-success w-100">Track</button>
                        </div>
                    </form>

                    @if($trackingNumber && !$lead)
                        <div class="alert alert-danger">No shipment found for this tracking number.</div>
                    @endif

                    @if($lead)
                        @php
                            $isRejected = in_array($lead->admin_status, ['rejected', 'cancelled']);
                            $isDelivered = $lead->admin_status === 'delivered';

                            $badgeClass = match(true) {
                                $isRejected  => 'bg-danger',
                                $isDelivered => 'bg-success',
                                default      => 'bg-warning text-dark',
                            };
                        @endphp

                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
                            <div>
                                <h4 class="mb-1">{{ $lead->item_name }}</h4>
                                <p class="mb-0">{{ $lead->tracking_number }}</p>
                            </div>
                            <div class="text-end">
                                <span class="badge {{ $badgeClass }}">{{ ucfirst($lead->admin_status) }}</span>
                                @if($isDelivered)
                                    <div class="mt-2">
                                        <a class="btn btn-sm btn-outline-success" href="{{ route('shipment.invoice.download', $lead->tracking_number) }}">
                                            Download Invoice
                                        </a>
                                    </div>
                                @else
                                    <div class="small text-muted mt-2">Invoice available after delivery</div>
                                @endif
                            </div>
                        </div>

                        @if($isRejected)
                            <div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
                                <i class="bi bi-x-circle-fill"></i>
                                <span>This shipment has been {{ strtolower($lead->admin_status) }}.</span>
                            </div>
                        @endif

                        <div class="tracking-line mb-4 {{ $isRejected ? 'rejected' : '' }}">
                            @foreach($steps as $key => $label)
                                @php $index = $loop->index; @endphp
                                <div class="tracking-step {{ !$isRejected && $current >= $index ? 'active' : '' }}">
                                    <span></span>
                                    <strong>{{ $label }}</strong>
                                </div>
                            @endforeach
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">From: <strong>{{ optional($lead->cityRoute)->from_city }}</strong></div>
                            <div class="col-md-6 mb-3">To: <strong>{{ optional($lead->cityRoute)->to_city }}</strong></div>
                            <div class="col-md-6 mb-3">Pickup: <strong>{{ optional($lead->confirmed_pickup_date ?: $lead->requested_pickup_date)->format('d M Y') }}</strong></div>
                            <div class="col-md-6 mb-3">Expected Delivery: <strong>{{ optional($lead->expected_delivery_date)->format('d M Y') ?? '-' }}</strong></div>
                            <div class="col-md-6 mb-3">Actual Delivery: <strong>{{ optional($lead->actual_delivery_date)->format('d M Y') ?? '-' }}</strong></div>
                            <div class="col-md-6 mb-3">Payment: <strong>{{ ucfirst($lead->payment_status) }}</strong></div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered mb-0">
                                        <tbody>
                                            @if($hasDiscount)
                                                <tr>
                                                    <th>Subtotal</th>
                                                    <td>{{ number_format($lead->subtotal, 2) }}</td>
                                                    <th>Tax Amount</th>
                                                    <td>{{ number_format($lead->tax_amount, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Discount Amount</th>
                                                    <td>{{ number_format($lead->discount_amount, 2) }}</td>
                                                    <th>Total Payable</th>
                                                    <td><strong>{{ number_format($lead->total_payment, 2) }}</strong></td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <th>Amount</th>
                                                    <td><strong>{{ number_format($lead->total_payment, 2) }}</strong></td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        @if($lead->admin_description)
                            <div class="alert alert-info mb-0">{{ $lead->admin_description }}</div>
                        @endif
                    @elseif($userLeads->isNotEmpty())
                        <h5 class="mb-3">Recent Shipments</h5>
                        <div class="list-group">
                            @foreach($userLeads as $item)
                                <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="{{ route('shipment.track', ['tracking_number' => $item->tracking_number]) }}">
                                    <span>{{ $item->item_name }} - {{ $item->tracking_number }}</span>
                                    <span class="badge bg-secondary">{{ ucfirst($item->admin_status) }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* ===== Tracking stepper ===== */
    .tracking-line {
        display: flex;
        align-items: flex-start;
        list-style: none;
        padding: 10px 0 0;
    }

    .tracking-step {
        flex: 1;
        position: relative;
        text-align: center;
        padding-top: 0;
    }

    /* connector line sitting behind each step (except the first) */
    .tracking-step:not(:first-child)::before {
        content: '';
        position: absolute;
        top: 9px;
        left: -50%;
        width: 100%;
        height: 3px;
        background: #e2e2e2;
        z-index: 1;
        transition: background-color .25s ease;
    }

    .tracking-step span {
        display: block;
        width: 20px;
        height: 20px;
        margin: 0 auto 10px;
        border-radius: 50%;
        background: #e2e2e2;
        border: 3px solid #fff;
        box-shadow: 0 0 0 2px #e2e2e2;
        position: relative;
        z-index: 2;
        transition: background-color .25s ease, box-shadow .25s ease;
    }

    .tracking-step strong {
        display: block;
        font-size: 12.5px;
        font-weight: 500;
        color: #9a9a9a;
        transition: color .25s ease;
    }

    /* ----- progressed / active state (green) ----- */
    .tracking-step.active span {
        background: #28a745;
        box-shadow: 0 0 0 2px #28a745;
    }

    .tracking-step.active:not(:first-child)::before {
        background: #28a745;
    }

    .tracking-step.active strong {
        color: #28a745;
        font-weight: 700;
    }

    /* ----- rejected / cancelled state: whole line red ----- */
    .tracking-line.rejected .tracking-step span {
        background: #dc3545;
        box-shadow: 0 0 0 2px #dc3545;
    }

    .tracking-line.rejected .tracking-step:not(:first-child)::before {
        background: #dc3545;
    }

    .tracking-line.rejected .tracking-step strong {
        color: #dc3545;
        font-weight: 600;
    }

    @media (max-width: 576px) {
        .tracking-step strong {
            font-size: 10.5px;
        }
    }
</style>

<style>
    .tracking-line {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        position: relative;
    }
    .tracking-line:before {
        content: "";
        position: absolute;
        left: 8%;
        right: 8%;
        top: 13px;
        height: 5px;
        background: #d8ead8;
    }
    .tracking-step {
        text-align: center;
        position: relative;
        z-index: 1;
    }
    .tracking-step span {
        width: 30px;
        height: 30px;
        display: inline-block;
        border-radius: 50%;
        background: #d8ead8;
        border: 4px solid #fff;
        box-shadow: 0 0 0 1px #d8ead8;
        margin-bottom: 8px;
    }
    .tracking-step.active span {
        background: #198754;
        box-shadow: 0 0 0 1px #198754;
    }
    .tracking-step strong {
        display: block;
        font-size: 13px;
    }
</style>

@include('web.footer')
