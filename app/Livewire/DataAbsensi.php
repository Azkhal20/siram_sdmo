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

class DataAbsensi extends Component
{
    use WithFileUploads;

    public $pdfFile;
    public $previewData = [];
    public $isProcessing = false;
    public $showPreview = false;

    public function updatedPdfFile()
    {
        $this->validate([
            'pdfFile' => 'required|mimes:pdf|max:10240',
        ]);

        $this->parsePdf();
    }

    public function parsePdf()
    {
        $this->isProcessing = true;

        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($this->pdfFile->getRealPath());
            $text = $pdf->getText();

            // Logic parsing sederhana: cari baris yang mengandung kode absensi
            // Format yang diharapkan biasanya ada Nama, Tanggal, dan Kode
            $lines = explode("\n", $text);
            $parsedResults = [];

            foreach ($lines as $line) {
                // Contoh deteksi sederhana berdasarkan keyword kode absensi
                // (Ini perlu disesuaikan dengan format PDF asli BKN nantinya)
                if (preg_match('/(TK|TM|PC|TMDHM|S|I|DL)/', $line, $matches)) {
                    // Prediksi format: [Nama] [Tanggal] [Kode]
                    // Kita kumpulkan dulu untuk preview
                    $parsedResults[] = [
                        'raw' => $line,
                        'kode' => $matches[0],
                        'tanggal' => now()->format('Y-m-d'), // Placeholder
                        'nama' => trim(str_replace($matches[0], '', $line)), // Placeholder
                    ];
                }
            }

            $this->previewData = $parsedResults;
            $this->showPreview = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memproses PDF: ' . $e->getMessage());
        }

        $this->isProcessing = false;
    }

    public function saveData()
    {
        if (empty($this->previewData)) return;

        DB::beginTransaction();
        try {
            foreach ($this->previewData as $data) {
                // Cari atau buat peserta
                $peserta = PesertaMagang::firstOrCreate(
                    ['nama' => $data['nama']],
                    ['kedeputian_id' => Kedeputian::first()->id ?? 1] // Default
                );

                Absensi::updateOrCreate(
                    [
                        'peserta_magang_id' => $peserta->id,
                        'tanggal' => $data['tanggal']
                    ],
                    [
                        'kode' => $data['kode'],
                        'jam_masuk' => '08:00:00',
                        'jam_pulang' => '16:00:00',
                    ]
                );
            }

            DB::commit();
            session()->flash('success', count($this->previewData) . ' data absensi berhasil disimpan!');
            $this->reset(['pdfFile', 'previewData', 'showPreview']);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan saat menyimpan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.data-absensi');
    }
}
