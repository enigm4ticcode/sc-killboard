<?php

namespace App\Services;

use App\Models\Manufacturer;
use Illuminate\Support\Str;

class ManufacturerService
{
    public function __construct(
        protected StarCitizenWikiService $starCitizenWikiService,
        protected array $config
    ) {}

    public function updateOrCreateManufacturer(array $wikiManufacturerData): ?Manufacturer
    {
        $code = $wikiManufacturerData['code'] ?? null;
        $name = $wikiManufacturerData['name'] ?? null;

        // Skip if code or name is empty
        if (empty($code) || empty($name)) {
            return null;
        }

        // Skip placeholder entries
        if ($name === '<= PLACEHOLDER =>') {
            return null;
        }

        // Normalize the code to uppercase for vehicle weapons, lowercase for FPS weapons
        $normalizedCode = $this->normalizeManufacturerCode($code);

        return Manufacturer::query()->updateOrCreate([
            'code' => $normalizedCode,
        ], [
            'name' => $name,
        ]);
    }

    /**
     * Normalize manufacturer code based on pattern.
     * Keep lowercase for FPS weapons (gmni, hdgw, ksar, lbco, volt)
     * Convert to uppercase for vehicle weapons
     */
    private function normalizeManufacturerCode(string $code): string
    {
        $fpsManufacturers = $this->config['manufacturers']['fps_manufacturers'] ?? [];
        $lowerCode = Str::lower($code);

        // If it's a known FPS manufacturer, keep it lowercase
        if (in_array($lowerCode, $fpsManufacturers)) {
            return $lowerCode;
        }

        // If the code is already lowercase and has 4 characters, it might be an FPS manufacturer
        if (strlen($code) === 4 && ctype_lower($code)) {
            return $code;
        }

        // Otherwise, uppercase for vehicle weapons
        return Str::upper($code);
    }
}
