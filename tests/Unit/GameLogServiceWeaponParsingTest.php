<?php

use App\Models\Kill;
use App\Services\GameLogService;
use App\Services\VehicleService;

if (! function_exists('makeGameLogService')) {
    function makeGameLogService(): GameLogService
    {
        $config = config('gamelog');

        return new GameLogService(new class extends VehicleService
        {
            public function __construct() {}
        }, $config);
    }
}

describe('Weapon Parsing - FPS Weapons', function () {
    it('parses basic FPS weapons correctly', function (string $input, string $expectedSlug, string $expectedName, string $expectedManufacturer): void {
        $service = makeGameLogService();
        $result = $service->parseWeaponName($input, Kill::TYPE_FPS);

        expect($result['slug'])->toBe($expectedSlug)
            ->and($result['name'])->toBe($expectedName)
            ->and($result['manufacturer_code'])->toBe($expectedManufacturer);
    })->with([
        'KSAR Energy Rifle with colors' => ['KSAR_Rifle_Energy_Red01_Pink02_123456', 'energy-rifle', 'Energy Rifle', 'KSAR'],
        'Behr Ballistic LMG' => ['behr_lmg_ballistic_01_1228672956976', 'ballistic-lmg', 'Ballistic Lmg', 'behr'],
        'None Ballistic LMG' => ['None_Lmg_Ballistic_01_123456', 'ballistic-lmg', 'Ballistic Lmg', 'None'],
        'Behr Ballistic Rifle Civilian' => ['Behr_Rifle_Ballistic_02_Civilian_456789', 'ballistic-rifle', 'Ballistic Rifle', 'Behr'],
        'GMNI Ballistic Pistol' => ['gmni_pistol_ballistic_01', 'ballistic-pistol', 'Ballistic Pistol', 'gmni'],
        'KLWE Energy LMG' => ['klwe_lmg_energy_01', 'energy-lmg', 'Energy Lmg', 'klwe'],
        'Behr Ballistic SMG Gold' => ['behr_smg_ballistic_01_gold01_123456', 'ballistic-smg', 'Ballistic Smg', 'behr'],
        'KLWE Energy Pistol Purple' => ['klwe_pistol_energy_01_purple_blue01_789', 'energy-pistol', 'Energy Pistol', 'klwe'],
        'Behr Ballistic Shotgun' => ['behr_shotgun_ballistic_01_456', 'ballistic-shotgun', 'Ballistic Shotgun', 'behr'],
        'GMNI Ballistic Sniper Arctic' => ['gmni_sniper_ballistic_01_arctic01_123', 'ballistic-sniper', 'Ballistic Sniper', 'gmni'],
    ]);
});

describe('Weapon Parsing - Vehicle Weapons', function () {
    it('parses vehicle weapons correctly', function (string $input, string $expectedSlug, string $expectedName, string $expectedManufacturer): void {
        $service = makeGameLogService();
        $result = $service->parseWeaponName($input, Kill::TYPE_VEHICLE);

        expect($result['slug'])->toBe($expectedSlug)
            ->and($result['name'])->toBe($expectedName)
            ->and($result['manufacturer_code'])->toBe($expectedManufacturer);
    })->with([
        'KLWE Laser Repeater S3' => ['KLWE_LaserRepeater_S3_284738394829823', 'laserrepeater-s3', 'Laserrepeater S3', 'KLWE'],
        'AMRS Laser Cannon S3' => ['AMRS_LaserCannon_S3_210730500921', 'lasercannon-s3', 'Lasercannon S3', 'AMRS'],
        'BEHR Ballistic Repeater S3' => ['BEHR_BallisticRepeater_S3_123456', 'ballisticrepeater-s3', 'Ballisticrepeater S3', 'BEHR'],
        'KLWE Laser Repeater S4' => ['KLWE_LaserRepeater_S4_456789', 'laserrepeater-s4', 'Laserrepeater S4', 'KLWE'],
        'BEHR Laser Cannon S5' => ['BEHR_LaserCannon_S5_789', 'lasercannon-s5', 'Lasercannon S5', 'BEHR'],
        'HRST Laser Beam Bespoke' => ['HRST_LaserBeam_Bespoke', 'laserbeam-bespoke', 'Laserbeam Bespoke', 'HRST'],
    ]);
});

describe('Weapon Parsing - Edge Cases', function () {
    it('handles weapons without underscores', function (string $input, string $killType, string $expectedSlug, string $expectedManufacturer): void {
        $service = makeGameLogService();
        $result = $service->parseWeaponName($input, $killType);

        expect($result['slug'])->toBe($expectedSlug)
            ->and($result['manufacturer_code'])->toBe($expectedManufacturer);
    })->with([
        'Simple FPS weapon' => ['SimpleWeapon', Kill::TYPE_FPS, 'simpleweapon', 'NONE'],
        'Simple vehicle weapon' => ['AnotherGun', Kill::TYPE_VEHICLE, 'anothergun', 'NONE'],
    ]);

    it('handles weapons with unknown type/pattern', function (string $input, string $killType, string $expectedSlug, string $expectedManufacturer): void {
        $service = makeGameLogService();
        $result = $service->parseWeaponName($input, $killType);

        expect($result['slug'])->toBe($expectedSlug)
            ->and($result['manufacturer_code'])->toBe($expectedManufacturer);
    })->with([
        'Unknown FPS weapon' => ['Unknown_Weapon_Type_123', Kill::TYPE_FPS, 'weapon-type', 'Unknown'],
        'Strange vehicle weapon' => ['Strange_Name_456', Kill::TYPE_VEHICLE, 'name', 'Strange'],
    ]);
});
