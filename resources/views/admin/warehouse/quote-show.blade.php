@extends('admin.Layout')

@section('content')
@php
    $user = $quote->user;
    $quoteNumber = $quote->invoice_number ?: 'WQT-' . str_pad((string) $quote->id, 6, '0', STR_PAD_LEFT);
    $userAddress = collect([
        $user->address_line_1 ?? null,
        $user->address_line_2 ?? null,
        $user->city ?? null,
        $user->state ?? null,
        $user->country ?? null,
        $user->pincode ?? null,
    ])->filter()->join(', ');
    $pickupAddress = $warehouseAddress->pickup_address ?? $userAddress;
    $destinationAddress = collect([$quote->warehouse_name, $quote->warehouse_address ?: $quote->warehouse_city])->filter()->join(', ');

    $itemRows = $quote->quote_data['warehouse_items'] ?? null;
    if (empty($itemRows)) {
        $itemRows = [[
            'item_name' => $quote->item_name,
            'item_type' => $quote->item_type,
            'quantity' => $quote->quantity,
            'length_cm' => $quote->length_cm,
            'width_cm' => $quote->width_cm,
            'height_cm' => $quote->height_cm,
            'weight_kg' => $quote->weight_kg,
            'storage_days' => $quote->storage_days,
            'charge_basis' => $quote->calculation_type,
            'volumetric_weight_kg' => null,
            'estimated_total' => $quote->subtotal,
        ]];
    }
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
            <h4 class="mb-0">Warehouse Quote Details</h4>
            <small class="text-muted">{{ $quoteNumber }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.warehouse_leads') }}" class="btn btn-light">Back</a>
            <a href="{{ route('admin.warehouse_lead.quote.download', $quote->warehouse_lead_id) }}" class="btn btn-primary">Download</a>
        </div>
    </div>

    <div class="quote-sheet">
        <div class="quote-header d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h1 class="quote-title">Warehouse Storage Quote</h1>
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
            <div class="section-title">Pickup & Warehouse</div>
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
                        <div class="info-label">Warehouse</div>
                        <div class="info-value">{{ $destinationAddress ?: '-' }}</div>
                        <div class="info-label mt-3">Storage Days</div>
                        <div class="info-value">{{ $quote->storage_days ?: 1 }} Day{{ $quote->storage_days > 1 ? 's' : '' }}</div>
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
                            <th>Storage Days</th>
                            <th>Charge Basis</th>
                            <th class="text-end">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($itemRows as $row)
                            <tr>
                                <td>
                                    <strong>{{ $row['item_name'] ?: '-' }}</strong>
                                    <br><small>{{ $row['item_type'] ?: '-' }}</small>
                                </td>
                                <td>{{ $row['quantity'] ?: 1 }}</td>
                                <td>{{ number_format((float) ($row['weight_kg'] ?? 0), 2) }} KG</td>
                                <td>
                                    {{ number_format((float) ($row['length_cm'] ?? 0), 2) }} x
                                    {{ number_format((float) ($row['width_cm'] ?? 0), 2) }} x
                                    {{ number_format((float) ($row['height_cm'] ?? 0), 2) }} CM
                                </td>
                                <td>{{ $row['storage_days'] ?? 1 }}</td>
                                <td>{{ ucfirst($row['charge_basis'] ?? '-') }}</td>
                                <td class="text-end">{{ number_format((float) ($row['estimated_total'] ?? 0), 2) }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="6" class="text-end"><strong>Items Subtotal</strong></td>
                            <td class="text-end"><strong>{{ number_format((float) $quote->subtotal, 2) }}</strong></td>
                        </tr>
                        @if((float) $quote->tax_amount > 0)
                            <tr>
                                <td colspan="6" class="text-end">Tax</td>
                                <td class="text-end">+{{ number_format((float) $quote->tax_amount, 2) }}</td>
                            </tr>
                        @endif
                        @if((float) $quote->discount_amount > 0)
                            <tr>
                                <td colspan="6" class="text-end">Discount</td>
                                <td class="text-end">-{{ number_format((float) $quote->discount_amount, 2) }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td colspan="6" class="text-end"><strong>Total</strong></td>
                            <td class="text-end"><strong>{{ number_format((float) $quote->total_payment, 2) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
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
