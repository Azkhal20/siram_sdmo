<?php

namespace App\Exports;

use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AbsensiExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Absensi::with(['pesertaMagang', 'pesertaMagang.kedeputian']);

        // Filter jika ada
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

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'NO',
            'NAMA PESERTA',
            'KEDEPUTIAN',
            'TANGGAL',
            'KODE',
            'JAM MASUK',
            'JAM PULANG',
            'TELAT (MENIT)',
            'KETERANGAN',
        ];
    }

    public function map($absensi): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $absensi->pesertaMagang->nama ?? '-',
            $absensi->pesertaMagang->kedeputian->nama_kedeputian ?? '-',
            $absensi->tanggal->format('d-m-Y'),
            $absensi->kode,
            $absensi->jam_masuk,
            $absensi->jam_pulang,
            $absensi->menit_telat ?? 0,
            $absensi->keterangan ?? '-',
        ];
    }
}