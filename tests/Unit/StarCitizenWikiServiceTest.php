<?php

use App\Services\StarCitizenWikiService;
use Illuminate\Support\Facades\Http;

it('returns vehicles json on success', function (): void {
    Http::fake([
        'https://wiki.star-citizen.com/api/vehicles*' => Http::response([
            'data' => [['id' => 1, 'name' => 'Constellation']]], 200),
    ]);

    $service = new StarCitizenWikiService([
        'base_url' => 'https://wiki.star-citizen.com/api',
        'per_page' => 10,
    ]);

    $res = $service->getVehicles(1);

    expect($res)->toHaveKey('data')
        ->and($res['data'])->toBeArray()->not->toBeEmpty();
});

it('returns empty array when vehicles call throws', function (): void {
    Http::fake([
        'https://wiki.star-citizen.com/api/vehicles*' => function () {
            throw new Exception('boom');
        },
    ]);

    $service = new StarCitizenWikiService([
        'base_url' => 'https://wiki.star-citizen.com/api',
        'per_page' => 10,
    ]);

    $res = $service->getVehicles(1);

    expect($res)->toBeArray()->toBeEmpty();
});

it('returns vehicle json by id on success', function (): void {
    Http::fake([
        'https://wiki.star-citizen.com/api/vehicles/abc-123' => Http::response([
            'data' => ['uuid' => 'abc-123', 'name' => 'Cutlass Black']], 200),
    ]);

    $service = new StarCitizenWikiService([
        'base_url' => 'https://wiki.star-citizen.com/api',
        'per_page' => 10,
    ]);

    $res = $service->getVehicleById('abc-123');

    expect($res)->toHaveKey('data')
        ->and($res['data']['uuid'])->toBe('abc-123');
});

it('performs search and returns json on success', function (): void {
    Http::fake([
        'https://wiki.star-citizen.com/api/vehicles/search' => Http::response([
            'data' => [['uuid' => 'veh-1'], ['uuid' => 'veh-2']]], 200),
    ]);

    $service = new StarCitizenWikiService([
        'base_url' => 'https://wiki.star-citizen.com/api',
        'per_page' => 10,
    ]);

    $res = $service->search('constellation');

    expect($res)->toHaveKey('data')
        ->and($res['data'])->toHaveCount(2);
});
