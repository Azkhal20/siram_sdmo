<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ActivityLog;

class LogAktivitas extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 15;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $logs = ActivityLog::with('user')
            ->where(function($query) {
                $query->where('action', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.log-aktivitas', ['logs' => $logs]);
    }
}