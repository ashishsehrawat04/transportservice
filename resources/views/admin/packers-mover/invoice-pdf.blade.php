<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice {{ $payment?->invoice_number ?: $lead->tracking_number }}</title>
    <style>
        @page { margin: 28px 32px; }
        * { box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; color:#1f2937; font-size:12px; }

        .sheet { border:1px solid #e6eaf0; border-radius:6px; overflow:hidden; }

        .invoice-header { background:#152238; color:#fff; padding:22px 26px; }
        .invoice-header table { width:100%; border-collapse:collapse; }
        .invoice-header td { border:none; padding:0; vertical-align:top; }
        .brand-name { font-size:20px; font-weight:700; color:#fff; margin:0; }
        .brand-tagline { font-size:10px; color:#9fb0c9; margin-top:2px; }
        .invoice-title { font-size:18px; font-weight:700; text-align:right; color:#fff; }
        .invoice-meta { text-align:right; font-size:11px; color:#cfd8e3; margin-top:6px; line-height:1.6; }
        .invoice-meta strong { color:#fff; }

        .body-pad { padding:22px 26px; }

        .grid-2 table { width:100%; border-collapse:collapse; }
        .grid-2 td { border:none; padding:0; vertical-align:top; width:50%; }

        .label { color:#6b7280; font-size:9.5px; text-transform:uppercase; letter-spacing:.4px; margin-bottom:5px; }
        .value { font-weight:700; font-size:13px; color:#111827; }
        .muted { color:#4b5563; }

        .section { margin-top:18px; }

        table.items { width:100%; border-collapse:collapse; margin-top:8px; }
        table.items th { background:#f3f5f9; color:#374151; font-size:10px; text-transform:uppercase; letter-spacing:.3px; text-align:left; padding:8px 10px; border:1px solid #e8edf3; }
        table.items td { padding:8px 10px; border:1px solid #e8edf3; font-size:11.5px; }
        table.items td.right, table.items th.right { text-align:right; }

        .totals { width:100%; border-collapse:collapse; margin-top:10px; }
        .totals td { padding:6px 10px; font-size:12px; }
        .totals .t-label { text-align:right; color:#6b7280; width:80%; }
        .totals .t-value { text-align:right; font-weight:700; width:20%; white-space:nowrap; }
        .totals .grand td { border-top:2px solid #152238; padding-top:10px; font-size:14px; }
        .totals .grand .t-value { color:#152238; }

        .badge { display:inline-block; padding:3px 10px; border-radius:12px; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.3px; }
        .badge-paid { background:#e2f6e9; color:#0f8a3f; }
        .badge-partial { background:#fef3d6; color:#b7791f; }
        .badge-unpaid { background:#fde3e3; color:#c22a2a; }
        .badge-refunded { background:#e7e9ee; color:#4b5563; }

        .payment-box table { width:100%; border-collapse:collapse; }
        .payment-box td { border:none; padding:0; vertical-align:top; }

        .footer-note { margin-top:26px; padding-top:14px; border-top:1px solid #e6eaf0; font-size:10.5px; color:#6b7280; text-align:center; line-height:1.6; }
    </style>
</head>
<body>
<div class="sheet">
    <div class="invoice-header">
        <table>
            <tr>
                <td>
                    <div class="brand-name">OneTrack</div>
                    <div class="brand-tagline">Smart Transport &amp; Shipment Tracking</div>
                </td>
                <td>
                    <div class="invoice-title">TAX INVOICE</div>
                    <div class="invoice-meta">
                        Invoice No: <strong>{{ $payment?->invoice_number ?: 'Pending' }}</strong><br>
                        Tracking No: <strong>{{ $lead->tracking_number ?: '-' }}</strong><br>
                        Date: <strong>{{ now()->format('d M Y') }}</strong>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="body-pad">
        <div class="grid-2">
            <table>
                <tr>
                    <td>
                        <div class="label">Billed To</div>
                        <div class="value">{{ optional($lead->user)->name ?: '-' }}</div>
                        <div class="muted">{{ optional($lead->user)->email ?: '-' }}</div>
                        <div class="muted">{{ optional($lead->user)->mobile ?: '-' }}</div>
                    </td>
                    <td>
                        <div class="label">Packers &amp; Movers Branch</div>
                        <div class="value">{{ optional($lead->packersMover)->name ?: '-' }}</div>
                        <div class="muted">{{ optional($lead->packersMover)->city ?: '-' }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="label">Item Details</div>
            <table class="items">
                <thead>
                <tr>
                    <th>Item</th>
                    <th>Dimensions</th>
                    <th>Weight</th>
                    <th>Distance</th>
                    <th>Charge Basis</th>
                    <th class="right">Amount</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>{{ $lead->item_name ?: '-' }}<br><small class="muted">{{ $lead->item_type ?: '-' }}</small></td>
                    <td>{{ number_format((float) $lead->length_cm, 2) }} x {{ number_format((float) $lead->width_cm, 2) }} x {{ number_format((float) $lead->height_cm, 2) }} CM</td>
                    <td>{{ number_format((float) $lead->weight_kg, 2) }} KG</td>
                    <td>{{ number_format((float) $lead->distance_km, 1) }} KM</td>
                    <td>{{ ucfirst($calculationType) }}</td>
                    <td class="right">{{ number_format((float) $lead->subtotal, 2) }}</td>
                </tr>
                </tbody>
            </table>

            <table class="totals">
                <tr>
                    <td class="t-label">Minimum Charge</td>
                    <td class="t-value">{{ number_format((float) $lead->base_price, 2) }}</td>
                </tr>
                @if($calculationType === 'volume')
                    <tr>
                        <td class="t-label">Volume Charge</td>
                        <td class="t-value">{{ number_format((float) $lead->volume_charge, 2) }}</td>
                    </tr>
                @else
                    <tr>
                        <td class="t-label">Weight Charge</td>
                        <td class="t-value">{{ number_format((float) $lead->weight_charge, 2) }}</td>
                    </tr>
                @endif
                <tr>
                    <td class="t-label">Subtotal</td>
                    <td class="t-value">{{ number_format((float) $lead->subtotal, 2) }}</td>
                </tr>
                @if((float) $lead->tax_amount > 0)
                    <tr>
                        <td class="t-label">Tax</td>
                        <td class="t-value">+{{ number_format((float) $lead->tax_amount, 2) }}</td>
                    </tr>
                @endif
                @if((float) $lead->discount_amount > 0)
                    <tr>
                        <td class="t-label">Discount</td>
                        <td class="t-value">-{{ number_format((float) $lead->discount_amount, 2) }}</td>
                    </tr>
                @endif
                <tr class="grand">
                    <td class="t-label">Total Payable</td>
                    <td class="t-value">Rs. {{ number_format((float) $lead->total_payment, 2) }}</td>
                </tr>
            </table>
        </div>

        <div class="section payment-box">
            <table>
                <tr>
                    <td>
                        <div class="label">Payment Status</div>
                        @php
                            $statusClass = match($lead->payment_status) {
                                'paid' => 'badge-paid',
                                'partial' => 'badge-partial',
                                'refunded' => 'badge-refunded',
                                default => 'badge-unpaid',
                            };
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ ucfirst((string) $lead->payment_status) }}</span>
                    </td>
                    <td>
                        <div class="label">Payment Method</div>
                        <div class="value">{{ ucfirst(str_replace('_', ' ', (string) ($lead->payment_method ?: '-'))) }}</div>
                    </td>
                    <td>
                        <div class="label">Transaction ID</div>
                        <div class="value">{{ $lead->transaction_id ?: '-' }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer-note">
            This is a computer-generated invoice and does not require a signature.<br>
            Questions about this invoice? Contact us at <strong>support@onetrack.test</strong> or <strong>(+91) 90000 00001</strong>.
        </div>
    </div>
</div>
</body>
</html>
