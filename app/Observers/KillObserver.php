<?php

namespace App\Observers;

use App\Models\Kill;
use App\Services\LeaderboardService;
use App\Services\RecentKillsService;

class KillObserver
{
    public function __construct(
        protected RecentKillsService $recentKillsService,
        protected LeaderboardService $leaderboardService,
    ) {}

    /**
     * Handle the Kill "created" event.
     */
    public function created(Kill $kill): void
    {
        // Cache refresh moved to batch processing completion to avoid N refreshes
        // Cache will be refreshed manually after batchProcessKills() completes
        // Individual kill creations (e.g. via API) can manually refresh if needed
    }

    /**
     * Handle the Kill "updated" event.
     */
    public function updated(Kill $kill): void
    {
        // Refresh cache on updates (less frequent than creates)
        $this->recentKillsService->refreshCache();
        $this->leaderboardService->refreshLeaderboards();
    }

    /**
     * Handle the Kill "deleted" event.
     */
    public function deleted(Kill $kill): void
    {
        // Refresh cache on deletes (less frequent than creates)
        $this->recentKillsService->refreshCache();
        $this->leaderboardService->refreshLeaderboards();
    }

    /**
     * Handle the Kill "restored" event.
     */
    public function restored(Kill $kill): void
    {
        // Refresh cache on restores (rare event)
        $this->recentKillsService->refreshCache();
        $this->leaderboardService->refreshLeaderboards();
    }

    /**
     * Handle the Kill "force deleted" event.
     */
    public function forceDeleted(Kill $kill): void
    {
        // Refresh cache on force deletes (rare event)
        $this->recentKillsService->refreshCache();
        $this->leaderboardService->refreshLeaderboards();
    }
}
