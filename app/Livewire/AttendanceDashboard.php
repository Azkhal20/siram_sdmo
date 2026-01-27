<?php

namespace App\Livewire;

use App\Models\Absensi;
use App\Models\PesertaMagang;
use App\Models\Kedeputian;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class AttendanceDashboard extends Component
{
    use \Livewire\WithPagination;

    public $selectedMonth;
    public $selectedYear;
    public $selectedKedeputian = "";
    public $activeDetail = 'recent'; // recent, TK, TM, PC
    public $perPage = 10;

    public function mount()
    {
        // Try to find the latest attendance data to set as default view
        try {
            $latest = Absensi::whereNotNull('tanggal')->orderBy('tanggal', 'desc')->first();

            if ($latest && $latest->tanggal) {
                // Ensure tanggal is treated as a Carbon instance
                $date = \Carbon\Carbon::parse($latest->tanggal);
                $this->selectedMonth = $date->format('m');
                $this->selectedYear = $date->format('Y');
            } else {
                $this->selectedMonth = date('m');
                $this->selectedYear = date('Y');
            }
        } catch (\Exception $e) {
            // Fallback if there is a database issue
            $this->selectedMonth = date('m');
            $this->selectedYear = date('Y');
        }
    }

    public function updatedSelectedKedeputian($value)
    {
        if (!$value) {
            // When switching to All Units, always jump to the global latest available month
            $latest = Absensi::orderBy('tanggal', 'desc')->first();
            if ($latest && $latest->tanggal) {
                $date = \Carbon\Carbon::parse($latest->tanggal);
                $this->selectedMonth = $date->format('m');
                $this->selectedYear = $date->format('Y');
            }
        } else {
            // When switching to a specific Unit, only jump if the current month has no data for that unit
            $hasDataCurrentPeriod = Absensi::whereMonth('tanggal', $this->selectedMonth)
                ->whereYear('tanggal', $this->selectedYear)
                ->whereHas('pesertaMagang', function ($pq) use ($value) {
                    $pq->where('kedeputian_id', $value);
                })->exists();

            if (!$hasDataCurrentPeriod) {
                $latest = Absensi::whereHas('pesertaMagang', function ($pq) use ($value) {
                    $pq->where('kedeputian_id', $value);
                })->orderBy('tanggal', 'desc')->first();

                if ($latest && $latest->tanggal) {
                    $date = \Carbon\Carbon::parse($latest->tanggal);
                    $this->selectedMonth = $date->format('m');
                    $this->selectedYear = $date->format('Y');
                }
            }
        }

        $this->resetPage();
    }

    public function setActiveDetail($type)
    {
        $this->activeDetail = ($this->activeDetail === $type) ? 'recent' : $type;
        $this->resetPage(); // Reset pagination when switching tabs

        // Dispatch event for auto-scrolling
        $this->dispatch('scroll-to-table');
    }

    public function getMonths()
    {
        return [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];
    }
    public function getBadgeClass($kode)
    {
        return match ($kode) {
            'TK' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-400 border border-red-200 dark:border-red-800',
            'S', 'I' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-400 border border-amber-200 dark:border-amber-800',
            'TM', 'PC' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-400 border border-amber-200 dark:border-amber-800',
            'TMDHM' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-400 border border-purple-200 dark:border-purple-800',
            default => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-400 border border-blue-200 dark:border-blue-800',
        };
    }

    public function render()
    {
        try {
            $query = Absensi::whereMonth('tanggal', $this->selectedMonth)
                ->whereYear('tanggal', $this->selectedYear);

            // Apply Kedeputian filter to the base query globally
            if ($this->selectedKedeputian) {
                $query->whereHas('pesertaMagang', function ($q) {
                    $q->where('kedeputian_id', $this->selectedKedeputian);
                });
            }

            // Calculate stats (Filtered by Month, Year, and Kedeputian)
            $stats = [
                'total_peserta' => PesertaMagang::when($this->selectedKedeputian, function ($q) {
                    return $q->where('kedeputian_id', $this->selectedKedeputian);
                })->count(),
                'total_tk' => (clone $query)->where('kehadiran', 'TK')->count(),
                'total_tm' => (clone $query)->where(function ($q) {
                    $q->where('kehadiran', 'like', 'TM%')
                        ->orWhere('kehadiran', 'like', '%TM%');
                })->count(),
                'total_pc' => (clone $query)->where(function ($q) {
                    $q->where('kehadiran', 'like', 'PC%')
                        ->orWhere('kehadiran', 'like', '%PC%');
                })->count(),
            ];

            // Top 5 TK (Now automatically filtered)
            $topTK = (clone $query)->where('kehadiran', 'TK')
                ->select('peserta_magang_id', DB::raw('count(*) as count'))
                ->groupBy('peserta_magang_id')
                ->orderBy('count', 'desc')
                ->with('pesertaMagang.kedeputian')
                ->take(5)
                ->get();

            // Top 5 TM (Now automatically filtered)
            $topTM = (clone $query)->where(function ($q) {
                $q->where('kehadiran', 'like', 'TM%')
                    ->orWhere('kehadiran', 'like', '%TM%');
            })
                ->select('peserta_magang_id', DB::raw('count(*) as count'))
                ->groupBy('peserta_magang_id')
                ->orderBy('count', 'desc')
                ->with('pesertaMagang.kedeputian')
                ->take(5)
                ->get();

            // Calculate Max counts for bar charts visualization
            $maxTK = $topTK->first()?->count ?? 1;
            $maxTM = $topTM->first()?->count ?? 1;

            // Prepare Data for List Table
            $listQuery = (clone $query)->with(['pesertaMagang.kedeputian']);

            if ($this->activeDetail === 'recent') {
                $detailData = $listQuery->orderBy('tanggal', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->paginate($this->perPage);
            } elseif ($this->activeDetail === 'TK') {
                $detailData = $listQuery->where('kehadiran', 'TK')
                    ->select('peserta_magang_id', DB::raw('count(*) as total_count'))
                    ->groupBy('peserta_magang_id')
                    ->orderBy('total_count', 'desc')
                    ->paginate($this->perPage);
            } elseif ($this->activeDetail === 'TM') {
                $detailData = $listQuery->where(function ($q) {
                    $q->where('kehadiran', 'like', 'TM%')->orWhere('kehadiran', 'like', '%TM%');
                })
                    ->select('peserta_magang_id', DB::raw('count(*) as total_count'))
                    ->groupBy('peserta_magang_id')
                    ->orderBy('total_count', 'desc')
                    ->paginate($this->perPage);
            } elseif ($this->activeDetail === 'PC') {
                $detailData = $listQuery->where(function ($q) {
                    $q->where('kehadiran', 'like', 'PC%')->orWhere('kehadiran', 'like', '%PC%');
                })
                    ->select('peserta_magang_id', DB::raw('count(*) as total_count'))
                    ->groupBy('peserta_magang_id')
                    ->orderBy('total_count', 'desc')
                    ->paginate($this->perPage);
            } else {
                $detailData = Absensi::whereRaw('1=0')->paginate($this->perPage);
            }

            // Final safety check to ensure detailData is always a paginator
            if (!($detailData instanceof \Illuminate\Contracts\Pagination\Paginator)) {
                $detailData = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage);
            }

            return view('livewire.attendance-dashboard', [
                'stats' => $stats,
                'topTK' => $topTK,
                'topTM' => $topTM,
                'maxTK' => $maxTK,
                'maxTM' => $maxTM,
                'detailData' => $detailData,
                'months' => $this->getMonths(),
                'years' => range(date('Y'), date('Y') - 5),
                'kedeputians' => Kedeputian::orderBy('nama')->get(),
            ]);
        } catch (\Exception $e) {
            // Handle cases where database might not be ready or migrations haven't run
            \Illuminate\Support\Facades\Log::error('Dashboard Render Error: ' . $e->getMessage());

            return view('livewire.attendance-dashboard', [
                'stats' => ['total_peserta' => 0, 'total_tk' => 0, 'total_tm' => 0, 'total_pc' => 0],
                'topTK' => collect(),
                'topTM' => collect(),
                'maxTK' => 1,
                'maxTM' => 1,
                'detailData' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10),
                'months' => $this->getMonths(),
                'years' => range(date('Y'), date('Y') - 5),
                'kedeputians' => collect(),
                'db_error' => true // Flag to show warning in view if you have a place for it
            ]);
        }
    }
}
