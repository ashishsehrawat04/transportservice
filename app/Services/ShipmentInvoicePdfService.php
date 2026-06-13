<?php

namespace App\Services;

use App\Models\ShipmentPayment;
use App\Models\TransportLead;

class ShipmentInvoicePdfService
{
    public function output(TransportLead $lead, ?ShipmentPayment $payment = null): string
    {
        $lead->loadMissing(['user', 'cityRoute']);
        $payment ??= $lead->payments()->latest()->first();

        $lines = [
            'Transport Invoice',
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
            'Route: ' . (optional($lead->cityRoute)->from_city ?: '-') . ' to ' . (optional($lead->cityRoute)->to_city ?: '-'),
            '',
            'Base Price: ' . number_format((float) $lead->base_price, 2),
            'Weight Charge: ' . number_format((float) $lead->weight_charge, 2),
            'Volume Charge: ' . number_format((float) $lead->volume_charge, 2),
            'Distance Charge: ' . number_format((float) $lead->distance_charge, 2),
            'Subtotal: ' . number_format((float) $lead->subtotal, 2),
            'Tax Amount: ' . number_format((float) $lead->tax_amount, 2),
            'Discount Amount: ' . number_format((float) $lead->discount_amount, 2),
            'Total Payable: ' . number_format((float) $lead->total_payment, 2),
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

            $fontSize = $line === 'Transport Invoice' ? 18 : 11;
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
}
