<?php

namespace App\Livewire;

use App\Services\LeaderboardService;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class TopKiller extends Component
{
    public Collection $killers;

    protected LeaderboardService $leaderboardService;

    public function mount(LeaderboardService $leaderboardService): void
    {
        $this->leaderboardService = $leaderboardService;
        $this->loadData();
    }

    #[On('killboard-updated')]
    public function refreshComponent(): void
    {
        $this->loadData();
    }

    private function loadData(): void
    {
        $this->killers = $this->leaderboardService->getLeaderboards()['top_vehicle_killers'];
    }

    public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
    {
        return view('livewire.top-killer');
    }
}
