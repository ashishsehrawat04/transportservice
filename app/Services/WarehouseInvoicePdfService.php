<?php

namespace App\Services;

use App\Models\WarehouseLead;
use App\Models\WarehousePayment;

class WarehouseInvoicePdfService
{
    public function output(WarehouseLead $lead, ?WarehousePayment $payment = null): string
    {
        $lead->loadMissing(['user', 'warehouse']);
        $payment ??= $lead->payments()->latest()->first();
        $calculationType = $this->calculationType($lead);
        $chargeLines = $this->chargeLines(
            $calculationType,
            (float) $lead->base_price,
            (float) $lead->volume_charge,
            (float) $lead->weight_charge,
            (float) $lead->subtotal,
            (float) $lead->total_payment
        );

        $lines = [
            'Warehouse Storage Invoice',
            'Invoice No: ' . ($payment?->invoice_number ?: 'Pending'),
            'Tracking No: ' . ($lead->tracking_number ?: '-'),
            'Date: ' . now()->format('d M Y'),
            '',
            'Customer: ' . (optional($lead->user)->name ?: '-'),
            'Email: ' . (optional($lead->user)->email ?: '-'),
            '',
            'Item: ' . $lead->item_name,
            'Type: ' . ($lead->item_type ?: '-'),
            'Quantity: ' . $lead->quantity,
            'Warehouse: ' . (optional($lead->warehouse)->name ?: '-') . ' (' . (optional($lead->warehouse)->city ?: '-') . ')',
            'Storage Days: ' . $lead->storage_days,
            '',
            ...$chargeLines,
            '',
            'Payment Status: ' . ucfirst((string) $lead->payment_status),
            'Payment Method: ' . ucfirst(str_replace('_', ' ', (string) ($lead->payment_method ?: '-'))),
            'Transaction ID: ' . ($lead->transaction_id ?: '-'),
        ];

        return $this->buildPdf($lines);
    }

    private function buildPdf(array $lines): string
    {
        $content = "BT\n/F1 16 Tf\n50 790 Td\n";
        $first = true;

        foreach ($lines as $line) {
            if (!$first) {
                $content .= "0 -22 Td\n";
            }

            $fontSize = $line === 'Warehouse Storage Invoice' ? 18 : 11;
            $content .= "/F1 {$fontSize} Tf\n(" . $this->escapeText($line) . ") Tj\n";
            $first = false;
        }

        $content .= "ET";

        $objects = [
            "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n",
            "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n",
            "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>\nendobj\n",
            "4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n",
            "5 0 obj\n<< /Length " . strlen($content) . " >>\nstream\n{$content}\nendstream\nendobj\n",
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $object) {
            $offsets[] = strlen($pdf);
            $pdf .= $object;
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= str_pad((string) $offsets[$i], 10, '0', STR_PAD_LEFT) . " 00000 n \n";
        }

        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n{$xrefOffset}\n%%EOF";

        return $pdf;
    }

    private function escapeText(string $text): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }

    private function calculationType(WarehouseLead $lead): string
    {
        if (in_array($lead->calculation_type, ['weight', 'volume'], true)) {
            return $lead->calculation_type;
        }

        return (float) $lead->volume_charge > 0 && (float) $lead->weight_charge <= 0 ? 'volume' : 'weight';
    }

    private function chargeLines(string $calculationType, float $minCharge, float $volumeCharge, float $weightCharge, float $subtotal, float $total): array
    {
        $lines = [
            'Calculation By: ' . ucfirst($calculationType),
            'Minimum Charge: ' . number_format($minCharge, 2),
        ];

        if ($calculationType === 'volume') {
            $lines[] = 'Volume Charge: ' . number_format($volumeCharge, 2);
        } else {
            $lines[] = 'Weight Charge: ' . number_format($weightCharge, 2);
        }

        $lines[] = 'Subtotal: ' . number_format($subtotal, 2);
        $lines[] = 'Total Payable: ' . number_format($total, 2);

        return $lines;
    }
}
