<?php

namespace App\Livewire;

use App\Models\Kill;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TopKiller extends Component
{
    public Collection $killers;

    public function mount(): void
    {
        $this->killers = Kill::query()
            ->select('killer_id', DB::raw('count(*) as kill_count'))
            ->where('destroyed_at', '>=', Carbon::now()->subWeek()->startOfWeek())
            ->groupBy('killer_id')
            ->orderBy('kill_count', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.top-killer');
    }
}
