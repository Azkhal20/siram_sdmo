<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Barryvdh\DomPDF\Facade\Pdf;

class GenerateDummyPdf extends Command
{
    protected $signature = 'make:dummy-pdf';
    protected $description = 'Generate a dummy attendance PDF in BKN format';

    public function handle()
    {
        $html = "
        <html>
        <head>
            <style>
                body { font-family: sans-serif; font-size: 11px; }
                .header { margin-bottom: 20px; }
                table { width: 100%; border-collapse: collapse; }
                th, td { border: 1px solid #ccc; padding: 5px; text-align: left; }
                .page-break { page-break-after: always; }
            </style>
        </head>
        <body>
            <!-- PAGE 1 -->
            <div class='header'>
                <h3>LAPORAN PER PERIODE KEHADIRAN</h3>
                <p>NIP : 3310016403020001</p>
                <p>Nama : DEVI ANGGRAINI</p>
                <p>Unit Kerja : Direktorat Perencanaan Kebutuhan Aparatur Sipil Negara</p>
            </div>
            <table>
                <thead>
                    <tr><th>Tanggal</th><th>Kehadiran</th><th>Masuk</th><th>Pulang</th></tr>
                </thead>
                <tbody>
                    <tr><td>01-12-2025</td><td>TK</td><td></td><td></td></tr>
                    <tr><td>02-12-2025</td><td>TMDHM</td><td></td><td>16:34</td></tr>
                    <tr><td>03-12-2025</td><td>HN</td><td>07:22</td><td>16:32</td></tr>
                </tbody>
            </table>
            
            <div class='page-break'></div>

            <!-- PAGE 2 -->
            <div class='header'>
                <h3>LAPORAN PER PERIODE KEHADIRAN</h3>
                <p>NIP : 199901012024011001</p>
                <p>Nama : AHMAD RIFAI</p>
                <p>Unit Kerja : Biro Sumber Daya Manusia</p>
            </div>
            <table>
                <thead>
                    <tr><th>Tanggal</th><th>Kehadiran</th><th>Masuk</th><th>Pulang</th></tr>
                </thead>
                <tbody>
                    <tr><td>01-12-2025</td><td>HN</td><td>07:30</td><td>16:00</td></tr>
                    <tr><td>02-12-2025</td><td>TM</td><td>08:15</td><td>16:00</td></tr>
                </tbody>
            </table>
        </body>
        </html>";

        $pdf = Pdf::loadHTML($html);
        $pdf->save(public_path('dummy-absensi.pdf'));

        $this->info("Dummy PDF BKN format generated at: " . public_path('dummy-absensi.pdf'));
    }
}
