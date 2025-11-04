<?php

use App\Models\Kill;
use App\Models\LogUpload;
use App\Models\Organization;
use App\Models\Ship;
use App\Models\User;
use App\Services\GameLogService;
use App\Services\VehicleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

class DummyVehicleService extends VehicleService
{
    public function __construct() {}
}

function makeGameLogService(): GameLogService
{
    $config = config('gamelog');

    return new GameLogService(new DummyVehicleService, $config);
}

beforeEach(function () {
    // Fake RSI endpoints to avoid network calls and produce defaults (no avatar, NONE org)
    Http::fake([
        'https://robertsspaceindustries.com/citizens/*' => Http::response('<html></html>', 200),
        'https://robertsspaceindustries.com/en/citizens/*/organizations' => Http::response('<html></html>', 200),
    ]);
});

describe('Weapon Name Parsing', function () {
    it('parses FPS ballistic rifle correctly', function (): void {
        $service = makeGameLogService();

        $result = $service->parseWeaponName('behr_rifle_ballistic_01', Kill::TYPE_FPS);

        expect($result)->toHaveKey('slug')
            ->and($result)->toHaveKey('name')
            ->and($result)->toHaveKey('manufacturer_code')
            ->and($result['slug'])->toBe('ballistic-rifle')
            ->and($result['name'])->toBe('Ballistic Rifle')
            ->and($result['manufacturer_code'])->toBe('behr');
    });

    it('parses FPS energy LMG correctly', function (): void {
        $service = makeGameLogService();

        $result = $service->parseWeaponName('volt_lmg_energy_01_tint02', Kill::TYPE_FPS);

        expect($result['slug'])->toBe('energy-lmg')
            ->and($result['name'])->toBe('Energy Lmg')
            ->and($result['manufacturer_code'])->toBe('volt');
    });

    it('parses vehicle laser cannon correctly', function (): void {
        $service = makeGameLogService();

        $result = $service->parseWeaponName('BEHR_LaserCannon_S5_6467392674844', Kill::TYPE_VEHICLE);

        expect($result['slug'])->toBe('lasercannon-s5')
            ->and($result['name'])->toBe('Lasercannon S5')
            ->and($result['manufacturer_code'])->toBe('BEHR');
    });

    it('parses vehicle laser repeater correctly', function (): void {
        $service = makeGameLogService();

        $result = $service->parseWeaponName('KLWE_LaserRepeater_S4_Turret_6467396474844', Kill::TYPE_VEHICLE);

        expect($result['slug'])->toBe('laserrepeater-s4-turret')
            ->and($result['name'])->toBe('Laserrepeater S4 Turret')
            ->and($result['manufacturer_code'])->toBe('KLWE');
    });

    it('parses vehicle neutron repeater correctly', function (): void {
        $service = makeGameLogService();

        $result = $service->parseWeaponName('MXOX_NeutronRepeater_S3', Kill::TYPE_VEHICLE);

        expect($result['slug'])->toBe('neutronrepeater-s3')
            ->and($result['name'])->toBe('Neutronrepeater S3')
            ->and($result['manufacturer_code'])->toBe('MXOX');
    });

    it('handles weapon without manufacturer code', function (): void {
        $service = makeGameLogService();

        $result = $service->parseWeaponName('UnknownWeapon', Kill::TYPE_FPS);

        expect($result['manufacturer_code'])->toBe('NONE');
    });

    it('removes numeric suffixes from FPS weapons', function (): void {
        $service = makeGameLogService();

        $result = $service->parseWeaponName('behr_rifle_ballistic_01_123456', Kill::TYPE_FPS);

        expect($result['slug'])->toBe('ballistic-rifle')
            ->and($result['name'])->toBe('Ballistic Rifle');
    });

    it('removes numeric suffixes from vehicle weapons', function (): void {
        $service = makeGameLogService();

        $result = $service->parseWeaponName('KLWE_LaserRepeater_S3_284738394829823', Kill::TYPE_VEHICLE);

        expect($result['slug'])->toBe('laserrepeater-s3')
            ->and($result['name'])->toBe('Laserrepeater S3');
    });

    it('handles FPS pistol correctly', function (): void {
        $service = makeGameLogService();

        $result = $service->parseWeaponName('behr_pistol_ballistic_01', Kill::TYPE_FPS);

        expect($result['slug'])->toBe('ballistic-pistol')
            ->and($result['name'])->toBe('Ballistic Pistol')
            ->and($result['manufacturer_code'])->toBe('behr');
    });

    it('handles FPS SMG correctly', function (): void {
        $service = makeGameLogService();

        $result = $service->parseWeaponName('gmni_smg_ballistic_01', Kill::TYPE_FPS);

        expect($result['slug'])->toBe('ballistic-smg')
            ->and($result['name'])->toBe('Ballistic Smg')
            ->and($result['manufacturer_code'])->toBe('gmni');
    });

    it('handles FPS sniper correctly', function (): void {
        $service = makeGameLogService();

        $result = $service->parseWeaponName('ksar_sniper_ballistic_01', Kill::TYPE_FPS);

        expect($result['slug'])->toBe('ballistic-sniper')
            ->and($result['name'])->toBe('Ballistic Sniper')
            ->and($result['manufacturer_code'])->toBe('ksar');
    });
});

describe('Kill Recording', function () {
    it('creates FPS kill and associates user and log upload', function (): void {
        $service = makeGameLogService();
        $user = User::factory()->create();
        $log = LogUpload::create(['filename' => 'log.txt', 'path' => '/tmp/log.txt', 'user_id' => $user->id]);

        $kill = $service->recordKill(
            now()->toIso8601String(),
            Kill::TYPE_FPS,
            'Orison',
            'Killer A',
            'Victim B',
            'behr_pistol_ballistic_01_123456',
            null,
            '222',  // victimGameId (Victim B's ID)
            '111',  // killerGameId (Killer A's ID)
            $user,
            $log,
        );

        expect($kill)->not->toBeNull()
            ->and($kill->type)->toBe(Kill::TYPE_FPS)
            ->and($kill->location)->toBe('Orison')
            ->and($kill->ship_id)->toBeNull()
            ->and($kill->weapon)->not->toBeNull()
            ->and($kill->weapon->slug)->toBe('ballistic-pistol')
            ->and($kill->weapon->name)->toBe('Ballistic Pistol')
            ->and($kill->victim->name)->toBe('Victim B')
            ->and($kill->killer->name)->toBe('Killer A')
            ->and($kill->user?->id)->toBe($user->id)
            ->and($kill->logUpload?->id)->toBe($log->id);

        // Players should have an organization (the default NONE created by org parser)
        $orgNames = Organization::query()->pluck('name')->all();
        expect($orgNames)->toContain(Organization::ORG_NONE);
    });

    it('creates VEHICLE kill and sets ship id', function (): void {
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
            'KLWE_LaserRepeater_S3_284738394829823',
            $ship,
            '444',  // victimGameId (Pilot Two's ID)
            '333',  // killerGameId (Pilot One's ID)
            null,
            null,
        );

        expect($kill)->not->toBeNull()
            ->and($kill->type)->toBe(Kill::TYPE_VEHICLE)
            ->and($kill->ship_id)->toBe($ship->id)
            ->and($kill->weapon->slug)->toBe('laserrepeater-s3')
            ->and($kill->weapon->name)->toBe('Laserrepeater S3');
    });

    it('reuses existing weapon records', function (): void {
        $service = makeGameLogService();

        // Create first kill with a weapon
        $kill1 = $service->recordKill(
            now()->toIso8601String(),
            Kill::TYPE_FPS,
            'Orison',
            'Killer A',
            'Victim B',
            'behr_rifle_ballistic_01_123456',
            null,
            '222',  // victimGameId (Victim B's ID)
            '111',  // killerGameId (Killer A's ID)
            null,
            null,
        );

        // Create second kill with same weapon (different variant)
        $kill2 = $service->recordKill(
            now()->toIso8601String(),
            Kill::TYPE_FPS,
            'Orison',
            'Killer C',
            'Victim D',
            'behr_rifle_ballistic_01_blue01_789',
            null,
            '444',  // victimGameId (Victim D's ID)
            '333',  // killerGameId (Killer C's ID)
            null,
            null,
        );

        // Both should use the same weapon record
        expect($kill1->weapon_id)->toBe($kill2->weapon_id)
            ->and($kill1->weapon->slug)->toBe('ballistic-rifle');
    });

    it('creates manufacturer when recording kill with weapon', function (): void {
        $service = makeGameLogService();

        $kill = $service->recordKill(
            now()->toIso8601String(),
            Kill::TYPE_FPS,
            'Orison',
            'Killer A',
            'Victim B',
            'behr_rifle_ballistic_01',
            null,
            '222',  // victimGameId (Victim B's ID)
            '111',  // killerGameId (Killer A's ID)
            null,
            null,
        );

        expect($kill)->not->toBeNull()
            ->and($kill->weapon->manufacturer)->not->toBeNull()
            ->and($kill->weapon->manufacturer->code)->toBe('behr');
    });

    it('creates Unknown manufacturer for unrecognized codes', function (): void {
        $service = makeGameLogService();

        $kill = $service->recordKill(
            now()->toIso8601String(),
            Kill::TYPE_FPS,
            'Orison',
            'Killer A',
            'Victim B',
            'UNKN_rifle_ballistic_01',
            null,
            '222',  // victimGameId (Victim B's ID)
            '111',  // killerGameId (Killer A's ID)
            null,
            null,
        );

        expect($kill)->not->toBeNull()
            ->and($kill->weapon->manufacturer)->not->toBeNull()
            ->and($kill->weapon->manufacturer->code)->toBe('UNKN')
            ->and($kill->weapon->manufacturer->name)->toBe('Unknown');
    });
});

describe('Kill Deduplication with Time Tolerance', function () {
    it('detects duplicate kill within 3 second window (exact same time)', function (): void {
        $service = makeGameLogService();
        $timestamp = now();

        // Create first kill
        $kill1 = $service->recordKill(
            $timestamp->toIso8601String(),
            Kill::TYPE_FPS,
            'Orison',
            'Killer A',
            'Victim B',
            'behr_rifle_ballistic_01',
            null,
            '222',  // victimGameId (Victim B's ID)
            '111',  // killerGameId (Killer A's ID)
            null,
            null,
        );

        // Try to create same kill again
        $kill2 = $service->recordKill(
            $timestamp->toIso8601String(),
            Kill::TYPE_FPS,
            'Orison',
            'Killer A',
            'Victim B',
            'behr_rifle_ballistic_01',
            null,
            '222',  // victimGameId (Victim B's ID)
            '111',  // killerGameId (Killer A's ID)
            null,
            null,
        );

        // Should return same kill
        expect($kill1->id)->toBe($kill2->id)
            ->and(Kill::query()->count())->toBe(1);
    });

    it('detects duplicate kill 2 seconds later (within tolerance)', function (): void {
        $service = makeGameLogService();
        $timestamp = now();

        // Create first kill
        $kill1 = $service->recordKill(
            $timestamp->toIso8601String(),
            Kill::TYPE_FPS,
            'Orison',
            'Killer A',
            'Victim B',
            'behr_rifle_ballistic_01',
            null,
            '222',  // victimGameId (Victim B's ID)
            '111',  // killerGameId (Killer A's ID)
            null,
            null,
        );

        // Try to create same kill 2 seconds later (within 3 second tolerance)
        $kill2 = $service->recordKill(
            $timestamp->copy()->addSeconds(2)->toIso8601String(),
            Kill::TYPE_FPS,
            'Orison',
            'Killer A',
            'Victim B',
            'behr_rifle_ballistic_01',
            null,
            '222',  // victimGameId (Victim B's ID)
            '111',  // killerGameId (Killer A's ID)
            null,
            null,
        );

        // Should return same kill
        expect($kill1->id)->toBe($kill2->id)
            ->and(Kill::query()->count())->toBe(1);
    });

    it('detects duplicate kill 2 seconds earlier (within tolerance)', function (): void {
        $service = makeGameLogService();
        $timestamp = now();

        // Create first kill
        $kill1 = $service->recordKill(
            $timestamp->toIso8601String(),
            Kill::TYPE_FPS,
            'Orison',
            'Killer A',
            'Victim B',
            'behr_rifle_ballistic_01',
            null,
            '222',  // victimGameId (Victim B's ID)
            '111',  // killerGameId (Killer A's ID)
            null,
            null,
        );

        // Try to create same kill 2 seconds earlier (within 3 second tolerance)
        $kill2 = $service->recordKill(
            $timestamp->copy()->subSeconds(2)->toIso8601String(),
            Kill::TYPE_FPS,
            'Orison',
            'Killer A',
            'Victim B',
            'behr_rifle_ballistic_01',
            null,
            '222',  // victimGameId (Victim B's ID)
            '111',  // killerGameId (Killer A's ID)
            null,
            null,
        );

        // Should return same kill
        expect($kill1->id)->toBe($kill2->id)
            ->and(Kill::query()->count())->toBe(1);
    });

    it('creates separate kill when timestamp differs by more than 3 seconds', function (): void {
        $service = makeGameLogService();
        $timestamp = now();

        // Create first kill
        $kill1 = $service->recordKill(
            $timestamp->toIso8601String(),
            Kill::TYPE_FPS,
            'Orison',
            'Killer A',
            'Victim B',
            'behr_rifle_ballistic_01',
            null,
            '222',  // victimGameId (Victim B's ID)
            '111',  // killerGameId (Killer A's ID)
            null,
            null,
        );

        // Try to create "same" kill 4 seconds later (outside 3 second tolerance)
        $kill2 = $service->recordKill(
            $timestamp->copy()->addSeconds(4)->toIso8601String(),
            Kill::TYPE_FPS,
            'Orison',
            'Killer A',
            'Victim B',
            'behr_rifle_ballistic_01',
            null,
            '222',  // victimGameId (Victim B's ID)
            '111',  // killerGameId (Killer A's ID)
            null,
            null,
        );

        // Should create a new kill
        expect($kill1->id)->not->toBe($kill2->id)
            ->and(Kill::query()->count())->toBe(2);
    });

    it('creates separate kill when location differs', function (): void {
        $service = makeGameLogService();
        $timestamp = now();

        // Create first kill in Orison
        $kill1 = $service->recordKill(
            $timestamp->toIso8601String(),
            Kill::TYPE_FPS,
            'Orison',
            'Killer A',
            'Victim B',
            'behr_rifle_ballistic_01',
            null,
            '222',  // victimGameId (Victim B's ID)
            '111',  // killerGameId (Killer A's ID)
            null,
            null,
        );

        // Create kill in different location (same time)
        $kill2 = $service->recordKill(
            $timestamp->toIso8601String(),
            Kill::TYPE_FPS,
            'Area18',
            'Killer A',
            'Victim B',
            'behr_rifle_ballistic_01',
            null,
            '222',  // victimGameId (Victim B's ID)
            '111',  // killerGameId (Killer A's ID)
            null,
            null,
        );

        // Should create separate kills
        expect($kill1->id)->not->toBe($kill2->id)
            ->and(Kill::query()->count())->toBe(2);
    });

    it('creates separate kill when killer differs', function (): void {
        $service = makeGameLogService();
        $timestamp = now();

        // Create first kill
        $kill1 = $service->recordKill(
            $timestamp->toIso8601String(),
            Kill::TYPE_FPS,
            'Orison',
            'Killer A',
            'Victim B',
            'behr_rifle_ballistic_01',
            null,
            '222',  // victimGameId (Victim B's ID)
            '111',  // killerGameId (Killer A's ID)
            null,
            null,
        );

        // Create kill with different killer (same time)
        $kill2 = $service->recordKill(
            $timestamp->toIso8601String(),
            Kill::TYPE_FPS,
            'Orison',
            'Killer C',
            'Victim B',
            'behr_rifle_ballistic_01',
            null,
            '222',  // victimGameId (Victim B's ID)
            '333',  // killerGameId (Killer C's ID)
            null,
            null,
        );

        // Should create separate kills
        expect($kill1->id)->not->toBe($kill2->id)
            ->and(Kill::query()->count())->toBe(2);
    });
});
