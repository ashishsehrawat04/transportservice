<?php

namespace App\Services;

use App\Models\TransportQuote;

class TransportQuotePdfService
{
    public function output(TransportQuote $quote, ?object $transportAddress = null): string
    {
        $quote->loadMissing(['user', 'transportLead']);
        $user = $quote->user;
        $pickupAddress = $transportAddress?->pickup_address ?: $this->userAddress($user);
        $deliveryAddress = $transportAddress?->delivery_address ?: trim(collect([$quote->to_city, $user?->state, $user?->country])->filter()->join(', '));

        $calculationType = $this->calculationType($quote);
        $chargeLines = $this->chargeLines(
            $calculationType,
            (float) $quote->base_price,
            (float) $quote->volume_charge,
            (float) $quote->distance_charge,
            (float) $quote->subtotal,
            (float) $quote->total_payment
        );

        $lines = [
            'Transport Quote',
            'Quote No: ' . ($quote->invoice_number ?: 'QT-' . str_pad((string) $quote->id, 6, '0', STR_PAD_LEFT)),
            'Tracking No: ' . ($quote->tracking_number ?: '-'),
            'Date: ' . optional($quote->created_at)->format('d M Y'),
            '',
            'Customer Details',
            'Name: ' . ($quote->customer_name ?: $user?->name ?: '-'),
            'Email: ' . ($quote->customer_email ?: $user?->email ?: '-'),
            'Mobile: ' . ($quote->customer_mobile ?: $user?->mobile ?: '-'),
            'User Address: ' . ($this->userAddress($user) ?: '-'),
            '',
            'Pickup Address: ' . ($pickupAddress ?: '-'),
            'Delivery Address: ' . ($deliveryAddress ?: '-'),
            '',
            'Item List',
            'Item: ' . ($quote->item_name ?: '-'),
            'Type: ' . ($quote->item_type ?: '-'),
            'Quantity: ' . ($quote->quantity ?: 1),
            'Weight: ' . number_format((float) $quote->weight_kg, 2) . ' KG',
            'Dimensions: ' . $this->dimensions($quote),
            'Volume: ' . number_format((float) $quote->volume_cft, 2) . ' CFT',
            'Route: ' . (($quote->from_city ?: '-') . ' to ' . ($quote->to_city ?: '-')),
            'Distance: ' . number_format((float) $quote->distance_km, 2) . ' KM',
            '',
            'Charges',
            ...$chargeLines,
            '',
            'Status',
            'Admin Status: ' . ucfirst((string) $quote->admin_status),
            'User Status: ' . ucfirst((string) $quote->user_status),
            'Payment Status: ' . ucfirst((string) $quote->payment_status),
            '',
            'Notes: ' . ($quote->admin_description ?: $quote->special_instructions ?: '-'),
        ];

        return $this->buildPdf($lines);
    }

    private function userAddress(?object $user): string
    {
        if (! $user) {
            return '';
        }

        return collect([
            $user->address_line_1,
            $user->address_line_2,
            $user->city,
            $user->state,
            $user->country,
            $user->pincode,
        ])->filter()->join(', ');
    }

    private function dimensions(TransportQuote $quote): string
    {
        return number_format((float) $quote->length_cm, 2) . ' x '
            . number_format((float) $quote->width_cm, 2) . ' x '
            . number_format((float) $quote->height_cm, 2) . ' CM';
    }

    private function money($amount): string
    {
        return 'Rs. ' . number_format((float) $amount, 2);
    }

    private function calculationType(TransportQuote $quote): string
    {
        if (in_array($quote->calculation_type, ['distance', 'volume'], true)) {
            return $quote->calculation_type;
        }

        return (float) $quote->volume_charge > 0 && (float) $quote->distance_charge <= 0 ? 'volume' : 'distance';
    }

    private function chargeLines(string $calculationType, float $minCharge, float $volumeCharge, float $distanceCharge, float $subtotal, float $total): array
    {
        $lines = [
            'Calculation By: ' . ucfirst($calculationType),
            'Minimum Charge: ' . $this->money($minCharge),
        ];

        if ($calculationType === 'volume') {
            $lines[] = 'Volume Charge: ' . $this->money($volumeCharge);
        } else {
            $lines[] = 'Distance Charge: ' . $this->money($distanceCharge);
        }

        $lines[] = 'Subtotal: ' . $this->money($subtotal);
        $lines[] = 'Total Payable: ' . $this->money($total);

        return $lines;
    }

    private function buildPdf(array $lines): string
    {
        $content = "BT\n/F1 18 Tf\n45 800 Td\n";
        $first = true;
        $y = 800;

        foreach ($lines as $line) {
            if (! $first) {
                $content .= "0 -18 Td\n";
                $y -= 18;
            }

            if ($y < 45) {
                $content .= "ET\nBT\n/F1 11 Tf\n45 800 Td\n";
                $y = 800;
            }

            $fontSize = in_array($line, ['Transport Quote', 'Customer Details', 'Item List', 'Charges', 'Status'], true) ? 15 : 10;
            $content .= "/F1 {$fontSize} Tf\n(" . $this->escapeText($this->fitLine($line)) . ") Tj\n";
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

    private function fitLine(string $line): string
    {
        return strlen($line) > 95 ? substr($line, 0, 92) . '...' : $line;
    }

    private function escapeText(string $text): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }
}
