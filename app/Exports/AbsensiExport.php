<?php

namespace App\Exports;

use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class AbsensiExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, WithStyles
{
    protected $request;
    protected $no = 0;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Absensi::with(['pesertaMagang', 'pesertaMagang.kedeputian']);

        if ($this->request->filled('search')) {
            $query->whereHas('pesertaMagang', function ($q) {
                $q->where('nama', 'like', '%' . $this->request->search . '%');
            });
        }

        if ($this->request->filled('filterKedeputian') && $this->request->filterKedeputian !== 'semua') {
            $query->whereHas('pesertaMagang', function ($q) {
                $q->where('kedeputian_id', $this->request->filterKedeputian);
            });
        }

        if ($this->request->filled('fromDate')) {
            $query->where('tanggal', '>=', $this->request->fromDate);
        }

        if ($this->request->filled('toDate')) {
            $query->where('tanggal', '<=', $this->request->toDate);
        }

        return $query->orderBy('tanggal', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'NO',
            'NAMA PESERTA',
            'KEDEPUTIAN',
            'TANGGAL',
            'KEHADIRAN',
            'JAM MASUK',
            'JAM PULANG',
            'TELAT MASUK',
            'PULANG CEPAT',
            'KETERANGAN',
        ];
    }

    private function menitKeJam(int $menit): string
    {
        if ($menit <= 0) return '-';
        $jam = floor($menit / 60);
        $sisaMenit = $menit % 60;
        return sprintf('%02d:%02d', $jam, $sisaMenit);
    }

    public function map($absensi): array
    {
        $this->no++;

        // Konversi string jam masuk/keluar ke Excel datetime serial number
        $jamMasukExcel = $absensi->jam_masuk ? $this->excelTimeValue($absensi->jam_masuk) : null;
        $jamPulangExcel = $absensi->jam_pulang ? $this->excelTimeValue($absensi->jam_pulang) : null;

        $menitTelat = $absensi->menit_telat ?? 0;
        $menitPulangCepat = $absensi->menit_pulang_cepat ?? 0;

        return [
            $this->no,
            $absensi->pesertaMagang->nama ?? '-',
            $absensi->pesertaMagang->kedeputian->nama ?? '-',
            $absensi->tanggal->format('d-m-Y'),
            $absensi->kehadiran ?? '-',
            $jamMasukExcel,
            $jamPulangExcel,
            $this->menitKeJam($menitTelat),
            $this->menitKeJam($menitPulangCepat),
            $absensi->keterangan ?? '-',
        ];
    }

    private function excelTimeValue(string $timeStr)
    {
        try {
            $time = \PhpOffice\PhpSpreadsheet\Shared\Date::stringToExcel($timeStr);
            return $time;
        } catch (\Exception $e) {
            return $timeStr; // fallback ke string kalau error
        }
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_TIME3,   // Kolom Jam Masuk (F)
            'G' => NumberFormat::FORMAT_TIME3,   // Kolom Jam Pulang (G)
            // Kolom Telat dan Pulang Cepat tetap teks biasa
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}