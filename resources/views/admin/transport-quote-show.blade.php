@extends('admin.Layout')

@section('content')
@php
    $user = $quote->user;
    $quoteNumber = $quote->invoice_number ?: 'QT-' . str_pad((string) $quote->id, 6, '0', STR_PAD_LEFT);
    $userAddress = collect([
        $user->address_line_1 ?? null,
        $user->address_line_2 ?? null,
        $user->city ?? null,
        $user->state ?? null,
        $user->country ?? null,
        $user->pincode ?? null,
    ])->filter()->join(', ');
    $pickupAddress = $transportAddress->pickup_address ?? $userAddress;
    $deliveryAddress = $transportAddress->delivery_address ?? collect([
        $quote->to_city,
        $user->state ?? null,
        $user->country ?? null,
    ])->filter()->join(', ');
@endphp

<style>
    .quote-page {
        background: #f4f6f9;
        padding: 18px;
    }

    .quote-sheet {
        background: #fff;
        border: 1px solid #e6eaf0;
        border-radius: 8px;
        overflow: hidden;
    }

    .quote-header {
        background: #152238;
        color: #fff;
        padding: 28px;
    }

    .quote-title {
        font-size: 28px;
        font-weight: 700;
        margin: 0;
    }

    .quote-meta {
        color: #cfd8e3;
        margin: 4px 0 0;
    }

    .quote-section {
        padding: 24px 28px;
        border-bottom: 1px solid #edf0f4;
    }

    .section-title {
        color: #152238;
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 14px;
        text-transform: uppercase;
    }

    .info-label {
        color: #697386;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 4px;
        text-transform: uppercase;
    }

    .info-value {
        color: #1f2937;
        font-weight: 600;
        word-break: break-word;
    }

    .charge-card {
        border: 1px solid #e8edf3;
        border-radius: 8px;
        padding: 14px;
        background: #fbfcfe;
        height: 100%;
    }

    .total-box {
        background: #f0f7ff;
        border: 1px solid #cfe8ff;
        border-radius: 8px;
        padding: 18px;
    }

    .quote-table th {
        background: #f7f9fc;
        color: #4b5563;
        font-size: 12px;
        text-transform: uppercase;
    }
</style>

<div class="quote-page">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h4 class="mb-0">Quote Details</h4>
            <small class="text-muted">{{ $quoteNumber }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.transport_quotes') }}" class="btn btn-light">Back</a>
            <a href="{{ route('admin.transport_quotes.download', $quote->id) }}" class="btn btn-primary">Download</a>
        </div>
    </div>

    <div class="quote-sheet">
        <div class="quote-header d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h1 class="quote-title">Transport Quote</h1>
                <p class="quote-meta">Tracking: {{ $quote->tracking_number ?: '-' }}</p>
            </div>
            <div class="text-end">
                <div class="quote-meta">Quote No.</div>
                <h5 class="mb-1 text-white">{{ $quoteNumber }}</h5>
                <div class="quote-meta">{{ optional($quote->created_at)->format('d M Y') }}</div>
            </div>
        </div>

        <div class="quote-section">
            <div class="section-title">User Details</div>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="info-label">Name</div>
                    <div class="info-value">{{ $quote->customer_name ?: optional($user)->name ?: '-' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="info-label">Email</div>
                    <div class="info-value">{{ $quote->customer_email ?: optional($user)->email ?: '-' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="info-label">Mobile</div>
                    <div class="info-value">{{ $quote->customer_mobile ?: optional($user)->mobile ?: '-' }}</div>
                </div>
                <div class="col-12">
                    <div class="info-label">User Address</div>
                    <div class="info-value">{{ $userAddress ?: '-' }}</div>
                </div>
            </div>
        </div>

        <div class="quote-section">
            <div class="section-title">Pickup & Delivery</div>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="charge-card">
                        <div class="info-label">Pickup Address</div>
                        <div class="info-value">{{ $pickupAddress ?: '-' }}</div>
                        <div class="info-label mt-3">Pickup Date</div>
                        <div class="info-value">{{ optional($quote->requested_pickup_date)->format('d M Y') ?: '-' }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="charge-card">
                        <div class="info-label">Delivery Address</div>
                        <div class="info-value">{{ $deliveryAddress ?: '-' }}</div>
                        <div class="info-label mt-3">Expected Delivery</div>
                        <div class="info-value">{{ optional($quote->expected_delivery_date)->format('d M Y') ?: '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="quote-section">
            <div class="section-title">Item List</div>
            <div class="table-responsive">
                <table class="table table-bordered quote-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Weight</th>
                            <th>Dimensions</th>
                            <th>Volume</th>
                            <th>Route</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <strong>{{ $quote->item_name ?: '-' }}</strong>
                                <br><small>{{ $quote->item_type ?: '-' }}</small>
                            </td>
                            <td>{{ $quote->quantity ?: 1 }}</td>
                            <td>{{ number_format((float) $quote->weight_kg, 2) }} KG</td>
                            <td>
                                {{ number_format((float) $quote->length_cm, 2) }} x
                                {{ number_format((float) $quote->width_cm, 2) }} x
                                {{ number_format((float) $quote->height_cm, 2) }} CM
                            </td>
                            <td>{{ number_format((float) $quote->volume_cft, 2) }} CFT</td>
                            <td>
                                {{ $quote->from_city ?: '-' }} to {{ $quote->to_city ?: '-' }}
                                <br><small>{{ number_format((float) $quote->distance_km, 2) }} KM</small>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="quote-section">
            <div class="section-title">Charge Breakdown</div>
            @php
                $calculationType = in_array($quote->calculation_type, ['distance', 'volume'], true)
                    ? $quote->calculation_type
                    : (((float) $quote->volume_charge > 0 && (float) $quote->distance_charge <= 0) ? 'volume' : 'distance');
            @endphp
            <div class="row g-3">
                <div class="col-md-3 col-sm-6">
                    <div class="charge-card">
                        <div class="info-label">Calculation By</div>
                        <div class="info-value">{{ ucfirst($calculationType) }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="charge-card">
                        <div class="info-label">Minimum Charge</div>
                        <div class="info-value">{{ number_format((float) $quote->base_price, 2) }}</div>
                    </div>
                </div>
                @if($calculationType === 'volume')
                    <div class="col-md-3 col-sm-6">
                        <div class="charge-card">
                            <div class="info-label">Volume Charge</div>
                            <div class="info-value">{{ number_format((float) $quote->volume_charge, 2) }}</div>
                        </div>
                    </div>
                @else
                    <div class="col-md-3 col-sm-6">
                        <div class="charge-card">
                            <div class="info-label">Distance Charge</div>
                            <div class="info-value">{{ number_format((float) $quote->distance_charge, 2) }}</div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="row justify-content-end mt-4">
                <div class="col-lg-5">
                    <div class="total-box">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <strong>{{ number_format((float) $quote->subtotal, 2) }}</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fs-5">
                            <span>Total</span>
                            <strong>{{ number_format((float) $quote->total_payment, 2) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="quote-section">
            <div class="section-title">Status & Notes</div>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="info-label">Admin Status</div>
                    <span class="badge bg-info">{{ $quote->admin_status ?: '-' }}</span>
                </div>
                <div class="col-md-4">
                    <div class="info-label">User Status</div>
                    <span class="badge bg-primary">{{ $quote->user_status ?: '-' }}</span>
                </div>
                <div class="col-md-4">
                    <div class="info-label">Payment Status</div>
                    <span class="badge bg-success">{{ $quote->payment_status ?: '-' }}</span>
                </div>
                <div class="col-12">
                    <div class="info-label">Notes</div>
                    <div class="info-value">{{ $quote->admin_description ?: $quote->special_instructions ?: '-' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
