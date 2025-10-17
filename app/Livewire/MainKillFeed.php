<?php

namespace App\Livewire;

use App\Models\Kill;
use Livewire\Component;
use Livewire\WithPagination;

class MainKillFeed extends Component
{
    use WithPagination;

    public function render(): \Illuminate\View\View
    {
        $mostRecentKillsDays = config('killboard.home_page.most_recent_kills_days');
        $killsPerPage = config('killboard.pagination.kills_per_page');
        $now = now();
        $startOfFeed = $now->copy()->subDays($mostRecentKillsDays)->startOfDay();

        $data = Kill::query()
            ->whereBetween('destroyed_at', [$startOfFeed, $now])
            ->orderByDesc('destroyed_at')
            ->paginate($killsPerPage);

        return view('livewire.main-kill-feed', ['kills' => $data]);
    }
}
