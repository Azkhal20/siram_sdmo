<?php

namespace App\Livewire;

use App\Models\PesertaMagang;
use App\Models\Kedeputian;
use Livewire\Component;
use Livewire\WithPagination;

class MasterPeserta extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $pesertaId, $nama, $no_induk, $univ, $kedeputian_id;

    public function rules()
    {
        return [
            'nama' => 'required|string|max:255',
            'no_induk' => 'required|string|max:50|unique:peserta_magangs,no_induk,' . $this->pesertaId,
            'univ' => 'nullable|string|max:255',
            'kedeputian_id' => 'required|exists:kedeputians,id',
        ];
    }

    public function create()
    {
        $this->reset(['pesertaId', 'nama', 'no_induk', 'univ', 'kedeputian_id']);
        $this->showModal = true;
    }

    public function edit($id)
    {
        $peserta = PesertaMagang::findOrFail($id);
        $this->pesertaId = $peserta->id;
        $this->nama = $peserta->nama;
        $this->no_induk = $peserta->no_induk;
        $this->univ = $peserta->univ;
        $this->kedeputian_id = $peserta->kedeputian_id;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        PesertaMagang::updateOrCreate(
            ['id' => $this->pesertaId],
            [
                'nama' => $this->nama,
                'no_induk' => $this->no_induk,
                'univ' => $this->univ,
                'kedeputian_id' => $this->kedeputian_id,
            ]
        );

        session()->flash('message', $this->pesertaId ? 'Data berhasil diupdate.' : 'Data berhasil ditambah.');
        $this->showModal = false;
    }

    public function delete($id)
    {
        PesertaMagang::findOrFail($id)->delete();
        session()->flash('message', 'Data berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.master-peserta', [
            'pesertas' => PesertaMagang::with('kedeputian')
                ->where('nama', 'like', '%' . $this->search . '%')
                ->orWhere('no_induk', 'like', '%' . $this->search . '%')
                ->paginate(10),
            'kedeputians' => Kedeputian::all(),
        ]);
    }
}
