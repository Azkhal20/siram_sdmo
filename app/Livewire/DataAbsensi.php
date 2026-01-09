<?php

namespace App\Livewire;

use App\Models\Absensi;
use App\Models\Kedeputian;
use App\Models\PesertaMagang;
use Livewire\Component;
use Livewire\WithFileUploads;
use Smalot\PdfParser\Parser;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DataAbsensi extends Component
{
    use WithFileUploads;

    public $pdfFile;
    public $previewData = [];
    public $isProcessing = false;
    public $showPreview = false;

    public function updatedPdfFile()
    {
        Log::info('File updated: ' . ($this->pdfFile ? $this->pdfFile->getClientOriginalName() : 'NULL'));

        $this->validate([
            'pdfFile' => 'required|mimes:pdf|max:20480',
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

            foreach ($pages as $index => $page) {
                $text = $page->getText();
                // Normalize text: replace multiple spaces/tabs with single space
                $text = preg_replace('/\s+/', ' ', $text);
                $lines = explode(' ', $text); // Try splitting by space for very messy text

                // Also keep the line-by-line version for structured table reading
                $structuredLines = explode("\n", $page->getText());

                Log::info("Page $index text block: " . substr($text, 0, 500));

                // --- 1. Extract Header Context (Page-level) ---
                $nip = 'N/A';
                $nama = 'Peserta Tidak Terdeteksi';
                $unitKerja = 'Unit Kerja Tidak Terdeteksi';

                foreach ($structuredLines as $sLine) {
                    $sLine = trim($sLine);
                    if (preg_match('/NIP\s*:\s*(\d+)/i', $sLine, $m)) $nip = $m[1];
                    if (preg_match('/Nama\s*:\s*([^\n:]+)/i', $sLine, $m)) $nama = trim($m[1]);
                    if (preg_match('/Unit Kerja\s*:\s*([^\n:]+)/i', $sLine, $m)) $unitKerja = trim($m[1]);
                }

                // --- 2. Extract Table Rows (Date-based) ---
                foreach ($structuredLines as $line) {
                    $line = trim($line);

                    // Regex for DD-MM-YYYY or DD/MM/YYYY
                    if (preg_match('/(\d{2}[\-\/]\d{2}[\-\/]\d{4})/', $line, $dateMatches)) {
                        $tanggalRaw = $dateMatches[1];
                        $tanggalStr = str_replace('/', '-', $tanggalRaw);

                        // Now find the Attendance Code in this line
                        // Possible codes: TK, TM, PC, TMDHM, HN, LN, LJ, S, I, DL, C
                        $codesPattern = '/\b(TK|TM|PC|TMDHM|HN|LN|LJ|S|I|DL|C)\b/i';
                        if (preg_match($codesPattern, $line, $codeMatches)) {
                            $kode = strtoupper($codeMatches[1]);

                            // Extract times (HH:MM)
                            preg_match_all('/(\d{2}:\d{2})/', $line, $timeMatches);
                            $times = $timeMatches[1] ?? [];

                            $jamMasuk = null;
                            $jamPulang = null;
                            $menitTelat = 0;

                            if ($kode === 'HN') {
                                $jamMasuk = $times[0] ?? null;
                                $jamPulang = $times[1] ?? null;
                            } elseif ($kode === 'TMDHM') {
                                $jamPulang = $times[0] ?? null;
                                $telatStr = $times[1] ?? '00:00';
                                $menitTelat = $this->timeToMinutes($telatStr);
                            } else {
                                if (count($times) >= 2) {
                                    $jamMasuk = $times[0];
                                    $jamPulang = $times[1];
                                } elseif (count($times) === 1) {
                                    if ($kode === 'TM') $menitTelat = $this->timeToMinutes($times[0]);
                                    else $jamMasuk = $times[0];
                                }
                            }

                            $allParsedResults[] = [
                                'nip' => $nip,
                                'nama' => $nama,
                                'unit' => $unitKerja,
                                'tanggal' => Carbon::parse($tanggalStr)->format('Y-m-d'),
                                'kode' => $kode,
                                'jam_masuk' => $jamMasuk,
                                'jam_pulang' => $jamPulang,
                                'menit_telat' => $menitTelat,
                            ];
                        }
                    }
                }
            }

            if (empty($allParsedResults)) {
                Log::error('No results found in PDF parsing.');
                session()->flash('error', 'Tidak ditemukan data absensi. Pastikan PDF memiliki tabel dengan kolom Tanggal dan Kehadiran.');
            } else {
                $this->previewData = $allParsedResults;
                $this->showPreview = true;
                Log::info('Parsing Success: ' . count($allParsedResults) . ' rows.');
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
                $kd = Kedeputian::firstOrCreate(['nama' => $data['unit']]);

                $peserta = PesertaMagang::updateOrCreate(
                    ['nomor_induk' => $data['nip']],
                    ['nama' => $data['nama'], 'kedeputian_id' => $kd->id]
                );

                Absensi::updateOrCreate(
                    ['peserta_magang_id' => $peserta->id, 'tanggal' => $data['tanggal']],
                    [
                        'kode' => $data['kode'],
                        'jam_masuk' => $data['jam_masuk'],
                        'jam_pulang' => $data['jam_pulang'],
                        'menit_telat' => $data['menit_telat'],
                    ]
                );
            }
            DB::commit();
            session()->flash('success', 'Berhasil menyimpan ' . count($this->previewData) . ' data.');
            $this->reset(['pdfFile', 'previewData', 'showPreview']);
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
