<?php

namespace App\Livewire;

use App\Services\LeaderboardService;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class TopFpsKillers extends Component
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

        $this->killers = $this->leaderboardService->getLeaderboards()['top_fps_killers'];
    }

    public function render(): View
    {
        return view('livewire.top-fps-killers');
    }
}
