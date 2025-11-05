<?php

namespace App\Livewire;

use App\Services\LeaderboardService;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class BigKills extends Component
{
    public Collection $bigKills;

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
        $this->bigKills = $this->leaderboardService->getLeaderboards()['big_kills'];
    }

    public function render(): View
    {
        return view('livewire.big-kills');
    }
}
