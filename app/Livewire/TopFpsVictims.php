<?php

namespace App\Livewire;

use App\Services\LeaderboardService;
use Illuminate\Support\Collection;
use Livewire\Component;

class TopFpsVictims extends Component
{
    public Collection $victims;

    public function mount(LeaderboardService $leaderboardService): void
    {
        $this->victims = $leaderboardService->getLeaderboards()['top_fps_victims'];
    }

    public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
    {
        return view('livewire.top-fps-victims');
    }
}
