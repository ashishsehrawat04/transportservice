<?php

namespace App\Services;

use App\Models\PackersMoverLead;
use App\Models\PackersMoverPayment;

class PackersMoverInvoicePdfService
{
    public function output(PackersMoverLead $lead, ?PackersMoverPayment $payment = null): string
    {
        $lead->loadMissing(['user', 'packersMover']);
        $payment ??= $lead->payments()->latest()->first();
        $calculationType = $this->calculationType($lead);

        $html = view('admin.packers-mover.invoice-pdf', [
            'lead' => $lead,
            'payment' => $payment,
            'calculationType' => $calculationType,
        ])->render();

        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            try {
                return \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('A4')->output();
            } catch (\Throwable) {
                // fall through to native dompdf
            }
        }

        if (class_exists(\Dompdf\Dompdf::class)) {
            try {
                $dompdf = new \Dompdf\Dompdf();
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();

                return $dompdf->output();
            } catch (\Throwable) {
                // fall through to plain HTML
            }
        }

        return $html;
    }

    private function calculationType(PackersMoverLead $lead): string
    {
        if (in_array($lead->calculation_type, ['weight', 'volume'], true)) {
            return $lead->calculation_type;
        }

        return (float) $lead->volume_charge > 0 && (float) $lead->weight_charge <= 0 ? 'volume' : 'weight';
    }
}
