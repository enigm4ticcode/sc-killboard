<?php

namespace App\Services;

use App\Models\Kill;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class RecentKillsService
{
    protected string $cacheKey;

    protected int $recentKillsDays;

    protected int $killsPerPage;

    public function __construct(string $cacheKey, int $recentKillsDays, int $killsPerPage)
    {
        $this->cacheKey = $cacheKey;
        $this->recentKillsDays = $recentKillsDays;
        $this->killsPerPage = $killsPerPage;
    }

    public function getRecentKills(): Collection
    {
        return Cache::get($this->cacheKey) ?? $this->refreshCache();
    }

    public function refreshCache(): Collection
    {
        Cache::forget($this->cacheKey);
        $now = now();
        $startOfFeed = $now->copy()->subDays($this->recentKillsDays)->startOfDay();

        $kills = Kill::query()
            ->whereBetween('destroyed_at', [$startOfFeed, $now])
            ->orderByDesc('destroyed_at')
            ->get();

        Cache::forever($this->cacheKey, $kills);

        return $kills;
    }
}
