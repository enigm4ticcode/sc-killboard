<?php

namespace App\Livewire;

use App\Services\LeaderboardService;
use Illuminate\Support\Collection;
use Livewire\Component;

class TopOrgs extends Component
{
    public Collection $orgs;

    public function mount(LeaderboardService $leaderboardService): void
    {
        $this->orgs = $leaderboardService->getLeaderboards()['top_orgs'];
    }

    public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
    {
        return view('livewire.top-orgs');
    }
}
