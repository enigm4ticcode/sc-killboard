<?php

namespace App\Livewire;

use App\Services\LeaderboardService;
use Illuminate\Support\Collection;
use Livewire\Component;

class TopKiller extends Component
{
    public Collection $killers;

    public function mount(LeaderboardService $leaderboardService): void
    {
        $this->killers = $leaderboardService->getLeaderboards()['top_vehicle_killers'];
    }

    public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
    {
        return view('livewire.top-killer');
    }
}
