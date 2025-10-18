<?php

namespace App\Livewire;

use App\Services\LeaderboardService;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class TopWeapon extends Component
{
    public Collection $weapons;

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

    public function loadData(): void
    {
        $this->weapons = $this->leaderboardService->getLeaderboards()['top_weapons'];
    }

    public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
    {
        return view('livewire.top-weapon');
    }
}
