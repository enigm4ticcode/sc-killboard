<?php

namespace App\Livewire;

use App\Models\Kill;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TopWeapon extends Component
{
    public Collection $weapons;

    public function mount()
    {
        $now = Carbon::now();
        $this->weapons = Kill::query()
            ->select('weapon_id', DB::raw('count(*) as weapon_kill_count'))
            ->where('destroyed_at', '>=', Carbon::now()->subWeek()->startOfWeek())
            ->groupBy('weapon_id')
            ->orderBy('weapon_kill_count', 'desc')
            ->get();
    }
    public function render()
    {
        return view('livewire.top-weapon');
    }
}
