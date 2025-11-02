<?php

use App\Services\RsiStatusService;
use Illuminate\Support\Facades\Http;

it('maps RSI status systems to slugs with statuses', function (): void {
    Http::fake([
        'https://status.robertsspaceindustries.com/index.json' => Http::response([
            'systems' => [
                ['name' => 'Arena Commander', 'status' => 'operational'],
                ['name' => 'Persistent Universe', 'status' => 'degraded_performance'],
                ['name' => 'Platform', 'status' => 'partial_outage'],
            ],
        ], 200),
    ]);

    $service = new RsiStatusService(
        'https://status.robertsspaceindustries.com',
        'index.json',
        'rsi_status',
        300,
    );

    $res = $service->getRsiStatus();

    expect($res)->toMatchArray([
        'arena_commander' => 'operational',
        'persistent_universe' => 'degraded_performance',
        'platform' => 'partial_outage',
    ]);
});

it('handles unknown or missing systems gracefully', function (): void {
    Http::fake([
        'https://status.robertsspaceindustries.com/index.json' => Http::response([
            'systems' => [
                ['name' => 'Foo', 'status' => 'bar'],
            ],
        ], 200),
    ]);

    $service = new RsiStatusService(
        'https://status.robertsspaceindustries.com',
        'index.json',
        'rsi_status',
        300,
    );

    $res = $service->getRsiStatus();

    expect($res['arena_commander'])->toBe('unknown')
        ->and($res['persistent_universe'])->toBe('unknown')
        ->and($res['platform'])->toBe('unknown');
});

it('returns unknowns when HTTP throws', function (): void {
    Http::fake([
        'https://status.robertsspaceindustries.com/index.json' => function () {
            throw new Exception('net');
        },
    ]);

    $service = new RsiStatusService(
        'https://status.robertsspaceindustries.com',
        'index.json',
        'rsi_status',
        300,
    );

    $res = $service->getRsiStatus();

    expect($res['arena_commander'])->toBe('unknown')
        ->and($res['persistent_universe'])->toBe('unknown')
        ->and($res['platform'])->toBe('unknown');
});
