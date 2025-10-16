<?php

namespace App\Livewire;

use App\Models\Kill;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TopKiller extends Component
{
    public Collection $killers;

    public function mount(): void
    {
        $ttl = config('killboard.cache.ttl');

        $this->killers = Cache::remember('top-killers', $ttl, function () {
            return Kill::query()
                ->select('killer_id', DB::raw('count(*) as kill_count'))
                ->where('destroyed_at', '>=', Carbon::now()->subWeek()->startOfWeek())
                ->groupBy('killer_id')
                ->orderBy('kill_count', 'desc')
                ->take(10)
                ->get();
        });
    }

    public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
    {
        return view('livewire.top-killer');
    }
}
