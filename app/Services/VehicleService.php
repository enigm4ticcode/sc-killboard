<?php

namespace App\Services;

use App\Models\Ship;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
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
        Log::debug('[VEHICLE SERVICE] Looking up vehicle', ['className' => $className, 'slug' => Str::slug($className)]);

        $result = $this->getAllVehicles()->firstWhere('slug', Str::slug($className));

        if ($result) {
            Log::debug('[VEHICLE SERVICE] Found vehicle in cache/database', ['ship' => $result->name]);

            return $result;
        }

        // Fall back and search the wiki instead
        $query = Str::replace('_', ' ', $className);
        Log::debug('[VEHICLE SERVICE] Searching wiki', ['query' => $query]);

        $searchResults = $this->starCitizenWikiService->search($query);
        $data = $searchResults['data'] ?? [];

        Log::debug('[VEHICLE SERVICE] Wiki search results', ['resultCount' => count($data)]);

        if (! empty($data)) {
            $firstResult = reset($data);
            Log::debug('[VEHICLE SERVICE] Using first result', ['name' => $firstResult['name'] ?? 'N/A', 'uuid' => $firstResult['uuid'] ?? 'N/A']);

            $vehicleWikiData = $this->starCitizenWikiService->getVehicleById($firstResult['uuid'])['data'] ?? [];

            if (! empty($vehicleWikiData)) {
                Log::debug('[VEHICLE SERVICE] Got vehicle data from wiki', ['name' => $vehicleWikiData['name'] ?? 'N/A']);

                return $this->updateOrCreateVehicle($vehicleWikiData, $className);
            } else {
                Log::warning('[VEHICLE SERVICE] Empty vehicle data from wiki', ['uuid' => $firstResult['uuid'] ?? 'N/A']);
            }
        } else {
            Log::warning('[VEHICLE SERVICE] No wiki search results', ['query' => $query]);
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
