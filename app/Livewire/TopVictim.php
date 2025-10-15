<?php

namespace App\Livewire;

use App\Models\Kill;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TopVictim extends Component
{
    public Collection $victims;

    public function mount(): void
    {
        $now = Carbon::now();
        $this->victims = Kill::query()
            ->select('victim_id', DB::raw('count(*) as death_count'))
            ->where('destroyed_at', '>=', Carbon::now()->subWeek()->startOfWeek())
            ->groupBy('victim_id')
            ->orderBy('death_count', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.top-victim');
    }
}
