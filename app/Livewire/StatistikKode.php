<?php

namespace App\Livewire;

use App\Models\Absensi;
use App\Models\PesertaMagang;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class StatistikKode extends Component
{
    public function render()
    {
        // Statistik per kategori
        $stats = Absensi::select('kode', DB::raw('count(*) as total'))
            ->groupBy('kode')
            ->get()
            ->pluck('total', 'kode');

        // Top 5 Peserta dengan TK (Tanpa Keterangan) terbanyak
        $topTK = Absensi::where('kode', 'TK')
            ->select('peserta_magang_id', DB::raw('count(*) as count'))
            ->groupBy('peserta_magang_id')
            ->orderBy('count', 'desc')
            ->with('pesertaMagang')
            ->take(5)
            ->get();

        // Top 5 Peserta dengan TM (Telat Masuk) terbanyak
        $topTM = Absensi::where('kode', 'TM')
            ->select('peserta_magang_id', DB::raw('count(*) as count'))
            ->groupBy('peserta_magang_id')
            ->orderBy('count', 'desc')
            ->with('pesertaMagang')
            ->take(5)
            ->get();

        return view('livewire.statistik-kode', [
            'stats' => $stats,
            'topTK' => $topTK,
            'topTM' => $topTM,
        ]);
    }
}
