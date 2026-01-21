<?php

namespace App\Exports;

use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AbsensiExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $search;
    protected $filterKedeputian;
    protected $fromDate;
    protected $toDate;
    protected $no = 0;

    private $kodeKehadiranList = [
        'TMDHM', 'TMDHP',
        'TM1', 'TM2', 'TM3', 'TM',
        'PC1', 'PC2', 'PC3', 'PC',
        'HN', 'TK', 'DL', 'LJ', 'LN', 'S', 'I', 'C', 'K'
    ];

    public function __construct($search = '', $filterKedeputian = '', $fromDate = null, $toDate = null)
    {
        $this->search = $search;
        $this->filterKedeputian = $filterKedeputian;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    public function collection()
    {
        $query = Absensi::with(['pesertaMagang.kedeputian']);

        if (!empty($this->search)) {
            $query->whereHas('pesertaMagang', function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->filterKedeputian)) {
            $query->whereHas('pesertaMagang', function ($q) {
                $q->where('kedeputian_id', $this->filterKedeputian);
            });
        }

        if (!empty($this->fromDate)) {
            $query->whereDate('tanggal', '>=', $this->fromDate);
        }

        if (!empty($this->toDate)) {
            $query->whereDate('tanggal', '<=', $this->toDate);
        }

        return $query->orderBy('tanggal', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'NO',
            'NIP',
            'NAMA PESERTA',
            'KEDEPUTIAN',
            'UNIT KERJA ASAL',
            'TANGGAL',
            'KEHADIRAN',
            'JAM MASUK',
            'JAM PULANG',
            'TELAT MASUK',
            'PULANG CEPAT',
            'KETERANGAN',
        ];
    }

    public function map($absensi): array
    {
        $this->no++;

        $jamMasuk = $absensi->jam_masuk ? date('H:i', strtotime($absensi->jam_masuk)) : '-';
        $jamPulang = $absensi->jam_pulang ? date('H:i', strtotime($absensi->jam_pulang)) : '-';

        $menitTelat = $absensi->menit_telat ?? 0;
        $telatStr = $menitTelat > 0 ? sprintf('%02d:%02d', floor($menitTelat / 60), $menitTelat % 60) : '-';

        $menitPulangCepat = $absensi->menit_pulang_cepat ?? 0;
        $pulangCepatStr = $menitPulangCepat > 0 ? sprintf('%02d:%02d', floor($menitPulangCepat / 60), $menitPulangCepat % 60) : '-';

        $keterangan = $this->getKeterangan($absensi->kehadiran ?? 'HN');

        return [
            $this->no,
            $absensi->pesertaMagang->nomor_induk ?? '-',
            $absensi->pesertaMagang->nama ?? '-',
            $absensi->pesertaMagang->kedeputian->nama ?? '-',
            $absensi->pesertaMagang->unit_kerja_text ?? '-',
            $absensi->tanggal->format('d-m-Y'),
            $absensi->kehadiran ?? '-',
            $jamMasuk,
            $jamPulang,
            $telatStr,
            $pulangCepatStr,
            $keterangan,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF4472C4'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }

    private function getKeteranganKode(string $kode): string
    {
        return match (strtoupper(trim($kode))) {
            'TK' => 'Tanpa Keterangan',
            'TMDHM' => 'Tidak Absen Masuk',
            'TMDHP' => 'Tidak Absen Pulang',
            'TM' => 'Terlambat Masuk',
            'TM1' => 'Terlambat < 30 menit',
            'TM2' => 'Terlambat > 30 menit',
            'TM3' => 'Terlambat > 1 jam',
            'PC' => 'Pulang Cepat',
            'PC1' => 'Pulang Cepat < 30 menit',
            'PC2' => 'Pulang Cepat > 30 menit',
            'PC3' => 'Pulang Cepat > 1 jam',
            'HN' => 'Hadir Normal',
            'DL' => 'Dinas Luar',
            'LJ' => 'Libur Sabtu/Minggu',
            'LN' => 'Libur Nasional',
            'S' => 'Sakit',
            'I' => 'Izin',
            'C' => 'Cuti',
            'K' => 'Tanpa Keterangan',
            default => $kode,
        };
    }

    private function parseKodeGabungan(string $kodeGabungan): array
    {
        $kodeGabungan = strtoupper(trim($kodeGabungan));

        if (str_contains($kodeGabungan, '-')) {
            return array_map('trim', explode('-', $kodeGabungan));
        }

        $hasil = [];
        $sisaKode = $kodeGabungan;

        while (!empty($sisaKode)) {
            $found = false;
            foreach ($this->kodeKehadiranList as $kode) {
                if (str_starts_with($sisaKode, $kode)) {
                    $hasil[] = $kode;
                    $sisaKode = substr($sisaKode, strlen($kode));
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                if (!empty($sisaKode)) {
                    $hasil[] = $sisaKode;
                }
                break;
            }
        }

        return !empty($hasil) ? $hasil : [$kodeGabungan];
    }

    private function getKeterangan(string $kodeKehadiran): string
    {
        $kodeParts = $this->parseKodeGabungan($kodeKehadiran);

        if (count($kodeParts) === 1) {
            $ket = $this->getKeteranganKode($kodeParts[0]);
            return !empty($ket) ? $ket . '.' : '-';
        }

        $keteranganParts = [];
        foreach ($kodeParts as $kode) {
            $ket = $this->getKeteranganKode($kode);
            if (!empty($ket) && $ket !== $kode) {
                $keteranganParts[] = $ket;
            }
        }

        return !empty($keteranganParts) ? implode(' & ', $keteranganParts) . '.' : '-';
    }
}