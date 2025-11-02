<?php

use App\Models\Ship;
use App\Models\User;
use App\Services\GameLogService;
use App\Services\VehicleService;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;

use function Pest\Laravel\postJson;

it('rejects requests without an API key', function (): void {
    $payload = [
        'username' => 'someone',
        'timestamp' => '2025-10-31T12:00:00.000000Z',
        'kill_type' => 'fps',
        'location' => 'Stanton',
        'killer' => 'Alpha',
        'victim' => 'Beta',
        'weapon' => 'p4-ar',
    ];

    postJson('/api/v1/kills', $payload)->assertUnauthorized();
});

it('rejects requests with an invalid API key', function (): void {
    $user = User::factory()->create(['username' => 'john']);
    $user->api_key = Str::random(64);
    $user->save();

    $payload = [
        'username' => 'john',
        'timestamp' => '2025-10-31T12:00:00.000000Z',
        'kill_type' => 'fps',
        'location' => 'Stanton',
        'killer' => 'Alpha',
        'victim' => 'Beta',
        'weapon' => 'p4-ar',
    ];

    postJson('/api/v1/kills', $payload, [
        'Authorization' => 'Bearer wrong-'.$user->api_key,
    ])->assertUnauthorized();
});

it('validates incoming payload and returns 422 on invalid data', function (): void {
    $user = User::factory()->create(['username' => 'jane']);
    $user->api_key = Str::random(64);
    $user->save();

    // missing required fields
    postJson('/api/v1/kills', ['username' => 'jane'], [
        'Authorization' => 'Bearer '.$user->api_key,
    ])->assertUnprocessable();
});

it('returns 404 when vehicle is not found for vehicle kill type', function (): void {
    $user = User::factory()->create(['username' => 'rick']);
    $user->api_key = Str::random(64);
    $user->save();

    // Mock VehicleService to return null
    $this->mock(VehicleService::class, function ($mock): void {
        $mock->shouldReceive('getVehicleByClass')->once()->andReturn(null);
    });

    // We still mock GameLogService so we do not touch external calls
    $this->mock(GameLogService::class, function ($mock): void {
        $mock->shouldNotReceive('recordKill');
    });

    $payload = [
        'username' => 'rick',
        'timestamp' => '2025-10-31T12:00:00.000000Z',
        'kill_type' => 'vehicle',
        'location' => 'Stanton',
        'killer' => 'Alpha',
        'victim' => 'Beta',
        'weapon' => 'size9-cannon',
        'vehicle' => 'hornet_f7c',
    ];

    postJson('/api/v1/kills', $payload, [
        'Authorization' => 'Bearer '.$user->api_key,
    ])->assertNotFound()->assertJson(fn (AssertableJson $json) => $json->where('message', 'Vehicle not found.'));
});

it('creates an FPS kill successfully (201)', function (): void {
    $user = User::factory()->create(['username' => 'neo']);
    $user->api_key = Str::random(64);
    $user->save();

    // Mock the GameLogService to avoid hitting external services or DB complexity
    $this->mock(GameLogService::class, function ($mock): void {
        $mock->shouldReceive('recordKill')->once()->andReturn(new \App\Models\Kill([
            'id' => 123,
            'type' => 'fps',
            'location' => 'Area18',
        ]));
    });

    // VehicleService should not be called for FPS
    $this->mock(VehicleService::class, function ($mock): void {
        $mock->shouldNotReceive('getVehicleByClass');
    });

    $payload = [
        'username' => 'neo',
        'timestamp' => '2025-10-31T12:00:00.000000Z',
        'kill_type' => 'fps',
        'location' => 'Area18',
        'killer' => 'Alpha',
        'victim' => 'Beta',
        'weapon' => 'p4-ar',
    ];

    postJson('/api/v1/kills', $payload, [
        'Authorization' => 'Bearer '.$user->api_key,
    ])->assertCreated()->assertJson(fn (AssertableJson $json) => $json
        ->where('success', true)
        ->where('data.id', 123)
        ->where('data.type', 'fps')
        ->etc());
});

it('creates a vehicle kill successfully (201)', function (): void {
    $user = User::factory()->create(['username' => 'trinity']);
    $user->api_key = Str::random(64);
    $user->save();

    $ship = new Ship(['id' => 777, 'name' => 'Hornet']);

    $this->mock(VehicleService::class, function ($mock) use ($ship): void {
        $mock->shouldReceive('getVehicleByClass')->once()->andReturn($ship);
    });

    $this->mock(GameLogService::class, function ($mock): void {
        $mock->shouldReceive('recordKill')->once()->andReturn(new \App\Models\Kill([
            'id' => 456,
            'type' => 'vehicle',
            'location' => 'Yela',
        ]));
    });

    $payload = [
        'username' => 'trinity',
        'timestamp' => '2025-10-31T12:00:00.000000Z',
        'kill_type' => 'vehicle',
        'location' => 'Yela',
        'killer' => 'Alpha',
        'victim' => 'Beta',
        'weapon' => 'gattling',
        'vehicle' => 'hornet_f7c',
    ];

    postJson('/api/v1/kills', $payload, [
        'Authorization' => 'Bearer '.$user->api_key,
    ])->assertCreated()->assertJson(fn (AssertableJson $json) => $json
        ->where('success', true)
        ->where('data.id', 456)
        ->where('data.type', 'vehicle')
        ->etc());
});

it('throttles requests to 20 per minute (429 on 21st)', function (): void {
    $user = User::factory()->create(['username' => 'taylor']);
    $user->api_key = Str::random(64);
    $user->save();

    $this->mock(VehicleService::class, function ($mock): void {
        $mock->shouldNotReceive('getVehicleByClass');
    });

    $this->mock(GameLogService::class, function ($mock): void {
        $mock->shouldReceive('recordKill')->andReturn(new \App\Models\Kill(['id' => 1, 'type' => 'fps', 'location' => 'Orison']));
    });

    $payload = [
        'username' => 'taylor',
        'timestamp' => '2025-10-31T12:00:00.000000Z',
        'kill_type' => 'fps',
        'location' => 'Orison',
        'killer' => 'Alpha',
        'victim' => 'Beta',
        'weapon' => 'p8-sc',
    ];

    $headers = ['Authorization' => 'Bearer '.$user->api_key];

    // 20 successful requests
    for ($i = 0; $i < 20; $i++) {
        postJson('/api/v1/kills', $payload, $headers)->assertStatus(Response::HTTP_CREATED);
    }

    // 21st should be throttled
    postJson('/api/v1/kills', $payload, $headers)->assertTooManyRequests();
});
