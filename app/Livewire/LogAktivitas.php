<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ActivityLog;

class LogAktivitas extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    // Reset pagination saat search atau perPage berubah
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        $logs = ActivityLog::with('user')
            ->where(function ($query) {
                $query->where('action', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.log-aktivitas', [
            'logs' => $logs,
        ]);
    }
}