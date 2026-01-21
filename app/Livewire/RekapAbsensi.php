<?php

namespace App\Livewire;

use App\Models\Absensi;
use App\Models\Kedeputian;
use Livewire\Component;
use Livewire\WithPagination;

class RekapAbsensi extends Component
{
    use WithPagination;

    public $search = '';
    public $filterKedeputian = '';
    public $fromDate = '';
    public $toDate = '';
    public $sortField = 'tanggal';
    public $sortDirection = 'desc';
    public $perPage = 10;

    // Daftar kode kehadiran untuk parsing gabungan
    private $kodeKehadiranList = [
        'TMDHM', 'TMDHP',
        'TM1', 'TM2', 'TM3', 'TM',
        'PC1', 'PC2', 'PC3', 'PC',
        'HN', 'TK', 'DL', 'LJ', 'LN', 'S', 'I', 'C', 'K'
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'filterKedeputian' => ['except' => ''],
        'fromDate' => ['except' => ''],
        'toDate' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingFilterKedeputian()
    {
        $this->resetPage();
    }
    public function updatingFromDate()
    {
        $this->resetPage();
    }
    public function updatingToDate()
    {
        $this->resetPage();
    }
    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function getBadgeClass($kehadiran)
    {
        return match ($kehadiran) {
            'TK' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-800',
            'S', 'I', 'C' => 'bg-teal-100 text-teal-800 dark:bg-teal-900/30 dark:text-teal-400 border border-teal-200 dark:border-teal-800',
            'TM', 'PC' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400 border border-amber-200 dark:border-amber-800',
            'TMDHM' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400 border border-purple-200 dark:border-purple-800',
            'DL' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 border border-blue-200 dark:border-blue-800',
            'HN' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600',
        };
    }

    /**
     * Konversi kode kehadiran tunggal ke keterangan
     */
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

    /**
     * Parse kode gabungan menjadi array kode tunggal
     */
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

    /**
     * Konversi kode kehadiran (tunggal/gabungan) ke keterangan lengkap
     */
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

    public function exportExcel()
    {
        $fileName = 'Rekap_Absensi_' . ($this->filterKedeputian ?: 'Semua') . '_' . date('Y-m-d_H-i-s') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');

            // Header CSV
            fputcsv($handle, [
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
                'KETERANGAN'
            ]);

            $no = 0;

            $query = Absensi::with('pesertaMagang.kedeputian')
                ->whereHas('pesertaMagang', function ($query) {
                    $query->where('nama', 'like', '%' . $this->search . '%');
                })
                ->when($this->filterKedeputian, function ($query) {
                    $query->whereHas('pesertaMagang', function ($q) {
                        $q->where('kedeputian_id', $this->filterKedeputian);
                    });
                })
                ->when($this->fromDate, function ($query) {
                    $query->whereDate('tanggal', '>=', $this->fromDate);
                })
                ->when($this->toDate, function ($query) {
                    $query->whereDate('tanggal', '<=', $this->toDate);
                })
                ->orderBy('tanggal', 'asc');

            $query->chunk(500, function ($absensis) use ($handle, &$no) {
                foreach ($absensis as $absen) {
                    $no++;

                    // Format jam masuk dan pulang
                    $jamMasuk = $absen->jam_masuk ? date('H:i', strtotime($absen->jam_masuk)) : '-';
                    $jamPulang = $absen->jam_pulang ? date('H:i', strtotime($absen->jam_pulang)) : '-';

                    // Format telat masuk
                    $menitTelat = $absen->menit_telat ?? 0;
                    $telatStr = $menitTelat > 0 ? sprintf('%02d:%02d', floor($menitTelat / 60), $menitTelat % 60) : '-';

                    // Format pulang cepat
                    $menitPulangCepat = $absen->menit_pulang_cepat ?? 0;
                    $pulangCepatStr = $menitPulangCepat > 0 ? sprintf('%02d:%02d', floor($menitPulangCepat / 60), $menitPulangCepat % 60) : '-';

                    // Generate keterangan dari kode kehadiran
                    $keterangan = $this->getKeterangan($absen->kehadiran ?? 'HN');

                    fputcsv($handle, [
                        $no,
                        "'" . ($absen->pesertaMagang->nomor_induk ?? '-'),
                        $absen->pesertaMagang->nama ?? '-',
                        $absen->pesertaMagang->kedeputian->nama ?? '-',
                        $absen->pesertaMagang->unit_kerja_text ?? '-',
                        $absen->tanggal->format('d-m-Y'),
                        $absen->kehadiran ?? '-',
                        $jamMasuk,
                        $jamPulang,
                        $telatStr,
                        $pulangCepatStr,
                        $keterangan
                    ]);
                }
            });

            fclose($handle);
        }, $fileName);
    }

    public function render()
    {
        $absensis = Absensi::with('pesertaMagang.kedeputian')
            ->whereHas('pesertaMagang', function ($query) {
                $query->where('nama', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterKedeputian, function ($query) {
                $query->whereHas('pesertaMagang', function ($q) {
                    $q->where('kedeputian_id', $this->filterKedeputian);
                });
            })
            ->when($this->fromDate, function ($query) {
                $query->whereDate('tanggal', '>=', $this->fromDate);
            })
            ->when($this->toDate, function ($query) {
                $query->whereDate('tanggal', '<=', $this->toDate);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.rekap-absensi', [
            'absensis' => $absensis,
            'kedeputians' => Kedeputian::all(),
        ]);
    }
}