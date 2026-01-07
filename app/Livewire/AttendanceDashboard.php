<?php

namespace App\Livewire;

use App\Models\Absensi;
use App\Models\Kedeputian;
use App\Models\PesertaMagang;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceDashboard extends Component
{
    use WithFileUploads, WithPagination;

    public $pdfFile;
    public $filterKedeputian = '';
    public $startDate = '';
    public $endDate = '';

    protected $queryString = [
        'filterKedeputian' => ['except' => ''],
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
    ];

    public function updatedPdfFile()
    {
        $this->validate([
            'pdfFile' => 'required|mimes:pdf|max:10240',
        ]);

        $this->importPdf();
    }

    public function importPdf()
    {
        try {
            $path = $this->pdfFile->getRealPath();
            $parser = new Parser();
            $pdfContent = $parser->parseFile($path);
            $text = $pdfContent->getText();

            // Simple line-based parsing logic
            // Note: PDF parsing highly depends on the source layout. 
            // This is a robust attempt to find tabular data patterns.
            $lines = explode("\n", $text);

            DB::beginTransaction();

            foreach ($lines as $line) {
                // Example line structure might be tab or space separated
                // We'll clean up the line and try to match the fields
                $data = preg_split('/\t+| {2,}/', trim($line));

                // We expect at least something like (Nama, Tanggal, Kode, ...)
                // This is a heuristic - assuming Nama is first and Tanggal follows.
                if (count($data) >= 3) {
                    $nama = $data[0];
                    $tanggalStr = $data[1];
                    $kode = $data[2];

                    // Attempt to parse date
                    try {
                        $tanggal = Carbon::parse($tanggalStr);
                    } catch (\Exception $e) {
                        continue; // Skip lines without a valid date
                    }

                    // Find or create PesertaMagang
                    // Default to first kedeputian if not specified in PDF
                    $peserta = PesertaMagang::firstOrCreate(
                        ['nama' => $nama],
                        ['nomor_induk' => uniqid('M-'), 'kedeputian_id' => Kedeputian::first()?->id ?? 1]
                    );

                    Absensi::updateOrCreate(
                        [
                            'peserta_magang_id' => $peserta->id,
                            'tanggal' => $tanggal->format('Y-m-d'),
                        ],
                        [
                            'kode' => $kode,
                            'jam_masuk' => $data[3] ?? null,
                            'jam_pulang' => $data[4] ?? null,
                            'menit_telat' => isset($data[5]) ? (int)$data[5] : 0,
                            'keterangan' => $data[6] ?? null,
                        ]
                    );
                }
            }

            DB::commit();
            $this->reset('pdfFile');
            session()->flash('message', 'Data absensi berhasil di-import dari PDF.');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal memproses PDF: ' . $e->getMessage());
        }
    }

    public function getBadgeClass($kode)
    {
        return match ($kode) {
            'TK' => 'bg-red-100 text-red-800',
            'TM' => 'bg-yellow-100 text-yellow-800',
            'TMDHM' => 'bg-purple-100 text-purple-800',
            'TMDHP' => 'bg-indigo-100 text-indigo-800',
            'PC' => 'bg-orange-100 text-orange-800',
            default => 'bg-green-100 text-green-800',
        };
    }

    public function render()
    {
        $query = Absensi::with('pesertaMagang.kedeputian');

        if ($this->filterKedeputian) {
            $query->whereHas('pesertaMagang', function ($q) {
                $q->where('kedeputian_id', $this->filterKedeputian);
            });
        }

        if ($this->startDate) {
            $query->whereDate('tanggal', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('tanggal', '<=', $this->endDate);
        }

        $absensis = $query->orderBy('tanggal', 'desc')->paginate(10);

        // Calculate Stats
        $stats = [
            'total_peserta' => PesertaMagang::count(),
            'total_tk' => Absensi::where('kode', 'TK')->count(),
            'total_tm' => Absensi::where('kode', 'TM')->count(),
            'total_tmdhm' => Absensi::where('kode', 'TMDHM')->count(),
            'total_pc' => Absensi::where('kode', 'PC')->count(),
        ];

        return view('livewire.attendance-dashboard', [
            'absensis' => $absensis,
            'kedeputians' => Kedeputian::all(),
            'stats' => $stats,
        ]);
    }
}
