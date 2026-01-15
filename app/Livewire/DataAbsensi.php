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

                        $codesPattern = '/\b(TK|TM|PC|TMDHM|HN|LN|LJ|S|I|DL|C)\b/i';
                        if (preg_match($codesPattern, $line, $codeMatches)) {
                            $kehadiran = strtoupper($codeMatches[1]);

                            preg_match_all('/(\d{2}:\d{2})/', $line, $timeMatches);
                            $times = $timeMatches[1] ?? [];

                            $jamMasuk = null;
                            $jamPulang = null;
                            $menitTelat = 0;

                            if ($kehadiran === 'HN') {
                                $jamMasuk = $times[0] ?? null;
                                $jamPulang = $times[1] ?? null;
                            } elseif ($kehadiran === 'TMDHM') {
                                $jamPulang = $times[0] ?? null;
                                $telatStr = $times[1] ?? '00:00';
                                $menitTelat = $this->timeToMinutes($telatStr);
                            } else {
                                if (count($times) >= 2) {
                                    $jamMasuk = $times[0];
                                    $jamPulang = $times[1];
                                } elseif (count($times) === 1) {
                                    if ($kehadiran === 'TM') $menitTelat = $this->timeToMinutes($times[0]);
                                    else $jamMasuk = $times[0];
                                }
                            }

                            $allParsedResults[] = [
                                '_index' => $globalIndex++,
                                'nip' => $nip,
                                'nama' => $nama,
                                'unit' => $unitKerja,
                                'tanggal' => Carbon::parse($tanggalStr)->format('Y-m-d'),
                                'kehadiran' => $kehadiran,
                                'jam_masuk' => $jamMasuk,
                                'jam_pulang' => $jamPulang,
                                'menit_telat' => $menitTelat,
                                'keterangan' => '',
                            ];
                        }
                    }
                }
            }

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
                'user_id' => auth()->id(),
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