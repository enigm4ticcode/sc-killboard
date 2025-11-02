<?php

use App\Models\Kill;
use App\Models\LogUpload;
use App\Models\Organization;
use App\Models\Ship;
use App\Models\User;
use App\Services\GameLogService;
use App\Services\VehicleService;
use Illuminate\Support\Facades\Http;

class DummyVehicleService extends VehicleService
{
    public function __construct() {}
}

function makeGameLogService(): GameLogService
{
    $vehicleService = new DummyVehicleService;

    $config = [
        'actor_kill_string' => 'Actor Death',
        'vehicle_destruction_string' => 'Vehicle Destruction',
        'valid_pvp_damage_types' => ['VehicleDestruction', 'Bullet'],
        'iso_timestamp_pattern' => '/\<(\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d+)?(?:Z|[+-]\d{2}:\d{2}))\>/',
        'lines_to_read' => 4,
    ];

    return new GameLogService($vehicleService, $config);
}

it('recordKill creates FPS kill and associates user and log upload', function (): void {
    // Fake RSI endpoints to avoid network and produce defaults (no avatar, NONE org)
    Http::fake([
        'https://robertsspaceindustries.com/citizens/*' => Http::response('<html></html>', 200),
        'https://robertsspaceindustries.com/en/citizens/*/organizations' => Http::response('<html></html>', 200),
    ]);

    $service = makeGameLogService();

    $user = User::factory()->create();
    $log = LogUpload::create(['filename' => 'log.txt', 'path' => '/tmp/log.txt', 'user_id' => $user->id]);

    $kill = $service->recordKill(
        now()->toIso8601String(),
        Kill::TYPE_FPS,
        'Orison',
        'Killer A',
        'Victim B',
        'p4-ar',
        null,
        111,
        222,
        $user,
        $log,
    );

    expect($kill)->not->toBeNull()
        ->and($kill->type)->toBe(Kill::TYPE_FPS)
        ->and($kill->location)->toBe('Orison')
        ->and($kill->ship_id)->toBeNull()
        ->and($kill->weapon)->not->toBeNull()
        ->and($kill->victim->name)->toBe('Victim B')
        ->and($kill->killer->name)->toBe('Killer A')
        ->and($kill->user?->id)->toBe($user->id)
        ->and($kill->logUpload?->id)->toBe($log->id);

    // Players should have an organization, likely the default NONE created by org parser defaults
    $orgNames = Organization::query()->pluck('name')->all();
    expect($orgNames)->toContain(Organization::ORG_NONE);
});

it('recordKill creates VEHICLE kill and sets ship id', function (): void {
    Http::fake([
        'https://robertsspaceindustries.com/citizens/*' => Http::response('<html></html>', 200),
        'https://robertsspaceindustries.com/en/citizens/*/organizations' => Http::response('<html></html>', 200),
    ]);

    $service = makeGameLogService();

    $ship = Ship::create([
        'slug' => 'gladius',
        'name' => 'Gladius',
        'class_name' => 'AEGIS Gladius',
        'version' => '1.0',
    ]);

    $kill = $service->recordKill(
        now()->toIso8601String(),
        Kill::TYPE_VEHICLE,
        'Yela',
        'Pilot One',
        'Pilot Two',
        'size3-gun',
        $ship,
        333,
        444,
        null,
        null,
    );

    expect($kill)->not->toBeNull()
        ->and($kill->type)->toBe(Kill::TYPE_VEHICLE)
        ->and($kill->ship_id)->toBe($ship->id);
});
