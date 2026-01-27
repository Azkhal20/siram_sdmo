<?php

namespace App\Livewire;

use App\Models\Absensi;
use App\Models\Kedeputian;
use App\Exports\AbsensiExport;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

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

    // Modal Edit
    public $showEditModal = false;
    public $editingAbsensiId;
    public $editKehadiran;
    public $editJamMasuk;
    public $editJamPulang;
    public $editKeterangan;

    // Modal Delete
    public $showDeleteModal = false;
    public $deletingAbsensiId;
    public $deletingPesertaNama;

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

    // Method untuk membuka modal edit
    public function openEditModal($absensiId)
    {
        $absensi = Absensi::with('pesertaMagang')->findOrFail($absensiId);
        
        $this->editingAbsensiId = $absensiId;
        $this->editKehadiran = $absensi->kehadiran;
        $this->editJamMasuk = $absensi->jam_masuk ? \Carbon\Carbon::parse($absensi->jam_masuk)->format('H:i') : '';
        $this->editJamPulang = $absensi->jam_pulang ? \Carbon\Carbon::parse($absensi->jam_pulang)->format('H:i') : '';
        $this->editKeterangan = $absensi->keterangan;
        
        $this->showEditModal = true;
    }

    // Method untuk menutup modal edit
    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->reset(['editingAbsensiId', 'editKehadiran', 'editJamMasuk', 'editJamPulang', 'editKeterangan']);
        $this->resetValidation();
    }

    // Method untuk update data
    public function updateAbsensi()
    {
        $this->validate([
            'editKehadiran' => 'required|string',
            'editJamMasuk' => 'nullable|date_format:H:i',
            'editJamPulang' => 'nullable|date_format:H:i',
            'editKeterangan' => 'nullable|string|max:500',
        ], [
            'editKehadiran.required' => 'Kehadiran wajib diisi.',
            'editJamMasuk.date_format' => 'Format jam masuk tidak valid (HH:MM).',
            'editJamPulang.date_format' => 'Format jam pulang tidak valid (HH:MM).',
        ]);

        $absensi = Absensi::findOrFail($this->editingAbsensiId);
        
        $absensi->update([
            'kehadiran' => $this->editKehadiran,
            'jam_masuk' => $this->editJamMasuk ?: null,
            'jam_pulang' => $this->editJamPulang ?: null,
            'keterangan' => $this->editKeterangan,
        ]);

        session()->flash('success', 'Data absensi berhasil diupdate.');
        $this->closeEditModal();
    }

    // Method untuk membuka modal delete
    public function openDeleteModal($absensiId)
    {
        $absensi = Absensi::with('pesertaMagang')->findOrFail($absensiId);
        $this->deletingAbsensiId = $absensiId;
        $this->deletingPesertaNama = $absensi->pesertaMagang->nama;
        $this->showDeleteModal = true;
    }

    // Method untuk menutup modal delete
    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->reset(['deletingAbsensiId', 'deletingPesertaNama']);
    }

    // Method untuk delete data
    public function deleteAbsensi()
    {
        $absensi = Absensi::findOrFail($this->deletingAbsensiId);
        $absensi->delete();

        session()->flash('success', 'Data absensi berhasil dihapus.');
        $this->closeDeleteModal();
    }

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

    private function getNamaKedeputian(): string
    {
        if (empty($this->filterKedeputian)) {
            return 'Semua_Kedeputian';
        }

        $kedeputian = Kedeputian::find($this->filterKedeputian);
        if ($kedeputian) {
            return str_replace(' ', '_', preg_replace('/[^A-Za-z0-9\s]/', '', $kedeputian->nama));
        }

        return 'Kedeputian_' . $this->filterKedeputian;
    }

    private function getRentangTanggal(): string
    {
        if ($this->fromDate && $this->toDate) {
            return date('d-m-Y', strtotime($this->fromDate)) . '_sd_' . date('d-m-Y', strtotime($this->toDate));
        } elseif ($this->fromDate) {
            return 'dari_' . date('d-m-Y', strtotime($this->fromDate));
        } elseif ($this->toDate) {
            return 'sampai_' . date('d-m-Y', strtotime($this->toDate));
        }

        return 'Semua_Tanggal';
    }

    public function exportExcel()
    {
        $namaKedeputian = $this->getNamaKedeputian();
        $rentangTanggal = $this->getRentangTanggal();
        $timestamp = date('Y-m-d_H-i-s');

        $fileName = "Rekap_Absensi_{$namaKedeputian}_{$rentangTanggal}_{$timestamp}.xlsx";

        return Excel::download(
            new AbsensiExport(
                $this->search,
                $this->filterKedeputian,
                $this->fromDate,
                $this->toDate
            ),
            $fileName
        );
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