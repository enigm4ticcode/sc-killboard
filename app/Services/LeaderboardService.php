<?php

namespace App\Services;

use App\Models\Kill;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LeaderboardService
{
    protected string $cacheKey;

    protected int $numOfPositions;

    public function __construct(string $cacheKey, int $numOfPositions)
    {
        $this->cacheKey = $cacheKey;
        $this->numOfPositions = $numOfPositions;
    }

    public function getLeaderboards(): array
    {
        return Cache::rememberForever($this->cacheKey, function () {
            return [
                'top_vehicle_killers' => $this->getTopVehicleKillers(),
                'top_fps_killers' => $this->getTopFpsKillers(),
                'top_orgs' => $this->getTopOrgs(),
                'top_vehicle_victims' => $this->getTopVehicleVictims(),
                'top_fps_victims' => $this->getTopFpsVictims(),
                'top_weapons' => $this->getTopWeapons(),
                'top_victim_orgs' => $this->getTopVictimOrgs(),
            ];
        });
    }

    public function refreshLeaderboards(): array
    {
        Cache::forget($this->cacheKey);

        return $this->getLeaderboards();
    }

    private function getTopVehicleKillers(): Collection
    {
        return Kill::query()
            ->select('killer_id', DB::raw('count(*) as kill_count'))
            ->where('destroyed_at', '>=', Carbon::now()->subWeek()->startOfWeek())
            ->where('type', Kill::TYPE_VEHICLE)
            ->groupBy('killer_id')
            ->orderBy('kill_count', 'desc')
            ->take($this->numOfPositions)
            ->get();
    }

    private function getTopFpsKillers(): Collection
    {
        return Kill::query()
            ->select('killer_id', DB::raw('count(*) as kill_count'))
            ->where('destroyed_at', '>=', Carbon::now()->subWeek()->startOfWeek())
            ->where('type', Kill::TYPE_FPS)
            ->groupBy('killer_id')
            ->orderBy('kill_count', 'desc')
            ->take($this->numOfPositions)
            ->get();
    }

    private function getTopOrgs(): Collection
    {
        return DB::table('kills')
            ->join('players', 'kills.killer_id', '=', 'players.id')
            ->join('organizations', 'players.organization_id', '=', 'organizations.id')
            ->where('kills.destroyed_at', '>=', Carbon::now()->subWeek()->startOfDay())
            ->groupBy('organizations.id', 'organizations.name', 'organizations.icon')
            ->select(
                'organizations.name as organization_name',
                'organizations.icon as organization_icon',
                DB::raw('COUNT(kills.id) as total_kills'),
                DB::raw('COUNT(DISTINCT players.id) as total_players'),
                DB::raw('COUNT(kills.id) / COUNT(DISTINCT players.id) as average_kills_per_player')
            )
            ->orderByDesc('total_kills')
            ->take($this->numOfPositions)
            ->get();
    }

    private function getTopVehicleVictims(): Collection
    {
        return Kill::query()
            ->select('victim_id', DB::raw('count(*) as death_count'))
            ->where('destroyed_at', '>=', Carbon::now()->subWeek()->startOfWeek())
            ->where('type', Kill::TYPE_VEHICLE)
            ->groupBy('victim_id')
            ->orderBy('death_count', 'desc')
            ->take($this->numOfPositions)
            ->get();
    }

    private function getTopFpsVictims(): Collection
    {
        return Kill::query()
            ->select('victim_id', DB::raw('count(*) as death_count'))
            ->where('destroyed_at', '>=', Carbon::now()->subWeek()->startOfWeek())
            ->where('type', Kill::TYPE_FPS)
            ->groupBy('victim_id')
            ->orderBy('death_count', 'desc')
            ->take($this->numOfPositions)
            ->get();
    }

    private function getTopWeapons(): Collection
    {
        return Kill::query()
            ->select('weapon_id', DB::raw('count(*) as weapon_kill_count'))
            ->where('destroyed_at', '>=', Carbon::now()->subWeek()->startOfWeek())
            ->groupBy('weapon_id')
            ->orderBy('weapon_kill_count', 'desc')
            ->take($this->numOfPositions)
            ->get();
    }

    private function getTopVictimOrgs(): Collection
    {
        return DB::table('kills')
            ->join('players', 'kills.victim_id', '=', 'players.id')
            ->join('organizations', 'players.organization_id', '=', 'organizations.id')
            ->where('kills.destroyed_at', '>=', Carbon::now()->subWeek()->startOfDay())
            ->groupBy('organizations.id', 'organizations.name', 'organizations.icon')
            ->select(
                'organizations.name as organization_name',
                'organizations.icon as organization_icon',
                DB::raw('COUNT(kills.id) as total_deaths'),
                DB::raw('COUNT(DISTINCT players.id) as total_players'),
                DB::raw('COUNT(kills.id) / COUNT(DISTINCT players.id) as average_deaths_per_player')
            )
            ->orderByDesc('total_deaths')
            ->take($this->numOfPositions)
            ->get();
    }
}
