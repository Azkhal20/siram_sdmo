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

    public function exportExcel()
    {
        $fileName = 'Rekap_Absensi_' . $this->filterKedeputian . '_' . date('Y-m-d_H-i-s') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');

            // Header CSV (Updated 'Status' to 'Kehadiran')
            fputcsv($handle, ['NIP', 'Nama Peserta', 'Kedeputian', 'Unit Kerja Asal', 'Tanggal', 'Kehadiran', 'Jam Masuk', 'Jam Pulang', 'Telat (Menit)']);

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
                ->orderBy('tanggal', 'desc');

            $query->chunk(500, function ($absensis) use ($handle) {
                foreach ($absensis as $absen) {
                    fputcsv($handle, [
                        $absen->pesertaMagang->nomor_induk . ' ',
                        $absen->pesertaMagang->nama,
                        $absen->pesertaMagang->kedeputian->nama ?? '-',
                        $absen->pesertaMagang->unit_kerja_text ?? '-',
                        $absen->tanggal->format('d/m/Y'),
                        $absen->kehadiran,
                        $absen->jam_masuk,
                        $absen->jam_pulang,
                        $absen->menit_telat
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
