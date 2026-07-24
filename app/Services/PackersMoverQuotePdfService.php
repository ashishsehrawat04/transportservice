<?php

namespace App\Services;

use App\Models\PackersMoverQuote;

class PackersMoverQuotePdfService
{
    /**
     * Return array with keys 'content' and 'mimetype'.
     * Falls back to plain HTML when PDF library is not available.
     *
     * @return array{content:string,mimetype:string}
     */
    public function output(PackersMoverQuote $quote, ?object $packersMoverAddress = null): array
    {
        $quote->loadMissing('user');

        $viewData = [
            'quote' => $quote,
            'packersMoverAddress' => $packersMoverAddress,
        ];

        $html = view('admin.packers-mover.quote-pdf', $viewData)->render();

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
}
