<?php

namespace App\Services;

use App\Models\WarehouseQuote;

class WarehouseQuotePdfService
{
    /**
     * Return array with keys 'content' and 'mimetype'.
     * Falls back to plain HTML when PDF library is not available.
     *
     * @return array{content:string,mimetype:string}
     */
    public function output(WarehouseQuote $quote, ?object $warehouseAddress = null): array
    {
        $quote->loadMissing('user');
        $user = $quote->user;
        $pickupAddress = $warehouseAddress?->pickup_address ?: $this->userAddress($user);
        $destinationAddress = collect([$quote->warehouse_name, $quote->warehouse_address ?: $quote->warehouse_city])->filter()->join(', ');

        $calculationType = $this->calculationType($quote);
        $chargeLines = $this->chargeLines(
            $calculationType,
            (float) $quote->base_price,
            (float) $quote->weight_charge,
            (float) $quote->volume_charge,
            (float) $quote->subtotal,
            (float) $quote->total_payment
        );

        $lines = [
            'Warehouse Storage Quote',
            'Quote No: ' . ($quote->invoice_number ?: 'WQT-' . str_pad((string) $quote->id, 6, '0', STR_PAD_LEFT)),
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
            'Warehouse: ' . ($destinationAddress ?: '-'),
            '',
            'Item List',
            'Item: ' . ($quote->item_name ?: '-'),
            'Type: ' . ($quote->item_type ?: '-'),
            'Quantity: ' . ($quote->quantity ?: 1),
            'Weight: ' . number_format((float) $quote->weight_kg, 2) . ' KG',
            'Dimensions: ' . $this->dimensions($quote),
            'Volume: ' . number_format((float) $quote->volume_cft, 2) . ' CFT',
            'Storage Days: ' . ($quote->storage_days ?: 1),
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

        // Try to render a Blade view and convert to PDF if Dompdf (or the barryvdh wrapper) is available.
        $viewData = [
            'quote' => $quote,
            'warehouseAddress' => $warehouseAddress,
        ];

        $html = view('admin.warehouse.quote-pdf', $viewData)->render();

        // Prefer barryvdh/laravel-dompdf facade if present
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            try {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('A4');
                return ['content' => $pdf->output(), 'mimetype' => 'application/pdf'];
            } catch (\Throwable) {
                // fall through to dompdf native check
            }
        }

        // Native Dompdf
        if (class_exists(\Dompdf\Dompdf::class)) {
            try {
                $dompdf = new \Dompdf\Dompdf();
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();
                return ['content' => $dompdf->output(), 'mimetype' => 'application/pdf'];
            } catch (\Throwable) {
                // continue to fallback
            }
        }

        // Fallback: return HTML so user can download the exact HTML view. Controller will set proper headers.
        return ['content' => $html, 'mimetype' => 'text/html'];
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

    private function dimensions(WarehouseQuote $quote): string
    {
        return number_format((float) $quote->length_cm, 2) . ' x '
            . number_format((float) $quote->width_cm, 2) . ' x '
            . number_format((float) $quote->height_cm, 2) . ' CM';
    }

    private function money($amount): string
    {
        return 'Rs. ' . number_format((float) $amount, 2);
    }

    private function calculationType(WarehouseQuote $quote): string
    {
        if (in_array($quote->calculation_type, ['weight', 'volume', 'mixed'], true)) {
            return $quote->calculation_type;
        }

        return (float) $quote->volume_charge > (float) $quote->weight_charge ? 'volume' : 'weight';
    }

    private function chargeLines(string $calculationType, float $minCharge, float $weightCharge, float $volumeCharge, float $subtotal, float $total): array
    {
        $lines = [
            'Calculation By: ' . ucfirst($calculationType),
            'Minimum Charge: ' . $this->money($minCharge),
        ];

        if (in_array($calculationType, ['weight', 'mixed'], true)) {
            $lines[] = 'Weight Charge: ' . $this->money($weightCharge);
        }

        if (in_array($calculationType, ['volume', 'mixed'], true)) {
            $lines[] = 'Volume Charge: ' . $this->money($volumeCharge);
        }

        $lines[] = 'Subtotal: ' . $this->money($subtotal);
        $lines[] = 'Total Payable: ' . $this->money($total);

        return $lines;
    }
}
