<?php

use App\Models\Kill;
use App\Models\Organization;
use App\Models\Player;
use App\Models\Ship;
use App\Models\Weapon;
use App\Services\LeaderboardService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

it('builds leaderboards from recent kills and respects cache', function (): void {
    Cache::forget('leaderboards');

    // Players
    $orgA = Organization::create([
        'name' => 'Alpha Org',
        'spectrum_id' => 'ALPHA',
        'icon' => Organization::DEFAULT_ORG_PIC_URL,
    ]);
    $orgB = Organization::create([
        'name' => 'Beta Org',
        'spectrum_id' => 'BETA',
        'icon' => Organization::DEFAULT_ORG_PIC_URL,
    ]);

    $killer1 = Player::create(['name' => 'Killer One', 'organization_id' => $orgA->id]);
    $killer2 = Player::create(['name' => 'Killer Two', 'organization_id' => $orgB->id]);
    $victim1 = Player::create(['name' => 'Victim One', 'organization_id' => $orgA->id]);
    $victim2 = Player::create(['name' => 'Victim Two', 'organization_id' => $orgB->id]);

    // Assets
    $weapon1 = Weapon::create(['slug' => 'c54-ar', 'name' => 'C54 AR']);
    $weapon2 = Weapon::create(['slug' => 'p4-ar', 'name' => 'P4 AR']);
    $ship = Ship::create(['slug' => 'arrow', 'name' => 'Arrow', 'class_name' => 'AEGIS Arrow', 'version' => '1.0']);

    // Recent kills (within 7 days)
    $recent = Carbon::now()->subDays(1);
    Kill::create([
        'destroyed_at' => $recent,
        'ship_id' => null,
        'weapon_id' => $weapon1->id,
        'victim_id' => $victim1->id,
        'killer_id' => $killer1->id,
        'type' => Kill::TYPE_FPS,
        'location' => 'Orison',
    ]);
    Kill::create([
        'destroyed_at' => $recent,
        'ship_id' => $ship->id,
        'weapon_id' => $weapon2->id,
        'victim_id' => $victim2->id,
        'killer_id' => $killer1->id,
        'type' => Kill::TYPE_VEHICLE,
        'location' => 'Yela',
    ]);
    Kill::create([
        'destroyed_at' => $recent,
        'ship_id' => null,
        'weapon_id' => $weapon1->id,
        'victim_id' => $victim2->id,
        'killer_id' => $killer2->id,
        'type' => Kill::TYPE_FPS,
        'location' => 'Area18',
    ]);

    // Old kill (outside window) should be ignored
    Kill::create([
        'destroyed_at' => Carbon::now()->subDays(30),
        'ship_id' => null,
        'weapon_id' => $weapon2->id,
        'victim_id' => $victim2->id,
        'killer_id' => $killer2->id,
        'type' => Kill::TYPE_FPS,
        'location' => 'Lorville',
    ]);

    $svc = new LeaderboardService('leaderboards', 5, 7);

    $boards = $svc->getLeaderboards();

    expect($boards)->toHaveKeys([
        'top_vehicle_killers',
        'top_fps_killers',
        'top_orgs',
        'top_vehicle_victims',
        'top_fps_victims',
        'top_weapons',
        'top_victim_orgs',
    ]);

    // Top FPS killers: killer1 has 1, killer2 has 1 (tie allowed)
    expect($boards['top_fps_killers']->pluck('killer_id')->all())
        ->toContain($killer1->id, $killer2->id);

    // Top vehicle killers: killer1 has 1
    expect($boards['top_vehicle_killers']->pluck('killer_id')->all())
        ->toContain($killer1->id);

    // Top weapons: weapon1 used twice within window
    $weaponCounts = $boards['top_weapons']->keyBy('weapon_id')->map->weapon_kill_count;
    expect($weaponCounts[$weapon1->id])->toBe(2);

    // Top orgs and victim orgs should include ALPHA and BETA
    $orgSpectrums = $boards['top_orgs']->pluck('spectrum_id')->all();
    expect($orgSpectrums)->toContain('ALPHA', 'BETA');

    $victimOrgSpectrums = $boards['top_victim_orgs']->pluck('spectrum_id')->all();
    expect($victimOrgSpectrums)->toContain('ALPHA', 'BETA');

    // Now add another recent FPS kill for killer1 and verify cache hides it until refresh
    Kill::create([
        'destroyed_at' => Carbon::now()->subHours(2),
        'ship_id' => null,
        'weapon_id' => $weapon2->id,
        'victim_id' => $victim1->id,
        'killer_id' => $killer1->id,
        'type' => Kill::TYPE_FPS,
        'location' => 'New Babbage',
    ]);

    $boardsCached = $svc->getLeaderboards();
    $fpsCountsCached = $boardsCached['top_fps_killers']->keyBy('killer_id')->map->kill_count;
    // Cache should still show old count (1) because KillObserver no longer refreshes on create
    // This is a performance optimization to avoid N refreshes during batch operations
    expect(($fpsCountsCached[$killer1->id] ?? 0))->toBe(1);

    // After manual refresh, the new count (2) should be visible
    $boardsRefreshed = $svc->refreshLeaderboards();
    $fpsCounts = $boardsRefreshed['top_fps_killers']->keyBy('killer_id')->map->kill_count;
    expect($fpsCounts[$killer1->id])->toBe(2);
});
