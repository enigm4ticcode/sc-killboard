<?php

namespace App\Livewire;

use App\Models\Kill;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TopWeapon extends Component
{
    public Collection $weapons;

    public function mount(): void
    {
        $ttl = config('killboard.cache.ttl');

        $this->weapons = Cache::remember('top-weapon', $ttl, function () {
            return Kill::query()
                ->select('weapon_id', DB::raw('count(*) as weapon_kill_count'))
                ->where('destroyed_at', '>=', Carbon::now()->subWeek()->startOfWeek())
                ->groupBy('weapon_id')
                ->orderBy('weapon_kill_count', 'desc')
                ->get();
        });
    }

    public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
    {
        return view('livewire.top-weapon');
    }
}
