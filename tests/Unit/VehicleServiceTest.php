<?php

use App\Models\Ship;
use App\Services\StarCitizenWikiService;
use App\Services\VehicleService;
use Illuminate\Support\Facades\Cache;

it('returns vehicle from cache/database when slug matches', function (): void {
    Cache::forget('all-vehicles');

    $ship = Ship::create([
        'slug' => 'drake-cutlass-black',
        'name' => 'Cutlass Black',
        'class_name' => 'Drake Cutlass Black',
        'version' => '1.0',
    ]);

    $wiki = new class() extends StarCitizenWikiService {
        public function __construct() {}
        public function search(string $query): array { return ['data' => []]; }
        public function getVehicleById(string $uuid): array { return ['data' => []]; }
    };

    $service = new VehicleService($wiki, 'all-vehicles');

    $found = $service->getVehicleByClass('Drake Cutlass Black');

    expect($found)->not->toBeNull()
        ->and($found->id)->toBe($ship->id);
});

it('falls back to wiki search and creates vehicle when not in cache', function (): void {
    Cache::forget('all-vehicles');

    $wiki = new class() extends StarCitizenWikiService {
        public function __construct() {}
        public function search(string $query): array { return ['data' => [['uuid' => 'veh-123']]]; }
        public function getVehicleById(string $uuid): array {
            return [
                'data' => [
                    'uuid' => $uuid,
                    'name' => 'Argo MOLE',
                    'description' => ['en_EN' => 'Mining vessel'],
                    'version' => '1.2.3',
                ],
            ];
        }
    };

    $service = new VehicleService($wiki, 'all-vehicles');

    $found = $service->getVehicleByClass('ARGO MOLE');

    expect($found)->not->toBeNull()
        ->and($found->slug)->toBe('argo-mole')
        ->and(Ship::query()->count())->toBe(1);
});

it('updateOrCreateVehicle maps fields correctly', function (): void {
    $wiki = new class() extends StarCitizenWikiService { public function __construct() {} };

    $service = new VehicleService($wiki, 'all-vehicles');

    $ship = $service->updateOrCreateVehicle([
        'name' => 'Origin 100i',
        'version' => '2.0',
        'description' => ['en_EN' => 'Touring starter'],
        'class_name' => 'Origin 100i',
    ]);

    expect($ship)->not->toBeNull()
        ->and($ship->name)->toBe('Origin 100i')
        ->and($ship->class_name)->toBe('Origin 100i')
        ->and($ship->description)->toBe('Touring starter')
        ->and($ship->version)->toBe('2.0');
});
