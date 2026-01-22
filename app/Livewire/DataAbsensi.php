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

    private $jamMasukNormal = '08:00';
    private $jamPulangNormal = '16:30';

    // Daftar kode kehadiran (urutan penting: yang lebih panjang dulu)
    private $kodeKehadiran = [
        'TMDHM',
        'TMDHP',
        'TM1',
        'TM2',
        'TM3',
        'TM',
        'PC1',
        'PC2',
        'PC3',
        'PC',
        'HN',
        'TK',
        'DL',
        'LJ',
        'LN',
        'S',
        'I',
        'C',
        'K'
    ];

    public function mount()
    {
        $this->kedeputianList = Kedeputian::orderBy('nama')->get();
    }

    public function updatedPdfFile()
    {
        $this->validate([
            'selectedKedeputian' => 'required',
            'pdfFile' => 'required|mimes:pdf|max:20480',
        ], [
            'selectedKedeputian.required' => 'Mohon pilih Kedeputian terlebih dahulu.',
        ]);

        $this->parsePdf();
    }

    private function timeToMinutes(?string $timeStr): int
    {
        if (!$timeStr || !str_contains($timeStr, ':')) return 0;
        $timeStr = trim($timeStr);
        $parts = explode(':', $timeStr);
        if (count($parts) !== 2) return 0;
        return ((int)$parts[0] * 60) + (int)$parts[1];
    }

    private function minutesToHoursMinutes(int $minutes): string
    {
        if ($minutes <= 0) return '00:00';
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return sprintf('%02d:%02d', $hours, $mins);
    }

    private function getKeteranganKode(string $kode): string
    {
        return match (strtoupper($kode)) {
            'TK' => 'Tanpa Keterangan',
            'TMDHM' => 'Tidak Absen Masuk',
            'TMDHP' => 'Tidak Absen Pulang',
            'TM' => 'Terlambat Masuk',
            'TM1' => 'Terlambat < 30 menit',
            'TM2' => 'Terlambat > 30 menit',
            'TM3' => 'Terlambat > 1 jam',
            'PC' => 'Pulang Cepat',
            'PC1' => 'Pulang Cepat < 30 menit',
            'PC2' => 'Pulang Cepat > 30 menit',
            'PC3' => 'Pulang Cepat > 1 jam',
            'HN' => 'Hadir Normal',
            'DL' => 'Dinas Luar',
            'LJ' => 'Libur Sabtu/Minggu',
            'LN' => 'Libur Nasional',
            'S' => 'Sakit',
            'I' => 'Izin',
            'C' => 'Cuti',
            default => $kode,
        };
    }

    /**
     * Pecah kode kehadiran gabungan menjadi array kode tunggal
     * Contoh: 'TM3PC1' => ['TM3', 'PC1']
     * Contoh: 'TM3-PC1' => ['TM3', 'PC1']
     * Contoh: 'PC1-TMDHM' => ['PC1', 'TMDHM']
     */
    private function parseKodeGabungan(string $kodeGabungan): array
    {
        $kodeGabungan = strtoupper(trim($kodeGabungan));

        // Jika ada tanda '-', split berdasarkan '-'
        if (str_contains($kodeGabungan, '-')) {
            return array_map('trim', explode('-', $kodeGabungan));
        }

        // Jika tidak ada '-', coba parse kode yang digabung tanpa separator
        $hasil = [];
        $sisaKode = $kodeGabungan;

        while (!empty($sisaKode)) {
            $found = false;

            // Cari kode yang cocok (mulai dari yang terpanjang)
            foreach ($this->kodeKehadiran as $kode) {
                if (str_starts_with($sisaKode, $kode)) {
                    $hasil[] = $kode;
                    $sisaKode = substr($sisaKode, strlen($kode));
                    $found = true;
                    break;
                }
            }

            // Jika tidak ada yang cocok, keluar dari loop
            if (!$found) {
                // Jika masih ada sisa, tambahkan sebagai kode tunggal
                if (!empty($sisaKode)) {
                    $hasil[] = $sisaKode;
                }
                break;
            }
        }

        return !empty($hasil) ? $hasil : [$kodeGabungan];
    }

    private function getKeterangan(string $kodeGabungan): string
    {
        $kodeParts = $this->parseKodeGabungan($kodeGabungan);

        if (count($kodeParts) === 1) {
            $ket = $this->getKeteranganKode($kodeParts[0]);
            return !empty($ket) ? $ket . '.' : '';
        }

        $keteranganParts = [];
        foreach ($kodeParts as $kode) {
            $ket = $this->getKeteranganKode($kode);
            if (!empty($ket) && $ket !== $kode) {
                $keteranganParts[] = $ket;
            }
        }

        return !empty($keteranganParts) ? implode(' & ', $keteranganParts) . '.' : '';
    }

    private function parseKodeKehadiran(string $kodeGabungan): array
    {
        $result = [
            'ada_jam_masuk' => true,
            'ada_jam_pulang' => true,
            'is_libur' => false,
            'is_tidak_hadir' => false,
        ];

        $kodeParts = $this->parseKodeGabungan($kodeGabungan);

        foreach ($kodeParts as $part) {
            $part = strtoupper(trim($part));

            if ($part === 'TMDHM') {
                $result['ada_jam_masuk'] = false;
            }

            if ($part === 'TMDHP') {
                $result['ada_jam_pulang'] = false;
            }

            if (in_array($part, ['LJ', 'LN'])) {
                $result['is_libur'] = true;
                $result['ada_jam_masuk'] = false;
                $result['ada_jam_pulang'] = false;
            }

            if ($part === 'TK') {
                $result['is_tidak_hadir'] = true;
                $result['ada_jam_masuk'] = false;
                $result['ada_jam_pulang'] = false;
            }
        }

        return $result;
    }

    private function hitungTelatMasuk(?string $jamMasuk): array
    {
        if (!$jamMasuk || empty(trim($jamMasuk))) {
            return ['menit' => 0, 'str' => ''];
        }

        try {
            $jamMasuk = trim($jamMasuk);
            $masukMenit = $this->timeToMinutes($jamMasuk);
            $normalMenit = $this->timeToMinutes($this->jamMasukNormal);

            if ($masukMenit > $normalMenit) {
                $menitTelat = $masukMenit - $normalMenit;
                return [
                    'menit' => $menitTelat,
                    'str' => $this->minutesToHoursMinutes($menitTelat)
                ];
            }
        } catch (\Exception $e) {
            Log::error('Error hitung telat: ' . $e->getMessage());
        }

        return ['menit' => 0, 'str' => ''];
    }

    private function hitungPulangCepat(?string $jamPulang): array
    {
        if (!$jamPulang || empty(trim($jamPulang))) {
            return ['menit' => 0, 'str' => ''];
        }

        try {
            $jamPulang = trim($jamPulang);
            $pulangMenit = $this->timeToMinutes($jamPulang);
            $normalMenit = $this->timeToMinutes($this->jamPulangNormal);

            if ($pulangMenit < $normalMenit) {
                $menitCepat = $normalMenit - $pulangMenit;
                return [
                    'menit' => $menitCepat,
                    'str' => $this->minutesToHoursMinutes($menitCepat)
                ];
            }
        } catch (\Exception $e) {
            Log::error('Error hitung pulang cepat: ' . $e->getMessage());
        }

        return ['menit' => 0, 'str' => ''];
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
            $parser = new Parser();
            $pdfContent = $parser->parseFile($path);
            $pages = $pdfContent->getPages();

            $allParsedResults = [];
            $globalIndex = 0;

            // Build regex pattern untuk kode kehadiran
            // Pattern ini menangkap kode tunggal, gabungan dengan '-', atau gabungan tanpa separator
            $kodeList = implode('|', $this->kodeKehadiran);
            // Pattern: menangkap kombinasi kode (dengan atau tanpa '-')
            $kodePattern = '/\b((?:' . $kodeList . ')(?:[-]?(?:' . $kodeList . '))*)\b/i';

            foreach ($pages as $page) {
                $text = $page->getText();
                $lines = explode("\n", $text);

                $nip = 'N/A';
                $nama = 'Peserta Tidak Terdeteksi';
                $unitKerja = 'Unit Kerja Tidak Terdeteksi';

                // Parse header info
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (preg_match('/NIP\s*:\s*(\d+)/i', $line, $m)) {
                        $nip = $m[1];
                    }
                    if (preg_match('/Nama\s*:\s*([A-Z\s\.]+)/i', $line, $m)) {
                        $nama = trim($m[1]);
                    }
                    if (preg_match('/Unit Kerja\s*:\s*(.+)/i', $line, $m)) {
                        $unitKerja = trim($m[1]);
                    }
                }

                // Parse data absensi
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line)) continue;

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

                            // Determine clock-time expectations based on the detected Kehadiran code
                            if (in_array($kehadiran, ['TK', 'LN', 'LJ', 'S', 'I', 'DL', 'C'])) {
                                $jamMasuk = null;
                                $jamPulang = null;
                            } elseif (str_contains($kehadiran, 'TMDHM') || str_contains($kehadiran, 'PC1-TMDHM')) {
                                $jamPulang = $rawTimes[0] ?? null;
                                $jamMasuk = null;
                            } elseif (str_contains($kehadiran, 'TMDHP')) {
                                $jamMasuk = $rawTimes[0] ?? null;
                                $jamPulang = null;
                            } else {
                                if (count($rawTimes) >= 2) {
                                    $jamMasuk  = $rawTimes[0];
                                    $jamPulang = $rawTimes[1];
                                } elseif (count($rawTimes) === 1) {
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

                            // Calculate stats
                            $telatData = $this->hitungTelatMasuk($jamMasuk);
                            $pulangCepatData = $this->hitungPulangCepat($jamPulang);

                            $allParsedResults[] = [
                                '_index' => $globalIndex++,
                                'nip' => $nip,
                                'nama' => $nama,
                                'unit' => $unitKerja,
                                'tanggal' => Carbon::parse($tanggalStr)->format('d-m-Y'),
                                'kehadiran' => $kehadiran,
                                'jam_masuk' => $jamMasuk,
                                'jam_pulang' => $jamPulang,
                                'menit_telat' => $telatData['menit'],
                                'jam_telat_str' => $telatData['str'],
                                'menit_pulang_cepat' => $pulangCepatData['menit'],
                                'jam_pulang_cepat_str' => $pulangCepatData['str'],
                                'keterangan' => $this->getKeterangan($kehadiran),
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
                session()->flash('error', 'Tidak ditemukan data absensi.');
            } else {
                $this->previewData = $allParsedResults;
                $this->showPreview = true;
                session()->flash('success', 'Berhasil membaca ' . count($allParsedResults) . ' data.');
            }
        } catch (\Exception $e) {
            Log::error('Parsing Error: ' . $e->getMessage());
            session()->flash('error', 'Gagal memproses PDF: ' . $e->getMessage());
        }

        $this->isProcessing = false;
    }
    public function saveData()
    {
        if (empty($this->previewData)) {
            session()->flash('error', 'Tidak ada data untuk disimpan.');
            return;
        }

        DB::beginTransaction();
        try {
            $savedCount = 0;

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
                        'menit_telat' => $data['menit_telat'] ?? 0,
                        'menit_pulang_cepat' => $data['menit_pulang_cepat'] ?? 0,
                        'keterangan' => $data['keterangan'] ?? null,
                    ]
                );

                $savedCount++;
            }

            DB::commit();

            $kedeputian = Kedeputian::find($this->selectedKedeputian);
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Import Absensi',
                'model_type' => 'Absensi',
                'model_id' => null,
                'description' => 'Import ' . $savedCount . ' data absensi ke Kedeputian: ' . ($kedeputian->nama ?? 'Unknown'),
                'ip_address' => request()->ip(),
            ]);

            session()->flash('success', 'Berhasil menyimpan ' . $savedCount . ' data.');
            $this->reset(['pdfFile', 'previewData', 'showPreview', 'selectedKedeputian']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Save Error: ' . $e->getMessage());
            session()->flash('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.data-absensi');
    }
}
