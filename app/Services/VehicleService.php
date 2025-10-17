<?php

namespace App\Services;

use App\Models\Ship;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class VehicleService
{
    protected StarCitizenWikiService $starCitizenWikiService;
    protected string $vehiclesCacheKey;

    public function __construct(StarCitizenWikiService $starCitizenWikiService, string $vehiclesCacheKey)
    {
        $this->starCitizenWikiService = $starCitizenWikiService;
        $this->vehiclesCacheKey = $vehiclesCacheKey;
    }

    public function getVehicleByClass(string $className): ?Ship
    {
        $result = $this->getAllVehicles()->firstWhere('slug', Str::slug($className));

        if ($result) {
            return $result;
        }

        // Fall back and search the wiki instead
        $query = Str::replace('_', ' ', $className);
        $searchResults = $this->starCitizenWikiService->search($query);
        $data = $searchResults['data'] ?? [];

        if (! empty($data)) {
            $firstResult = reset($data);
            $vehicleWikiData = $this->starCitizenWikiService->getVehicleById($firstResult['uuid'])['data'] ?? [];

            if (! empty($vehicleWikiData)) {
                return $this->updateOrCreateVehicle($vehicleWikiData, $className);
            }
        }

        return null;
    }

    public function getAllVehicles(): Collection
    {
        return Cache::rememberForever($this->vehiclesCacheKey, function () {
            return Ship::all();
        });
    }

    public function updateOrCreateVehicle(array $wikiVehicleData, ?string $className = null): ?Ship
    {
        $className = $className ?: $wikiVehicleData['class_name'] ?? null;

        if ($className !== null) {
            return Ship::query()->updateOrCreate([
                'slug' => Str::slug($className),
            ], [
                'class_name' => $className,
                'name' => $wikiVehicleData['name'],
                'description' => $wikiVehicleData['description']['en_EN'] ?? null,
                'version' => $wikiVehicleData['version'],
            ]);
        }

        return null;
    }
}
