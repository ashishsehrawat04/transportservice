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
                <div class="quote-title">Packers &amp; Movers Quote</div>
                <div style="font-size:12px;color:#cfd8e3">Tracking: {{ $quote->tracking_number ?: '-' }}</div>
            </div>
            <div style="text-align:right">
                <div style="font-size:11px;color:#cfd8e3">Quote No.</div>
                <div style="font-weight:700">{{ $quote->invoice_number ?: ('PMQT-' . str_pad((string) $quote->id, 6, '0', STR_PAD_LEFT)) }}</div>
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
        <div class="value">{{ $packersMoverAddress?->pickup_address ?: (optional($quote->user)->address_line_1 ? collect([optional($quote->user)->address_line_1, optional($quote->user)->address_line_2, optional($quote->user)->city, optional($quote->user)->state, optional($quote->user)->country, optional($quote->user)->pincode])->filter()->join(', ') : '-') }}</div>
    </div>

    <div class="section">
        <div class="label">Drop Address</div>
        <div class="value">{{ $packersMoverAddress?->drop_address ?: '-' }}</div>
    </div>

    <div class="section">
        <div class="label">Branch</div>
        <div class="value">{{ collect([$quote->packers_mover_name, $quote->packers_mover_address ?: $quote->packers_mover_city])->filter()->join(', ') ?: '-' }}</div>
    </div>

    <div class="section">
        <div class="label">Distance</div>
        <div class="value">{{ number_format((float) $quote->distance_km, 1) }} KM</div>
    </div>

    @php
        $itemRows = $quote->quote_data['packers_mover_items'] ?? null;
        if (empty($itemRows)) {
            $itemRows = [[
                'item_name' => $quote->item_name,
                'item_type' => $quote->item_type,
                'quantity' => $quote->quantity,
                'length_cm' => $quote->length_cm,
                'width_cm' => $quote->width_cm,
                'height_cm' => $quote->height_cm,
                'weight_kg' => $quote->weight_kg,
                'charge_basis' => $quote->calculation_type,
                'estimated_total' => $quote->subtotal,
            ]];
        }
    @endphp

    <div class="section">
        <div class="label">Items</div>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Weight</th>
                    <th>Dimensions</th>
                    <th>Charge Basis</th>
                    <th class="right">Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($itemRows as $row)
                    <tr>
                        <td>{{ $row['item_name'] ?: '-' }}<br><small>{{ $row['item_type'] ?: '-' }}</small></td>
                        <td>{{ $row['quantity'] ?: 1 }}</td>
                        <td>{{ number_format((float) ($row['weight_kg'] ?? 0), 2) }} KG</td>
                        <td>{{ number_format((float) ($row['length_cm'] ?? 0), 2) }} x {{ number_format((float) ($row['width_cm'] ?? 0), 2) }} x {{ number_format((float) ($row['height_cm'] ?? 0), 2) }} CM</td>
                        <td>{{ ucfirst($row['charge_basis'] ?? '-') }}</td>
                        <td class="right">{{ number_format((float) ($row['estimated_total'] ?? 0), 2) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="5" class="right"><strong>Items Subtotal</strong></td>
                    <td class="right"><strong>{{ number_format((float) $quote->subtotal, 2) }}</strong></td>
                </tr>
                @if((float) $quote->tax_amount > 0)
                    <tr>
                        <td colspan="5" class="right">Tax</td>
                        <td class="right">+{{ number_format((float) $quote->tax_amount, 2) }}</td>
                    </tr>
                @endif
                @if((float) $quote->discount_amount > 0)
                    <tr>
                        <td colspan="5" class="right">Discount</td>
                        <td class="right">-{{ number_format((float) $quote->discount_amount, 2) }}</td>
                    </tr>
                @endif
                <tr>
                    <td colspan="5" class="right"><strong>Total</strong></td>
                    <td class="right"><strong>{{ number_format((float) $quote->total_payment, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div style="margin-top:18px;font-size:12px;color:#6b7280">Notes: {{ $quote->admin_description ?: $quote->special_instructions ?: '-' }}</div>
</div>
</body>
</html>
