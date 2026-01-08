<?php

namespace App\Livewire;

use App\Models\Kedeputian;
use Livewire\Component;

class MasterKedeputian extends Component
{
    public $nama;
    public $kedeputianId;
    public $showModal = false;

    public function create()
    {
        $this->reset(['nama', 'kedeputianId']);
        $this->showModal = true;
    }

    public function edit($id)
    {
        $kd = Kedeputian::findOrFail($id);
        $this->kedeputianId = $kd->id;
        $this->nama = $kd->nama;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate(['nama' => 'required|string|max:255']);

        Kedeputian::updateOrCreate(
            ['id' => $this->kedeputianId],
            ['nama' => $this->nama]
        );

        $this->showModal = false;
        session()->flash('message', 'Kedeputian berhasil disimpan.');
    }

    public function delete($id)
    {
        Kedeputian::findOrFail($id)->delete();
        session()->flash('message', 'Kedeputian berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.master-kedeputian', [
            'kedeputians' => Kedeputian::withCount('pesertaMagang')->get(),
        ]);
    }
}
