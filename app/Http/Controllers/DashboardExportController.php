<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Kedeputian;
use App\Models\PesertaMagang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardExportController extends Controller
{
    public function export(Request $request)
    {
        $selectedMonth = $request->query('month', date('m'));
        $selectedYear = $request->query('year', date('Y'));
        $selectedKedeputian = $request->query('kedeputian_id');

        $query = Absensi::whereMonth('tanggal', $selectedMonth)
            ->whereYear('tanggal', $selectedYear);

        if ($selectedKedeputian) {
            $query->whereHas('pesertaMagang', function ($q) use ($selectedKedeputian) {
                $q->where('kedeputian_id', $selectedKedeputian);
            });
        }

        // Stats
        $stats = [
            'total_peserta' => PesertaMagang::when($selectedKedeputian, function ($q) use ($selectedKedeputian) {
                return $q->where('kedeputian_id', $selectedKedeputian);
            })->count(),
            'total_tk' => (clone $query)->where('kehadiran', 'TK')->count(),
            'total_tm' => (clone $query)->where(function ($q) {
                $q->where('kehadiran', 'like', 'TM%')->orWhere('kehadiran', 'like', '%TM%');
            })->count(),
            'total_pc' => (clone $query)->where(function ($q) {
                $q->where('kehadiran', 'like', 'PC%')->orWhere('kehadiran', 'like', '%PC%');
            })->count(),
        ];

        // Top 5 TK
        $topTK = (clone $query)->where('kehadiran', 'TK')
            ->select('peserta_magang_id', DB::raw('count(*) as count'))
            ->groupBy('peserta_magang_id')
            ->orderBy('count', 'desc')
            ->with(['pesertaMagang.kedeputian'])
            ->take(5)
            ->get();

        // Top 5 TM
        $topTM = (clone $query)->where(function ($q) {
            $q->where('kehadiran', 'like', 'TM%')->orWhere('kehadiran', 'like', '%TM%');
        })
            ->select('peserta_magang_id', DB::raw('count(*) as count'))
            ->groupBy('peserta_magang_id')
            ->orderBy('count', 'desc')
            ->with(['pesertaMagang.kedeputian'])
            ->take(5)
            ->get();

        // Max scale for charts
        $maxTK = $topTK->first()?->count ?? 1;
        $maxTM = $topTM->first()?->count ?? 1;

        // Fetch FULL Lists for PDF Appendix
        $listQuery = (clone $query)->with(['pesertaMagang.kedeputian']);

        $tkList = (clone $listQuery)->where('kehadiran', 'TK')
            ->select('peserta_magang_id', DB::raw('count(*) as total'))
            ->groupBy('peserta_magang_id')
            ->orderBy('total', 'desc')
            ->get();

        $tmList = (clone $listQuery)->where(function ($q) {
            $q->where('kehadiran', 'like', 'TM%')->orWhere('kehadiran', 'like', '%TM%');
        })
            ->select('peserta_magang_id', DB::raw('count(*) as total'))
            ->groupBy('peserta_magang_id')
            ->orderBy('total', 'desc')
            ->get();

        $pcList = (clone $listQuery)->where(function ($q) {
            $q->where('kehadiran', 'like', 'PC%')->orWhere('kehadiran', 'like', '%PC%');
        })
            ->select('peserta_magang_id', DB::raw('count(*) as total'))
            ->groupBy('peserta_magang_id')
            ->orderBy('total', 'desc')
            ->get();

        // Meta data
        $kedeputianLabel = $selectedKedeputian ? Kedeputian::find($selectedKedeputian)->nama : 'Semua Kedeputian';
        $periodLabel = \Carbon\Carbon::createFromDate($selectedYear, $selectedMonth, 1)->translatedFormat('F Y');

        $pdf = Pdf::loadView('pdf.dashboard-export', [
            'stats' => $stats,
            'topTK' => $topTK,
            'topTM' => $topTM,
            'maxTK' => $maxTK,
            'maxTM' => $maxTM,
            'kedeputianLabel' => $kedeputianLabel,
            'periodLabel' => $periodLabel,
            'tkList' => $tkList,
            'tmList' => $tmList,
            'pcList' => $pcList
        ]);

        return $pdf->download('Laporan_Dashboard_' . $periodLabel . '.pdf');
    }
}
