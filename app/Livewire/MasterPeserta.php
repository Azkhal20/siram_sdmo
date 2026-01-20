<?php

namespace App\Livewire;

use App\Models\PesertaMagang;
use App\Models\Kedeputian;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class MasterPeserta extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $showModal = false;
    public $pesertaId, $nama, $nomor_induk, $kedeputian_id;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function rules()
    {
        return [
            'nama' => 'required|string|max:255',
            'nomor_induk' => 'required|string|max:50|unique:peserta_magang,nomor_induk,' . $this->pesertaId,
            'kedeputian_id' => 'required|exists:kedeputians,id',
        ];
    }

    public function create()
    {
        $this->reset(['pesertaId', 'nama', 'nomor_induk', 'kedeputian_id']);
        $this->showModal = true;
    }

    public function edit($id)
    {
        $peserta = PesertaMagang::findOrFail($id);
        $this->pesertaId = $peserta->id;
        $this->nama = $peserta->nama;
        $this->nomor_induk = $peserta->nomor_induk;
        $this->kedeputian_id = $peserta->kedeputian_id;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $peserta = PesertaMagang::updateOrCreate(
            ['id' => $this->pesertaId],
            [
                'nama' => $this->nama,
                'nomor_induk' => $this->nomor_induk,
                'kedeputian_id' => $this->kedeputian_id,
            ]
        );

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $this->pesertaId ? 'Update Peserta' : 'Create Peserta',
            'model_type' => 'PesertaMagang',
            'model_id' => $peserta->id,
            'description' => 'Mengubah data peserta: ' . $peserta->nama,
            'ip_address' => request()->ip(),
        ]);

        session()->flash('message', $this->pesertaId ? 'Data berhasil diupdate.' : 'Data berhasil ditambah.');
        $this->showModal = false;
    }

    public function delete($id)
    {
        $peserta = PesertaMagang::findOrFail($id);
        $namaPeserta = $peserta->nama;
        $peserta->delete();

        session()->flash('message', 'Data berhasil dihapus.');

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Delete Peserta',
            'model_type' => 'PesertaMagang',
            'model_id' => $id,
            'description' => 'Menghapus data peserta: ' . $namaPeserta,
            'ip_address' => request()->ip(),
        ]);
    }

    public function render()
    {
        return view('livewire.master-peserta', [
            'pesertas' => PesertaMagang::with('kedeputian')
                ->where('nama', 'like', '%' . $this->search . '%')
                ->orWhere('nomor_induk', 'like', '%' . $this->search . '%')
                ->paginate($this->perPage),
            'kedeputians' => Kedeputian::all(),
        ]);
    }
}
