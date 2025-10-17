<?php

namespace App\Observers;

use App\Models\Kill;
use App\Services\LeaderboardService;

class KillObserver
{
    public function __construct(protected LeaderboardService $leaderboardService) {}

    /**
     * Handle the Kill "created" event.
     */
    public function created(Kill $kill): void
    {
        $this->leaderboardService->refreshLeaderboards();
    }

    /**
     * Handle the Kill "updated" event.
     */
    public function updated(Kill $kill): void
    {
        $this->leaderboardService->refreshLeaderboards();
    }

    /**
     * Handle the Kill "deleted" event.
     */
    public function deleted(Kill $kill): void
    {
        $this->leaderboardService->refreshLeaderboards();
    }

    /**
     * Handle the Kill "restored" event.
     */
    public function restored(Kill $kill): void
    {
        $this->leaderboardService->refreshLeaderboards();
    }

    /**
     * Handle the Kill "force deleted" event.
     */
    public function forceDeleted(Kill $kill): void
    {
        $this->leaderboardService->refreshLeaderboards();
    }
}
