<?php

namespace App\Livewire;

use App\Models\Absensi;
use App\Models\Kedeputian;
use App\Models\PesertaMagang;
use App\Models\ActivityLog;
use Livewire\Component;
use Livewire\WithFileUploads;
use Smalot\PdfParser\Parser;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DataAbsensi extends Component
{
    use WithFileUploads;

    public $pdfFile;
    public $previewData = [];
    public $isProcessing = false;
    public $showPreview = false;
    public $selectedKedeputian = '';
    public $kedeputianList = [];

    public function mount()
    {
        $this->kedeputianList = Kedeputian::orderBy('nama')->get();
    }

    public function updatedPdfFile()
    {
        Log::info('File updated: ' . ($this->pdfFile ? $this->pdfFile->getClientOriginalName() : 'NULL'));

        $this->validate([
            'selectedKedeputian' => 'required',
            'pdfFile' => 'required|mimes:pdf|max:20480',
        ], [
            'selectedKedeputian.required' => 'Mohon pilih Kedeputian terlebih dahulu.',
        ]);

        $this->parsePdf();
    }

    public function parsePdf()
    {
        $this->isProcessing = true;
        $this->showPreview = false;
        $this->previewData = [];

        try {
            if (!$this->pdfFile) {
                throw new \Exception('File tidak ditemukan dalam upload.');
            }

            $path = $this->pdfFile->getRealPath();
            Log::info('Parsing PDF path: ' . $path);

            $parser = new Parser();
            $pdfContent = $parser->parseFile($path);
            $pages = $pdfContent->getPages();

            Log::info('PDF Pages Found: ' . count($pages));

            $allParsedResults = [];
            $globalIndex = 0;

            foreach ($pages as $index => $page) {
                $text = $page->getText();
                $text = preg_replace('/\s+/', ' ', $text);

                $structuredLines = explode("\n", $page->getText());

                $nip = 'N/A';
                $nama = 'Peserta Tidak Terdeteksi';
                $unitKerja = 'Unit Kerja Tidak Terdeteksi';

                foreach ($structuredLines as $sLine) {
                    $sLine = trim($sLine);
                    if (preg_match('/NIP\s*:\s*(\d+)/i', $sLine, $m)) $nip = $m[1];
                    if (preg_match('/Nama\s*:\s*([^\n:]+)/i', $sLine, $m)) $nama = trim($m[1]);
                    if (preg_match('/Unit Kerja\s*:\s*([^\n:]+)/i', $sLine, $m)) $unitKerja = trim($m[1]);
                }

                foreach ($structuredLines as $line) {
                    $line = trim($line);

                    if (preg_match('/(\d{2}[\-\/]\d{2}[\-\/]\d{4})/', $line, $dateMatches)) {
                        $tanggalRaw = $dateMatches[1];
                        $tanggalStr = str_replace('/', '-', $tanggalRaw);

                        // Reset kehadiran for each line processing
                        $kehadiran = null;

                        // Robust Pattern: Match codes that might be attached to dates/numbers or standalone
                        // Covers: "2024TK", "TM 1", "TM1", "TMDHM", etc.
                        $codesPattern = '/(?:^|[\s\d])(PC\s?\d+[\s-]?TMDHM|TM\s?\d+[\s-]?TMDHP|TMDHM|TMDHP|TM\s?\d*|PC\s?\d*|TK|HN|LN|LJ|S|I|DL|C)(?:\b|$)/i';

                        if (preg_match($codesPattern, $line, $codeMatches)) {
                            // Normalize "TM 1" -> "TM1"
                            $raw = strtoupper(str_replace(' ', '', $codeMatches[1]));
                            // Normalize compound logic: ensure dash separator for specific compounds
                            $kehadiran = str_replace(['PC1TMDHM', 'PC2TMDHM', 'PC3TMDHM', 'PC1 TMDHM'], 'PC1-TMDHM', $raw); // Fallback to PC1-TMDHM if variation found, or better specific replacements if needed

                            // Better generalization:
                            if (str_contains($raw, 'TMDHM') && str_contains($raw, 'PC')) {
                                // Extract PC number
                                preg_match('/PC(\d+)/', $raw, $pcM);
                                $num = $pcM[1] ?? '1';
                                $kehadiran = "PC{$num}-TMDHM";
                            }
                        }

                        if ($kehadiran) {
                            // Skip codes that the user requested to exclude (HN, LN, LJ, and unused S, I, DL, C)
                            if (in_array($kehadiran, ['HN', 'LN', 'LJ', 'S', 'I', 'DL', 'C'])) {
                                continue;
                            }

                            preg_match_all('/(\d{2}:\d{2})/', $line, $timeMatches);
                            $times = $timeMatches[1] ?? [];

                            $jamMasuk = null;
                            $jamPulang = null;
                            $menitTelat = 0;

                            // Robust Time Extraction Logic
                            $validAttendanceTimes = [];
                            foreach ($times as $t) {
                                // Filter out likely durations (e.g. 00:xx) if context suggests detailed codes (TM1-3, PC1-3)
                                // But keep everything for generic codes.
                                $validAttendanceTimes[] = $t;
                            }

                            if ($kehadiran === 'TK') {
                                $jamMasuk = null;
                                $jamPulang = null;
                            } elseif ($kehadiran === 'TMDHM') {
                                // only pulang (afternoon)
                                foreach ($validAttendanceTimes as $t) {
                                    $h = (int)explode(':', $t)[0];
                                    if ($h >= 12) {
                                        $jamPulang = $t;
                                        break;
                                    }
                                }
                            } elseif ($kehadiran === 'TMDHP') {
                                // only masuk (morning)
                                foreach ($validAttendanceTimes as $t) {
                                    $h = (int)explode(':', $t)[0];
                                    if ($h < 12) {
                                        $jamMasuk = $t;
                                        break;
                                    }
                                }
                            } elseif ($kehadiran === 'HN') {
                                // Could be [masuk, pulang]
                                $jamMasuk = $validAttendanceTimes[0] ?? null;
                                $jamPulang = $validAttendanceTimes[1] ?? null;
                            } else {
                                // General Logic (TM, PC, TM1-3, etc)
                                if (count($validAttendanceTimes) >= 2) {
                                    $t1 = $validAttendanceTimes[0];
                                    $t2 = $validAttendanceTimes[1];
                                    $h1 = (int)explode(':', $t1)[0];
                                    $h2 = (int)explode(':', $t2)[0];

                                    if ($h1 < 12 && $h2 >= 12) {
                                        $jamMasuk = $t1;
                                        $jamPulang = $t2;
                                    } elseif ($h1 < 12 && $h2 < 12) {
                                        $jamMasuk = $t1; // assume first is masuk
                                    } else {
                                        $jamPulang = $t1; // assume first is pulang
                                    }
                                } elseif (count($validAttendanceTimes) === 1) {
                                    $t1 = $validAttendanceTimes[0];
                                    $h1 = (int)explode(':', $t1)[0];
                                    if ($h1 < 12) $jamMasuk = $t1;
                                    else $jamPulang = $t1;
                                }
                            }
                        }

                        // Calculate Telat / Pulang Cepat & Determine Codes for Keterangan
                        $telatFormatted = '-';
                        $pulangCepatFormatted = '-';
                        $tmCode = null;
                        $pcCode = null;

                        // Skip calculation for pure TK
                        if ($kehadiran !== 'TK') {
                            // 1. Telat Masuk Calculation
                            if ($jamMasuk) {
                                $masukVs = Carbon::parse($jamMasuk);
                                // Compare against 08:00:00
                                if ($masukVs->format('H:i:s') > '08:00:00') {
                                    $limitSeconds = 8 * 3600;
                                    $parts = explode(':', $masukVs->format('H:i:s'));
                                    $currentSeconds = ($parts[0] * 3600) + ($parts[1] * 60) + $parts[2];
                                    $diff = $currentSeconds - $limitSeconds;
                                    $totalMinutes = intdiv($diff, 60);

                                    $telatFormatted = sprintf('%02d:%02d', intdiv($diff, 3600), intdiv($diff % 3600, 60));

                                    if ($totalMinutes > 60) $tmCode = 'TM3';
                                    elseif ($totalMinutes > 30) $tmCode = 'TM2';
                                    else $tmCode = 'TM1';
                                }
                            } elseif (!in_array($kehadiran, ['HN', 'LN', 'LJ'])) {
                                // If no jam_masuk and not a leave code, implied TMDHM if it's a workday attendace
                                $tmCode = 'TMDHM';
                            }

                            // 2. Pulang Cepat Calculation
                            if ($jamPulang) {
                                $pulangVs = Carbon::parse($jamPulang);
                                // Compare against 16:30:00
                                if ($pulangVs->format('H:i:s') < '16:30:00') {
                                    $limitSeconds = (16 * 3600) + (30 * 60);
                                    $parts = explode(':', $pulangVs->format('H:i:s'));
                                    $currentSeconds = ($parts[0] * 3600) + ($parts[1] * 60) + $parts[2];
                                    $diff = $limitSeconds - $currentSeconds;
                                    $totalMinutes = intdiv($diff, 60);

                                    $pulangCepatFormatted = sprintf('%02d:%02d', intdiv($diff, 3600), intdiv($diff % 3600, 60));

                                    if ($totalMinutes > 60) $pcCode = 'PC3';
                                    elseif ($totalMinutes > 30) $pcCode = 'PC2';
                                    else $pcCode = 'PC1';
                                }
                            } elseif ($jamMasuk && !in_array($kehadiran, ['HN', 'LN', 'LJ'])) {
                                // If yes jam_masuk but no jam_pulang -> implied TMDHP
                                $pcCode = 'TMDHP';
                            }
                        }

                        // 3. Build keterangan - STRICTLY BASED ON KEHADIRAN COLUMN
                        // User Request: "Keterangan tinggal ngikutin kehadirannya apa"

                        $descMap = [
                            'TK'        => 'Tanpa Keterangan',
                            'TMDHM'     => 'Tidak Absen Masuk',
                            'TMDHP'     => 'Tidak Absen Pulang',
                            'PC1-TMDHM' => 'Pulang Cepat Kurang dari 30 menit dan Tidak Absen Masuk',
                            'TM1'       => 'Terlambat masuk',
                            'TM2'       => 'Lebih dari 30 Menit',
                            'TM3'       => 'Lebih dari 1 Jam',
                            'TM'        => 'Terlambat',
                            'PC'        => 'Pulang cepat',
                            'PC1'       => 'Kurang dari 30 Menit',
                            'PC2'       => 'Lebih dari 30 Menit',
                            'PC3'       => 'Lebih dari 1 Jam',
                            'S'         => 'Sakit',
                            'I'         => 'Izin',
                            'C'         => 'Cuti',
                            'DL'        => 'Dinas Luar',
                            'HN'        => 'Hadir Normal',
                            'LN'        => 'Lupa Absen Negara',
                            'LJ'        => 'Lupa Absen Jumat',
                        ];

                        $cleanKehadiran = str_replace(' ', '', $kehadiran); // e.g. "TM 1" -> "TM1"

                        // Direct mapping: What is in "Kehadiran" column -> "Keterangan"
                        // No extra logic to add "dan Pulang Cepat" based on time if the code doesn't say so.
                        if (isset($descMap[$cleanKehadiran])) {
                            $keteranganStr = $descMap[$cleanKehadiran];
                        } else {
                            // If code has no description (e.g. unknown code), use the code itself
                            $keteranganStr = $cleanKehadiran;
                        }

                        $allParsedResults[] = [
                            '_index' => $globalIndex++,
                            'nip' => $nip,
                            'nama' => $nama,
                            'unit' => $unitKerja,
                            'tanggal' => Carbon::parse($tanggalStr)->format('Y-m-d'),
                            'kehadiran' => $kehadiran,
                            'jam_masuk' => $jamMasuk ? Carbon::parse($jamMasuk)->format('H:i') : null,
                            'jam_pulang' => $jamPulang ? Carbon::parse($jamPulang)->format('H:i') : null,
                            'menit_telat' => 0,
                            'telat_formatted' => $telatFormatted,
                            'pulang_cepat_formatted' => $pulangCepatFormatted,
                            'keterangan' => $keteranganStr,
                        ];
                    }
                }
            }


            // Sort by NIP then Tanggal
            usort($allParsedResults, function ($a, $b) {
                if ($a['nip'] === $b['nip']) {
                    return strcmp($a['tanggal'], $b['tanggal']);
                }
                return strcmp($a['nip'], $b['nip']);
            });

            if (empty($allParsedResults)) {
                session()->flash('error', 'Tidak ditemukan data absensi. Pastikan PDF valid.');
            } else {
                $this->previewData = $allParsedResults;
                $this->showPreview = true;
            }
        } catch (\Exception $e) {
            Log::error('Parsing Error: ' . $e->getMessage());
            session()->flash('error', 'Gagal memproses PDF: ' . $e->getMessage());
        }

        $this->isProcessing = false;
    }

    private function timeToMinutes($timeStr)
    {
        if (!$timeStr || !str_contains($timeStr, ':')) return 0;
        $parts = explode(':', $timeStr);
        return ((int)$parts[0] * 60) + (int)$parts[1];
    }

    public function saveData()
    {
        if (empty($this->previewData)) return;

        DB::beginTransaction();
        try {
            foreach ($this->previewData as $data) {
                $tanggal = Carbon::parse($data['tanggal'])->format('Y-m-d');

                $peserta = PesertaMagang::updateOrCreate(
                    ['nomor_induk' => $data['nip']],
                    [
                        'nama' => $data['nama'],
                        'kedeputian_id' => $this->selectedKedeputian,
                        'unit_kerja_text' => $data['unit']
                    ]
                );

                Absensi::updateOrCreate(
                    ['peserta_magang_id' => $peserta->id, 'tanggal' => $tanggal],
                    [
                        'kehadiran' => $data['kehadiran'],
                        'jam_masuk' => $data['jam_masuk'],
                        'jam_pulang' => $data['jam_pulang'],
                        'menit_telat' => $data['menit_telat'],
                        'keterangan' => $data['keterangan'] ?? null,
                    ]
                );
            }
            DB::commit();

            // ✅ CATAT LOG AKTIVITAS
            $kedeputian = Kedeputian::find($this->selectedKedeputian);
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Import Absensi',
                'model_type' => 'Absensi',
                'model_id' => null,
                'description' => 'Import ' . count($this->previewData) . ' data absensi dari PDF ke Kedeputian: ' . ($kedeputian->nama ?? 'Unknown'),
                'ip_address' => request()->ip(),
            ]);

            session()->flash('success', 'Berhasil menyimpan ' . count($this->previewData) . ' data ke Kedeputian terpilih.');
            $this->reset(['pdfFile', 'previewData', 'showPreview', 'selectedKedeputian']);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.data-absensi');
    }
}
