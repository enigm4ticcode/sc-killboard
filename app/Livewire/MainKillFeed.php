<?php

namespace App\Livewire;

use App\Models\Kill;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class MainKillFeed extends Component
{
    use WithPagination;

    protected $listeners = ['killboard-updated' => '$refresh'];

    public function render(): \Illuminate\View\View
    {
        return view('livewire.main-kill-feed', ['kills' => $this->loadData()]);
    }

    private function loadData(): LengthAwarePaginator
    {
        $mostRecentKillsDays = config('killboard.home_page.most_recent_kills_days');
        $killsPerPage = config('killboard.pagination.kills_per_page');
        $now = now();
        $startOfFeed = $now->copy()->subDays($mostRecentKillsDays)->startOfDay();

        return Kill::query()
            ->whereBetween('destroyed_at', [$startOfFeed, $now])
            ->orderByDesc('destroyed_at')
            ->paginate($killsPerPage);
    }
}
