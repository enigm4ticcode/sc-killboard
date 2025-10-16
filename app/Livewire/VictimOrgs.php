<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class VictimOrgs extends Component
{
    public Collection $orgs;

    public function mount(): void
    {
        $ttl = config('killboard.cache.ttl');

        $this->orgs = Cache::remember('victim-organizations', $ttl, function () {
            return DB::table('kills')
                ->join('players', 'kills.victim_id', '=', 'players.id')
                ->join('organizations', 'players.organization_id', '=', 'organizations.id')
                ->where('kills.destroyed_at', '>=', Carbon::now()->subWeek()->startOfDay())
                ->groupBy('organizations.id', 'organizations.name', 'organizations.icon')
                ->select(
                    'organizations.name as organization_name',
                    'organizations.icon as organization_icon',
                    DB::raw('COUNT(kills.id) as total_deaths'),
                    DB::raw('COUNT(DISTINCT players.id) as total_players'),
                    DB::raw('COUNT(kills.id) / COUNT(DISTINCT players.id) as average_deaths_per_player')
                )
                ->orderByDesc('average_deaths_per_player')
                ->take(10)
                ->get();
        });
    }

    public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
    {
        return view('livewire.victim-orgs');
    }
}
