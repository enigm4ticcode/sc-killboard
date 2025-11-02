<?php

use App\Models\Kill;
use App\Models\Player;
use App\Models\Weapon;
use App\Services\RecentKillsService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

it('refreshes cache with recent kills within the configured window and orders by newest first', function (): void {
    $cacheKey = 'test_recent_kills_'.uniqid();

    $weapon = Weapon::query()->create([
        'slug' => 'p4-ar',
        'name' => 'P4-AR',
    ]);

    $alice = Player::query()->create(['name' => 'Alice']);
    $bob = Player::query()->create(['name' => 'Bob']);

    // in range (now - 1 day)
    $k1 = Kill::query()->create([
        'destroyed_at' => Carbon::now()->subDay(),
        'weapon_id' => $weapon->id,
        'victim_id' => $bob->id,
        'killer_id' => $alice->id,
        'type' => 'fps',
        'location' => 'Area18',
    ]);

    // out of range (now - 10 days)
    $k2 = Kill::query()->create([
        'destroyed_at' => Carbon::now()->subDays(10),
        'weapon_id' => $weapon->id,
        'victim_id' => $alice->id,
        'killer_id' => $bob->id,
        'type' => 'fps',
        'location' => 'Orison',
    ]);

    // in range (now - 2 hours), should be first in order
    $k3 = Kill::query()->create([
        'destroyed_at' => Carbon::now()->subHours(2),
        'weapon_id' => $weapon->id,
        'victim_id' => $alice->id,
        'killer_id' => $bob->id,
        'type' => 'fps',
        'location' => 'GrimHEX',
    ]);

    $service = new RecentKillsService(cacheKey: $cacheKey, recentKillsDays: 3, killsPerPage: 30);

    $result = $service->refreshCache();

    expect($result)->toHaveCount(2);
    expect($result->first()->id)->toBe($k3->id);
    expect($result->last()->id)->toBe($k1->id);

    // Ensure it was cached
    $fromCache = Cache::get($cacheKey);
    expect($fromCache)->not->toBeNull()->toHaveCount(2);
});

it('returns cached recent kills when available without recalculating', function (): void {
    $cacheKey = 'test_recent_kills_'.uniqid();

    $service = new RecentKillsService(cacheKey: $cacheKey, recentKillsDays: 3, killsPerPage: 30);

    $stub = collect([['id' => 1], ['id' => 2]]);

    Cache::forever($cacheKey, $stub);

    $result = $service->getRecentKills();

    // Should match the cached value exactly
    expect($result->toArray())->toBe($stub->toArray());
});
