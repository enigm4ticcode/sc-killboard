<?php

use App\Models\Kill;
use App\Models\LogUpload;
use App\Models\Player;
use App\Models\User;
use App\Services\GameLogService;
use App\Services\VehicleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('local');

    // Create test data
    $this->user = User::factory()->create();
    $this->logUpload = LogUpload::create([
        'user_id' => $this->user->id,
        'filename' => 'test.log',
        'path' => 'test.log',
    ]);

    $this->killer = Player::create(['name' => 'TestKiller', 'game_id' => '67890']);
    $this->victim = Player::create(['name' => 'TestVictim', 'game_id' => '12345']);
});

it('prevents duplicate kills with exact same timestamp in batch', function () {
    // Create a log file with duplicate kill entries (exact same timestamp)
    $timestamp = now()->format('Y-m-d H:i:s');
    $logContent = <<<LOG
<{$timestamp}> CActor::Kill: 'TestVictim' [12345] in zone 'TestZone' killed by 'TestKiller' [67890] using 'KSAR_Rifle_Energy_01' [Class TestClass] with damage type 'Bullet'
<{$timestamp}> CActor::Kill: 'TestVictim' [12345] in zone 'TestZone' killed by 'TestKiller' [67890] using 'KSAR_Rifle_Energy_01' [Class TestClass] with damage type 'Bullet'
<{$timestamp}> CActor::Kill: 'TestVictim' [12345] in zone 'TestZone' killed by 'TestKiller' [67890] using 'KSAR_Rifle_Energy_01' [Class TestClass] with damage type 'Bullet'
LOG;

    $filePath = 'test_log.log';
    Storage::put($filePath, $logContent);

    $config = config('gamelog');
    $vehicleService = app(VehicleService::class);
    $gameLogService = new GameLogService($vehicleService, $config);

    // Process the log
    $result = $gameLogService->processGameLog($filePath, $this->logUpload);

    // Should only create 1 kill, not 3
    expect(Kill::count())->toBe(1)
        ->and($result['total_kills'])->toBe(1);
});

it('prevents duplicate kills within time tolerance window', function () {
    // Create kills with timestamps within 3 seconds of each other
    $baseTime = now();
    $logContent = <<<LOG
<{$baseTime->format('Y-m-d H:i:s')}> CActor::Kill: 'TestVictim' [12345] in zone 'TestZone' killed by 'TestKiller' [67890] using 'KSAR_Rifle_Energy_01' [Class TestClass] with damage type 'Bullet'
<{$baseTime->addSeconds(1)->format('Y-m-d H:i:s')}> CActor::Kill: 'TestVictim' [12345] in zone 'TestZone' killed by 'TestKiller' [67890] using 'KSAR_Rifle_Energy_01' [Class TestClass] with damage type 'Bullet'
<{$baseTime->addSeconds(1)->format('Y-m-d H:i:s')}> CActor::Kill: 'TestVictim' [12345] in zone 'TestZone' killed by 'TestKiller' [67890] using 'KSAR_Rifle_Energy_01' [Class TestClass] with damage type 'Bullet'
LOG;

    $filePath = 'test_log_time_tolerance.log';
    Storage::put($filePath, $logContent);

    $config = config('gamelog');
    $vehicleService = app(VehicleService::class);
    $gameLogService = new GameLogService($vehicleService, $config);

    // Process the log
    $result = $gameLogService->processGameLog($filePath, $this->logUpload);

    // Should only create 1 kill due to time tolerance (±3 seconds)
    expect(Kill::count())->toBe(1)
        ->and($result['total_kills'])->toBe(1);
});

it('allows kills with same players but different timestamps outside tolerance', function () {
    $baseTime = now();
    $logContent = <<<LOG
<{$baseTime->format('Y-m-d H:i:s')}> CActor::Kill: 'TestVictim' [12345] in zone 'TestZone' killed by 'TestKiller' [67890] using 'KSAR_Rifle_Energy_01' [Class TestClass] with damage type 'Bullet'
<{$baseTime->addSeconds(10)->format('Y-m-d H:i:s')}> CActor::Kill: 'TestVictim' [12345] in zone 'TestZone' killed by 'TestKiller' [67890] using 'KSAR_Rifle_Energy_01' [Class TestClass] with damage type 'Bullet'
LOG;

    $filePath = 'test_log_different_times.log';
    Storage::put($filePath, $logContent);

    $config = config('gamelog');
    $vehicleService = app(VehicleService::class);
    $gameLogService = new GameLogService($vehicleService, $config);

    // Process the log
    $result = $gameLogService->processGameLog($filePath, $this->logUpload);

    // Should create 2 kills because they're >3 seconds apart
    expect(Kill::count())->toBe(2)
        ->and($result['total_kills'])->toBe(2);
});

it('allows kills with different victims at same timestamp', function () {
    $timestamp = now()->format('Y-m-d H:i:s');

    // Create second victim
    $victim2 = Player::create(['name' => 'TestVictim2', 'game_id' => '54321']);

    $logContent = <<<LOG
<{$timestamp}> CActor::Kill: 'TestVictim' [12345] in zone 'TestZone' killed by 'TestKiller' [67890] using 'KSAR_Rifle_Energy_01' [Class TestClass] with damage type 'Bullet'
<{$timestamp}> CActor::Kill: 'TestVictim2' [54321] in zone 'TestZone' killed by 'TestKiller' [67890] using 'KSAR_Rifle_Energy_01' [Class TestClass] with damage type 'Bullet'
LOG;

    $filePath = 'test_log_different_victims.log';
    Storage::put($filePath, $logContent);

    $config = config('gamelog');
    $vehicleService = app(VehicleService::class);
    $gameLogService = new GameLogService($vehicleService, $config);

    // Process the log
    $result = $gameLogService->processGameLog($filePath, $this->logUpload);

    // Should create 2 kills because victims are different
    expect(Kill::count())->toBe(2)
        ->and($result['total_kills'])->toBe(2);
});

it('prevents duplicate kills with different weapons at same timestamp', function () {
    // This tests the real-world scenario where Star Citizen logs the same kill with different weapons
    $timestamp = now()->format('Y-m-d H:i:s');
    $logContent = <<<LOG
<{$timestamp}> CActor::Kill: 'TestVictim' [12345] in zone 'TestZone' killed by 'TestKiller' [67890] using 'KSAR_Rifle_Energy_01' [Class TestClass] with damage type 'Bullet'
<{$timestamp}> CActor::Kill: 'TestVictim' [12345] in zone 'TestZone' killed by 'TestKiller' [67890] using 'KSAR_Ballistic_Shotgun_01' [Class TestClass] with damage type 'Bullet'
<{$timestamp}> CActor::Kill: 'TestVictim' [12345] in zone 'TestZone' killed by 'TestKiller' [67890] using 'AEGS_Saber_Weapon_01' [Class TestClass] with damage type 'Energy'
LOG;

    $filePath = 'test_log_different_weapons.log';
    Storage::put($filePath, $logContent);

    $config = config('gamelog');
    $vehicleService = app(VehicleService::class);
    $gameLogService = new GameLogService($vehicleService, $config);

    // Process the log
    $result = $gameLogService->processGameLog($filePath, $this->logUpload);

    // Should only create 1 kill despite different weapons being logged
    expect(Kill::count())->toBe(1)
        ->and($result['total_kills'])->toBe(1);
});

it('prevents re-creating kills when processing same log file multiple times', function () {
    // This tests the real-world scenario where the same log is processed twice
    $timestamp = now()->format('Y-m-d H:i:s');
    $logContent = <<<LOG
<{$timestamp}> CActor::Kill: 'TestVictim' [12345] in zone 'TestZone' killed by 'TestKiller' [67890] using 'KSAR_Rifle_Energy_01' [Class TestClass] with damage type 'Bullet'
LOG;

    $filePath = 'test_log_reprocess.log';
    Storage::put($filePath, $logContent);

    $config = config('gamelog');
    $vehicleService = app(VehicleService::class);
    $gameLogService = new GameLogService($vehicleService, $config);

    // Process the log first time
    $result1 = $gameLogService->processGameLog($filePath, $this->logUpload);
    expect(Kill::count())->toBe(1)
        ->and($result1['total_kills'])->toBe(1);

    // Process the same log again
    $result2 = $gameLogService->processGameLog($filePath, $this->logUpload);

    // Should still only have 1 kill (duplicate prevention worked)
    expect(Kill::count())->toBe(1)
        ->and($result2['total_kills'])->toBe(0); // 0 new kills created
});

it('deduplicates within batches correctly', function () {
    // Create a log with 10 entries with several duplicates
    // This tests batch deduplication efficiently
    $timestamp = now()->format('Y-m-d H:i:s');
    $logLines = [];

    // Add 5 unique kills
    for ($i = 1; $i <= 5; $i++) {
        Player::create(['name' => "Victim{$i}", 'game_id' => (string) $i]);
        $logLines[] = "<{$timestamp}> CActor::Kill: 'Victim{$i}' [{$i}] in zone 'TestZone' killed by 'TestKiller' [67890] using 'KSAR_Rifle_Energy_01' [Class TestClass] with damage type 'Bullet'";
    }

    // Add duplicate of first kill
    $logLines[] = "<{$timestamp}> CActor::Kill: 'Victim1' [1] in zone 'TestZone' killed by 'TestKiller' [67890] using 'KSAR_Rifle_Energy_01' [Class TestClass] with damage type 'Bullet'";

    // Add more unique kills
    for ($i = 6; $i <= 10; $i++) {
        Player::create(['name' => "Victim{$i}", 'game_id' => (string) $i]);
        $logLines[] = "<{$timestamp}> CActor::Kill: 'Victim{$i}' [{$i}] in zone 'TestZone' killed by 'TestKiller' [67890] using 'KSAR_Rifle_Energy_01' [Class TestClass] with damage type 'Bullet'";
    }

    // Add duplicate of kill from middle
    $logLines[] = "<{$timestamp}> CActor::Kill: 'Victim5' [5] in zone 'TestZone' killed by 'TestKiller' [67890] using 'KSAR_Rifle_Energy_01' [Class TestClass] with damage type 'Bullet'";

    // Add duplicate of kill from end
    $logLines[] = "<{$timestamp}> CActor::Kill: 'Victim10' [10] in zone 'TestZone' killed by 'TestKiller' [67890] using 'KSAR_Rifle_Energy_01' [Class TestClass] with damage type 'Bullet'";

    $filePath = 'test_log_batch.log';
    Storage::put($filePath, implode("\n", $logLines));

    $config = config('gamelog');
    $vehicleService = app(VehicleService::class);
    $gameLogService = new GameLogService($vehicleService, $config);

    // Process the log
    $result = $gameLogService->processGameLog($filePath, $this->logUpload);

    // Should create exactly 10 kills (3 duplicates removed)
    expect(Kill::count())->toBe(10)
        ->and($result['total_kills'])->toBe(10);
});
