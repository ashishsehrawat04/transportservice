<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body{font-family: Arial, Helvetica, sans-serif;color:#1f2937}
        .quote-sheet{background:#fff;border:1px solid #e6eaf0;border-radius:4px;padding:18px}
        .quote-header{background:#152238;color:#fff;padding:18px}
        .quote-title{font-size:20px;margin:0}
        .section{margin-top:12px}
        .label{color:#6b7280;font-size:11px;text-transform:uppercase;margin-bottom:6px}
        .value{font-weight:700}
        table{width:100%;border-collapse:collapse;margin-top:10px}
        th,td{border:1px solid #e8edf3;padding:8px;text-align:left}
        .right{text-align:right}
    </style>
</head>
<body>
<div class="quote-sheet">
    <div class="quote-header">
        <div style="display:flex;justify-content:space-between;align-items:center">
            <div>
                <div class="quote-title">Transport Quote</div>
                <div style="font-size:12px;color:#cfd8e3">Tracking: {{ $quote->tracking_number ?: '-' }}</div>
            </div>
            <div style="text-align:right">
                <div style="font-size:11px;color:#cfd8e3">Quote No.</div>
                <div style="font-weight:700">{{ $quote->invoice_number ?: ('QT-' . str_pad((string) $quote->id, 6, '0', STR_PAD_LEFT)) }}</div>
                <div style="font-size:12px;color:#cfd8e3">{{ optional($quote->created_at)->format('d M Y') }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="label">Customer</div>
        <div class="value">{{ $quote->customer_name ?: optional($quote->user)->name ?: '-' }}</div>
        <div>{{ $quote->customer_email ?: optional($quote->user)->email ?: '-' }} | {{ $quote->customer_mobile ?: optional($quote->user)->mobile ?: '-' }}</div>
    </div>

    <div class="section">
        <div class="label">Pickup Address</div>
        <div class="value">{{ $transportAddress?->pickup_address ?: (optional($quote->user)->address_line_1 ? collect([optional($quote->user)->address_line_1, optional($quote->user)->address_line_2, optional($quote->user)->city, optional($quote->user)->state, optional($quote->user)->country, optional($quote->user)->pincode])->filter()->join(', ') : '-') }}</div>
    </div>

    <div class="section">
        <div class="label">Items</div>
        <table>
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
                    <td>{{ $quote->item_name ?: '-' }}<br><small>{{ $quote->item_type ?: '-' }}</small></td>
                    <td>{{ $quote->quantity ?: 1 }}</td>
                    <td>{{ number_format((float)$quote->weight_kg,2) }} KG</td>
                    <td>{{ number_format((float)$quote->length_cm,2) }} x {{ number_format((float)$quote->width_cm,2) }} x {{ number_format((float)$quote->height_cm,2) }} CM</td>
                    <td>{{ number_format((float)$quote->volume_cft,2) }} CFT</td>
                    <td>{{ $quote->from_city ?: '-' }} to {{ $quote->to_city ?: '-' }} ({{ number_format((float)$quote->distance_km,2) }} KM)</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="label">Charges</div>
        <table>
            <tbody>
                <tr><td>Calculation By</td><td class="right">{{ ucfirst($quote->calculation_type ?: ((float)$quote->volume_charge > (float)$quote->weight_charge ? 'volume' : 'weight')) }}</td></tr>
                <tr><td>Minimum Charge</td><td class="right">{{ number_format((float)$quote->base_price,2) }}</td></tr>
                @if(in_array($quote->calculation_type, ['weight','mixed'], true))
                <tr><td>Weight Charge</td><td class="right">{{ number_format((float)$quote->weight_charge,2) }}</td></tr>
                @endif
                @if(in_array($quote->calculation_type, ['volume','mixed'], true))
                <tr><td>Volume Charge</td><td class="right">{{ number_format((float)$quote->volume_charge,2) }}</td></tr>
                @endif
                <tr><td>Subtotal</td><td class="right">{{ number_format((float)$quote->subtotal,2) }}</td></tr>
                <tr><td><strong>Total</strong></td><td class="right"><strong>{{ number_format((float)$quote->total_payment,2) }}</strong></td></tr>
            </tbody>
        </table>
    </div>

    <div style="margin-top:18px;font-size:12px;color:#6b7280">Notes: {{ $quote->admin_description ?: $quote->special_instructions ?: '-' }}</div>
</div>
</body>
</html>
