<?php

namespace App\Livewire;

use App\Models\Absensi;
use App\Models\PesertaMagang;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class StatistikKehadiran extends Component
{
    public function render()
    {
        // Statistik per kategori
        $stats = Absensi::select('kehadiran', DB::raw('count(*) as total'))
            ->groupBy('kehadiran')
            ->get()
            ->pluck('total', 'kehadiran');

        // Top 5 Peserta dengan TK (Tanpa Keterangan) terbanyak
        $topTK = Absensi::where('kehadiran', 'TK')
            ->select('peserta_magang_id', DB::raw('count(*) as count'))
            ->groupBy('peserta_magang_id')
            ->orderBy('count', 'desc')
            ->with('pesertaMagang')
            ->take(5)
            ->get();

        // Top 5 Peserta dengan TM (Telat Masuk) terbanyak
        $topTM = Absensi::where('kehadiran', 'TM')
            ->select('peserta_magang_id', DB::raw('count(*) as count'))
            ->groupBy('peserta_magang_id')
            ->orderBy('count', 'desc')
            ->with('pesertaMagang')
            ->take(5)
            ->get();

        return view('livewire.statistik-kehadiran', [
            'stats' => $stats,
            'topTK' => $topTK,
            'topTM' => $topTM,
        ]);
    }
}
