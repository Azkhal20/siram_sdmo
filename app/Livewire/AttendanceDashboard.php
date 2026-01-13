<?php

namespace App\Livewire;

use App\Models\Absensi;
use App\Models\PesertaMagang;
use Livewire\Component;

class AttendanceDashboard extends Component
{
    public function getBadgeClass($kehadiran)
    {
        return match ($kehadiran) {
            'TK' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-400 border border-red-200 dark:border-red-800',
            'S', 'I' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-400 border border-amber-200 dark:border-amber-800',
            'TM', 'PC' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-400 border border-amber-200 dark:border-amber-800',
            'TMDHM' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-400 border border-purple-200 dark:border-purple-800',
            default => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-400 border border-blue-200 dark:border-blue-800',
        };
    }

    public function render()
    {
        return view('livewire.attendance-dashboard', [
            'stats' => [
                'total_peserta' => PesertaMagang::count(),
                'total_tk' => Absensi::where('kehadiran', 'TK')->count(),
                'total_tm' => Absensi::where('kehadiran', 'TM')->count(),
                'total_pc' => Absensi::where('kehadiran', 'PC')->count(),
            ],
            'absensis' => Absensi::with('pesertaMagang')->latest()->take(5)->get(),
        ]);
    }
}
