<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class DomPDFServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $pdf = app('dompdf.wrapper');
        $dompdf = $pdf->getDomPDF();

        $fontFile = public_path('fonts/SolaimanLipi.ttf');
        $fontFamily = 'SolaimanLipi';

        $dompdf->getFontMetrics()->registerFont(
            ['family' => $fontFamily, 'style' => 'normal', 'weight' => 'normal'],
            $fontFile
        );
    }
}
