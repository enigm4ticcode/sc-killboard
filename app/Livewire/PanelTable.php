<?php

namespace App\Livewire;

use App\Models\Kill;
use Illuminate\Support\Collection;
use Livewire\Component;

class PanelTable extends Component
{
    public Collection $kills;

    public string $date;

    public function mount(string $date): void
    {
        $this->date = $date;
        $this->kills = Kill::query()
            ->whereDate('destroyed_at', $date)
            ->orderBy('id', 'desc')
            ->get();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.panel-table');
    }
}
