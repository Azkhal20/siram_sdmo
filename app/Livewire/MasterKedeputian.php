<?php

namespace App\Livewire;

use App\Models\Kedeputian;
use App\Models\ActivityLog;
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

        $isUpdate = $this->kedeputianId ? true : false;

        $kedeputian = Kedeputian::updateOrCreate(
            ['id' => $this->kedeputianId],
            ['nama' => $this->nama]
        );

        // ✅ CATAT LOG AKTIVITAS
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $isUpdate ? 'Update Kedeputian' : 'Create Kedeputian',
            'model_type' => 'Kedeputian',
            'model_id' => $kedeputian->id,
            'description' => ($isUpdate ? 'Mengubah' : 'Menambah') . ' kedeputian: ' . $kedeputian->nama,
            'ip_address' => request()->ip(),
        ]);

        $this->showModal = false;
        session()->flash('message', 'Kedeputian berhasil disimpan.');
    }

    public function delete($id)
    {
        $kedeputian = Kedeputian::findOrFail($id);
        $namaKedeputian = $kedeputian->nama;

        $kedeputian->delete();

        // ✅ CATAT LOG AKTIVITAS
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Delete Kedeputian',
            'model_type' => 'Kedeputian',
            'model_id' => $id,
            'description' => 'Menghapus kedeputian: ' . $namaKedeputian,
            'ip_address' => request()->ip(),
        ]);

        session()->flash('message', 'Kedeputian berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.master-kedeputian', [
            'kedeputians' => Kedeputian::withCount('pesertaMagang')->get(),
        ]);
    }
}