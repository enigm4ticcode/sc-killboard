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
            ->with([
                'victim.organization',
                'killer.organization',
                'ship',
                'weapon.manufacturer',
            ])
            ->whereBetween('destroyed_at', [$startOfFeed, $now])
            ->orderByDesc('destroyed_at')
            ->get();

        // Use TTL-based caching instead of forever (15 minutes)
        Cache::put($this->cacheKey, $kills, now()->addMinutes(15));

        return $kills;
    }
}
