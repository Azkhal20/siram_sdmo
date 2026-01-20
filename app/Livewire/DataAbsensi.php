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

                    // Initialize all variables at the start of each line loop to prevent undefined variable errors
                    $kehadiran = null;
                    $jamMasuk = null;
                    $jamPulang = null;
                    $menitTelat = 0;

                    if (preg_match('/(\d{2}[\-\/]\d{2}[\-\/]\d{4})/', $line, $dateMatches)) {
                        $tanggalRaw = $dateMatches[1];
                        $tanggalStr = str_replace('/', '-', $tanggalRaw);

                        // Regex Strategy:
                        // Group 1: Sticky Codes (can follow digits/spaces, e.g. 2026TM1, PC1-TMDHM)
                        // Group 2: Standalone Codes (Strict Word Boundaries, e.g. S, I, C, TK)
                        $compound = 'PC\s?\d+[\s-]?TMDHM|TM\s?\d+[\s-]?TMDHP|TMDHM|TMDHP';
                        $concat   = 'TM\d+PC\d+|PC\d+TM\d+';
                        $simple   = 'TM\s?\d*|PC\s?\d*';
                        $stickyPart = "{$compound}|{$concat}|{$simple}";
                        $standalonePart = 'TK|HN|LN|LJ';

                        $codesPattern = "/(?:^|[\s\d])({$stickyPart})(?:\b|$)|\b({$standalonePart})\b/i";

                        if (preg_match($codesPattern, $line, $codeMatches)) {
                            // Determine which group matched
                            $raw = !empty($codeMatches[1]) ? $codeMatches[1] : ($codeMatches[2] ?? '');

                            if ($raw) {
                                // Normalize
                                $upperRaw = strtoupper(str_replace(' ', '', $raw));

                                // 1. Handle PC1-TMDHM variations
                                if (str_contains($upperRaw, 'TMD') && str_contains($upperRaw, 'PC')) {
                                    preg_match('/PC(\d+)/', $upperRaw, $pcM);
                                    $num = $pcM[1] ?? '1';
                                    $kehadiran = "PC{$num}-TMDHM";
                                }
                                // 2. Handle TM3PC1 concatenated variations
                                elseif (preg_match('/^(TM\d+)(PC\d+)$/', $upperRaw, $splitM) || preg_match('/^(PC\d+)(TM\d+)$/', $upperRaw, $splitM)) {
                                    $kehadiran = $upperRaw;
                                }
                                // 3. Standard
                                else {
                                    $kehadiran = $upperRaw;
                                }

                                // Specific check for PC number preservation
                                if (str_contains($upperRaw, 'TMDHM') && str_contains($upperRaw, 'PC') && !str_contains($kehadiran, '-')) {
                                    preg_match('/PC(\d+)/', $upperRaw, $pcM);
                                    $num = $pcM[1] ?? '1';
                                    $kehadiran = "PC{$num}-TMDHM";
                                }
                            }
                        }

                        // Only proceed if a recognized attendance code is found
                        if ($kehadiran) {
                            preg_match_all('/(\d{2}:\d{2})/', $line, $timeMatches);
                            $rawTimes = $timeMatches[1] ?? [];

                            // Point 1: Determine clock-time expectations based on the detected Kehadiran code
                            // Group 1: No clock times expected
                            if (in_array($kehadiran, ['TK', 'LN', 'LJ', 'S', 'I', 'DL', 'C'])) {
                                $jamMasuk = null;
                                $jamPulang = null;
                            }
                            // Group 2: Only Pulang expected (e.g., missed morning scan)
                            elseif (str_contains($kehadiran, 'TMDHM') || str_contains($kehadiran, 'PC1-TMDHM')) {
                                // The FIRST HH:mm appearing on a TMDHM line is the Jam Pulang.
                                // Subsequent times (like Telat Masuk duration) are ignored.
                                $jamPulang = $rawTimes[0] ?? null;
                                $jamMasuk = null;
                            }
                            // Group 3: Only Masuk expected (e.g., missed afternoon scan)
                            elseif (str_contains($kehadiran, 'TMDHP')) {
                                // The FIRST HH:mm appearing on a TMDHP line is the Jam Masuk.
                                $jamMasuk = $rawTimes[0] ?? null;
                                $jamPulang = null;
                            }
                            // Group 4: Standard presence expecting both or unknown presence codes
                            else {
                                // In BKN reports, clock times appear first, followed by durations (Telat/PC).
                                // We take the first two matches as Masuk and Pulang.
                                if (count($rawTimes) >= 2) {
                                    $jamMasuk  = $rawTimes[0];
                                    $jamPulang = $rawTimes[1];
                                } elseif (count($rawTimes) === 1) {
                                    // Fallback for single scan: use hour to guess if it's morning or afternoon
                                    $t1 = $rawTimes[0];
                                    $h1 = (int)explode(':', $t1)[0];
                                    if ($h1 < 12) {
                                        $jamMasuk = $t1;
                                        $jamPulang = null;
                                    } else {
                                        $jamPulang = $t1;
                                        $jamMasuk = null;
                                    }
                                }
                            }

                            // Calculate Telat / Pulang Cepat - STRICT SYSTEM CALCULATION
                            // Ketentuan: Masuk 08:00 WIB, Pulang 16:30 WIB
                            $telatFormatted = '-';
                            $pulangCepatFormatted = '-';

                            // 1. Telat Masuk Calculation
                            if ($jamMasuk && !in_array($kehadiran, ['TK', 'LN', 'LJ'])) {
                                $masukVs = Carbon::parse($jamMasuk);
                                if ($masukVs->format('H:i:s') > '08:00:00') {
                                    $limitSeconds = 8 * 3600;
                                    $parts = explode(':', $masukVs->format('H:i:s'));
                                    $currentSeconds = ($parts[0] * 3600) + ($parts[1] * 60) + $parts[2];
                                    $diff = $currentSeconds - $limitSeconds;
                                    $telatFormatted = sprintf('%02d:%02d', intdiv($diff, 3600), intdiv($diff % 3600, 60));
                                }
                            }

                            // 2. Pulang Cepat Calculation
                            if ($jamPulang && !in_array($kehadiran, ['TK', 'LN', 'LJ'])) {
                                $pulangVs = Carbon::parse($jamPulang);
                                if ($pulangVs->format('H:i:s') < '16:30:00') {
                                    $limitSeconds = (16 * 3600) + (30 * 60);
                                    $parts = explode(':', $pulangVs->format('H:i:s'));
                                    $currentSeconds = ($parts[0] * 3600) + ($parts[1] * 60) + $parts[2];
                                    $diff = $limitSeconds - $currentSeconds;
                                    $pulangCepatFormatted = sprintf('%02d:%02d', intdiv($diff, 3600), intdiv($diff % 3600, 60));
                                }
                            }

                            // 3. Build keterangan - STRICTLY BASED ON KEHADIRAN CODE
                            $keteranganStr = Absensi::getKeteranganByCode($kehadiran);

                            $allParsedResults[] = [
                                '_index' => $globalIndex++,
                                'nip' => $nip,
                                'nama' => $nama,
                                'unit' => $unitKerja,
                                'tanggal' => Carbon::parse($tanggalStr)->format('d-m-Y'),
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
            }



            // Sort by NIP then Tanggal (Proper Date Comparison)
            usort($allParsedResults, function ($a, $b) {
                if ($a['nip'] === $b['nip']) {
                    $da = Carbon::createFromFormat('d-m-Y', $a['tanggal']);
                    $db = Carbon::createFromFormat('d-m-Y', $b['tanggal']);
                    return $da <=> $db;
                }
                return strcmp($a['nip'], $b['nip']);
            });

            // Re-map after sorting to ensure _index matches the array key for Livewire binding
            $allParsedResults = collect($allParsedResults)->map(function ($item, $key) {
                $item['_index'] = $key;
                return $item;
            })->toArray();

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
                // Parse date from d-m-Y format as set in parsePdf
                $tanggal = Carbon::createFromFormat('d-m-Y', $data['tanggal'])->format('Y-m-d');

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
