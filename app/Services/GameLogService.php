<?php

namespace App\Services;

use App\Models\Kill;
use App\Models\LogUpload;
use App\Models\Organization;
use App\Models\Player;
use App\Models\Ship;
use App\Models\User;
use App\Models\Weapon;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PHPHtmlParser\Dom;

class GameLogService
{
    private const UNKNOWN = 'unknown';

    private const COMBAT = 'Combat';

    private const DAMAGE_TYPE_FPS = 'Bullet';

    protected array $acMatchStrings = [];

    protected string $vehicleDestructionString;

    protected array $validPvpDamageTypes;

    protected VehicleService $vehicleService;

    protected int $linesToRead;

    protected array $fpsWeaponPatterns;

    protected array $fpsWeaponTypes;

    protected int $killTimeToleranceSeconds;

    protected array $npcPatterns;

    protected int $batchSize;

    public function __construct(VehicleService $vehicleService, array $config)
    {
        $this->vehicleService = $vehicleService;
        $this->vehicleDestructionString = $config['vehicle_destruction_string'];
        $this->acMatchStrings = $config['ac_match_strings'];
        $this->validPvpDamageTypes = $config['valid_pvp_damage_types'];
        $this->linesToRead = $config['lines_to_read'];
        $this->fpsWeaponPatterns = $config['weapons']['fps_weapon_patterns'] ?? [];
        $this->fpsWeaponTypes = $config['weapons']['fps_weapon_types'] ?? [];
        $this->killTimeToleranceSeconds = $config['kill_time_tolerance_seconds'] ?? 3;
        $this->npcPatterns = $config['npc_patterns'] ?? [];
        $this->batchSize = $config['batch_size'] ?? 500;
    }

    public function processGameLog(string $filePath, LogUpload $logUpload): array
    {
        $out = [
            'total_kills' => 0,
            'has_arena_commander_kills' => false,
        ];

        $foundEntries = [];
        $totalKills = 0;
        $previousLine = null;

        if (Storage::exists($filePath)) {
            $handle = Storage::readStream($filePath);

            if ($handle) {
                while (($currentLine = fgets($handle)) !== false) {
                    // Pre-filter: Skip lines that don't contain Actor Death pattern
                    if (! Str::contains($currentLine, 'CActor::Kill:')) {
                        // Check for Arena Commander before skipping
                        if (in_array($currentLine, $this->acMatchStrings)) {
                            $out['has_arena_commander_kills'] = true;
                            break;
                        }

                        $previousLine = $currentLine;

                        continue;
                    }

                    $trimmedCurrent = trim($currentLine);

                    // Parse using string operations (2-3x faster than regex)
                    $parsedData = $this->parseActorDeathLine($trimmedCurrent);

                    if ($parsedData === null) {
                        // Log parsing failures for debugging
                        Log::warning('[GAMELOG PARSER] Failed to parse actor death line', [
                            'line' => Str::limit($trimmedCurrent, 200),
                        ]);

                        continue;
                    }

                    // Successfully parsed - process the data
                    $damageType = $parsedData['damageType'];

                    Log::debug('[GAMELOG PARSER] Parsed kill', [
                        'victim' => $parsedData['victim'],
                        'killer' => $parsedData['killer'],
                        'damageType' => $damageType,
                        'weapon' => $parsedData['weapon'],
                    ]);

                    if (Str::contains($damageType, $this->validPvpDamageTypes)) {
                        $timestamp = Carbon::parse($parsedData['timestamp']);
                        $victim = $parsedData['victim'];
                        $victimGameId = $parsedData['victimId'];
                        $killerGameId = $parsedData['killerId'];
                        $killWeapon = $parsedData['weapon'];
                        $killer = $parsedData['killer'];
                        $victimDeathZone = $parsedData['zone'];

                        Log::debug('[GAMELOG PARSER] Valid damage type, checking NPCs', [
                            'victim' => $victim,
                            'killer' => $killer,
                            'isVictimNpc' => $this->isNpc($victim),
                            'isKillerNpc' => $this->isNpc($killer),
                        ]);

                        if ($victim !== $killer && ! $this->isNpc($victim) && ! $this->isNpc($killer)) {
                            $entry = [
                                'timestamp' => $timestamp,
                                'victim' => $victim,
                                'killer' => $killer,
                                'location' => $victimDeathZone,
                                'victimGameId' => $victimGameId,
                                'killerGameId' => $killerGameId,
                                'killWeapon' => $killWeapon,
                                'vehicle' => null,
                                'killType' => $damageType === self::DAMAGE_TYPE_FPS ? Kill::TYPE_FPS : Kill::TYPE_VEHICLE,
                            ];

                            // Check if there's a vehicle destruction event in the previous line
                            if ($previousLine !== null
                                && $damageType !== self::DAMAGE_TYPE_FPS
                                && Str::contains($previousLine, $this->vehicleDestructionString)
                            ) {
                                // Parse vehicle destruction using string operations
                                $vehicleData = $this->parseVehicleDestructionLine($previousLine);

                                if ($vehicleData !== null) {
                                    $deathVehicle = $vehicleData['vehicle'];
                                    $deathLocation = $vehicleData['zone'];
                                    $isCombat = $vehicleData['isCombat'];

                                    if ($isCombat && $deathVehicle === $victimDeathZone) {
                                        $vehicleClass = Str::beforeLast($deathVehicle, '_');
                                        $entry['location'] = $deathLocation;
                                        $entry['vehicle'] = $this->vehicleService->getVehicleByClass($vehicleClass);

                                        if ($entry['vehicle'] === null) {
                                            Log::warning('[GAMELOG PARSER] Vehicle not found for class', [
                                                'vehicleClass' => $vehicleClass,
                                                'fullVehicleName' => $deathVehicle,
                                            ]);
                                        }
                                    }
                                }
                            }

                            // Add entry if it meets the criteria
                            // FPS kills: always add
                            // VEHICLE kills: only add if we found the vehicle in the database
                            if (($entry['killType'] === Kill::TYPE_VEHICLE && $entry['vehicle'] !== null)
                                || $entry['killType'] === Kill::TYPE_FPS
                            ) {
                                Log::debug('[GAMELOG PARSER] Adding entry to batch', [
                                    'killType' => $entry['killType'],
                                    'victim' => $entry['victim'],
                                    'killer' => $entry['killer'],
                                    'hasVehicle' => $entry['vehicle'] !== null,
                                ]);

                                $foundEntries[] = $entry;

                                // Process batch when it reaches the configured size
                                if (count($foundEntries) >= $this->batchSize) {
                                    $totalKills += $this->batchProcessKills($foundEntries, $logUpload);
                                    $foundEntries = []; // Clear memory
                                }
                            } else {
                                Log::debug('[GAMELOG PARSER] Entry not added - VEHICLE kill without valid ship', [
                                    'killType' => $entry['killType'],
                                    'victim' => $entry['victim'],
                                    'killer' => $entry['killer'],
                                    'zone' => $entry['location'],
                                ]);
                            }
                        }
                    }

                    $previousLine = $currentLine;
                }

                fclose($handle);
            }
        }

        if ($out['has_arena_commander_kills']) {
            return $out;
        }

        // Process any remaining entries
        if (! empty($foundEntries)) {
            $totalKills += $this->batchProcessKills($foundEntries, $logUpload);
        }

        // Single cache refresh at the very end (after all batches processed)
        if ($totalKills > 0) {
            $this->refreshCaches();
        }

        $out['total_kills'] = $totalKills;

        return $out;
    }

    private function parseVehicleDestructionLine(string $line): ?array
    {
        // Extract vehicle name (after "Vehicle '")
        $vehicleMarker = "Vehicle '";
        $vehicleStart = strpos($line, $vehicleMarker);
        if ($vehicleStart === false) {
            return null;
        }
        $vehicleStart += strlen($vehicleMarker);
        $vehicleEnd = strpos($line, "'", $vehicleStart);
        if ($vehicleEnd === false) {
            return null;
        }
        $vehicle = substr($line, $vehicleStart, $vehicleEnd - $vehicleStart);

        // Extract zone (after "in zone '")
        $zoneMarker = "in zone '";
        $zoneStart = strpos($line, $zoneMarker, $vehicleEnd);
        if ($zoneStart === false) {
            return null;
        }
        $zoneStart += strlen($zoneMarker);
        $zoneEnd = strpos($line, "'", $zoneStart);
        if ($zoneEnd === false) {
            return null;
        }
        $zone = substr($line, $zoneStart, $zoneEnd - $zoneStart);

        // Check if cause is "Combat" (appears near end: "with 'Combat'")
        $isCombat = strpos($line, "with 'Combat'") !== false;

        return [
            'vehicle' => $vehicle,
            'zone' => $zone,
            'isCombat' => $isCombat,
        ];
    }

    private function parseActorDeathLine(string $line): ?array
    {
        // Extract timestamp (between < and >)
        $timestampStart = strpos($line, '<');
        $timestampEnd = strpos($line, '>');
        if ($timestampStart === false || $timestampEnd === false) {
            return null;
        }
        $timestamp = substr($line, $timestampStart + 1, $timestampEnd - $timestampStart - 1);

        // Find the CActor::Kill: section
        $killStart = strpos($line, "CActor::Kill: '");
        if ($killStart === false) {
            return null;
        }
        $killStart += 15; // Length of "CActor::Kill: '"

        // Extract victim (until next ')
        $victimEnd = strpos($line, "'", $killStart);
        if ($victimEnd === false) {
            return null;
        }
        $victim = substr($line, $killStart, $victimEnd - $killStart);

        // Extract victim ID [between brackets]
        $victimIdStart = $victimEnd + 3; // Skip "' ["
        $victimIdEnd = strpos($line, ']', $victimIdStart);
        if ($victimIdEnd === false) {
            return null;
        }
        $victimId = substr($line, $victimIdStart, $victimIdEnd - $victimIdStart);

        // Extract zone (after "in zone '")
        $zoneMarker = "in zone '";
        $zoneStart = strpos($line, $zoneMarker, $victimIdEnd);
        if ($zoneStart === false) {
            return null;
        }
        $zoneStart += strlen($zoneMarker);
        $zoneEnd = strpos($line, "'", $zoneStart);
        if ($zoneEnd === false) {
            return null;
        }
        $zone = substr($line, $zoneStart, $zoneEnd - $zoneStart);

        // Extract killer (after "killed by '")
        $killerMarker = "killed by '";
        $killerStart = strpos($line, $killerMarker, $zoneEnd);
        if ($killerStart === false) {
            return null;
        }
        $killerStart += strlen($killerMarker);
        $killerEnd = strpos($line, "'", $killerStart);
        if ($killerEnd === false) {
            return null;
        }
        $killer = substr($line, $killerStart, $killerEnd - $killerStart);

        // Extract killer ID
        $killerIdStart = $killerEnd + 3; // Skip "' ["
        $killerIdEnd = strpos($line, ']', $killerIdStart);
        if ($killerIdEnd === false) {
            return null;
        }
        $killerId = substr($line, $killerIdStart, $killerIdEnd - $killerIdStart);

        // Extract weapon (after "using '")
        $weaponMarker = "using '";
        $weaponStart = strpos($line, $weaponMarker, $killerIdEnd);
        if ($weaponStart === false) {
            return null;
        }
        $weaponStart += strlen($weaponMarker);
        $weaponEnd = strpos($line, "'", $weaponStart);
        if ($weaponEnd === false) {
            return null;
        }
        $weapon = substr($line, $weaponStart, $weaponEnd - $weaponStart);

        // Extract class (after "[Class ")
        $classMarker = '[Class ';
        $classStart = strpos($line, $classMarker, $weaponEnd);
        if ($classStart === false) {
            return null;
        }
        $classStart += strlen($classMarker);
        $classEnd = strpos($line, ']', $classStart);
        if ($classEnd === false) {
            return null;
        }
        $class = substr($line, $classStart, $classEnd - $classStart);

        // Extract damage type (after "with damage type '")
        $damageTypeMarker = "with damage type '";
        $damageTypeStart = strpos($line, $damageTypeMarker, $classEnd);
        if ($damageTypeStart === false) {
            return null;
        }
        $damageTypeStart += strlen($damageTypeMarker);
        $damageTypeEnd = strpos($line, "'", $damageTypeStart);
        if ($damageTypeEnd === false) {
            return null;
        }
        $damageType = substr($line, $damageTypeStart, $damageTypeEnd - $damageTypeStart);

        return [
            'timestamp' => $timestamp,
            'victim' => $victim,
            'victimId' => $victimId,
            'zone' => $zone,
            'killer' => $killer,
            'killerId' => $killerId,
            'weapon' => $weapon,
            'class' => $class,
            'damageType' => $damageType,
        ];
    }

    private function batchProcessKills(array $foundEntries, LogUpload $logUpload): int
    {
        if (empty($foundEntries)) {
            return 0;
        }

        // Step 1: Extract and batch upsert all players
        $playerMap = $this->batchUpsertPlayers($foundEntries);

        // Step 2: Extract and batch upsert all manufacturers and weapons
        $weaponMap = $this->batchUpsertWeapons($foundEntries);

        // Step 3: Batch insert all kills
        $killsCreated = $this->batchInsertKills($foundEntries, $playerMap, $weaponMap, $logUpload);

        // Step 4: Update player metadata (avatars, orgs) in background
        // This is less critical and can be done asynchronously
        $this->updatePlayersMetadata(array_values($playerMap));

        // Note: Cache refresh moved to end of processGameLog() to refresh only once after all batches

        return $killsCreated;
    }

    private function refreshCaches(): void
    {
        // Refresh recent kills and leaderboards caches
        app(\App\Services\RecentKillsService::class)->refreshCache();
        app(\App\Services\LeaderboardService::class)->refreshLeaderboards();
    }

    private function batchUpsertPlayers(array $foundEntries): array
    {
        $playersData = [];

        foreach ($foundEntries as $entry) {
            // Collect killer data
            if (! isset($playersData[$entry['killer']])) {
                $playersData[$entry['killer']] = [
                    'name' => $entry['killer'],
                    'game_id' => $entry['killerGameId'],
                ];
            }

            // Collect victim data
            if (! isset($playersData[$entry['victim']])) {
                $playersData[$entry['victim']] = [
                    'name' => $entry['victim'],
                    'game_id' => $entry['victimGameId'],
                ];
            }
        }

        if (empty($playersData)) {
            return [];
        }

        // Single bulk upsert (MySQL's INSERT ... ON DUPLICATE KEY UPDATE)
        Player::upsert(
            array_values($playersData),
            ['name'],  // Unique key
            ['game_id']  // Columns to update on conflict
        );

        // Fetch all players in single query
        $names = array_keys($playersData);
        $players = Player::query()->whereIn('name', $names)->get()->keyBy('name');

        return $players->all();
    }

    private function batchUpsertWeapons(array $foundEntries): array
    {
        $weaponsData = [];
        $manufacturersData = [];

        // Extract all unique weapons and manufacturers
        foreach ($foundEntries as $entry) {
            $weaponString = $entry['killWeapon'];
            $killType = $entry['killType'];

            if (! isset($weaponsData[$weaponString])) {
                $parsed = $this->parseWeaponName($weaponString, $killType);
                $weaponsData[$weaponString] = $parsed;

                // Collect manufacturer code
                if (! isset($manufacturersData[$parsed['manufacturer_code']])) {
                    $manufacturersData[$parsed['manufacturer_code']] = [
                        'code' => $parsed['manufacturer_code'],
                        'name' => 'Unknown',
                    ];
                }
            }
        }

        if (empty($manufacturersData)) {
            return [];
        }

        // Bulk upsert manufacturers
        \App\Models\Manufacturer::upsert(
            array_values($manufacturersData),
            ['code'],  // Unique key
            ['name']   // Don't update name if already exists
        );

        // Fetch all manufacturers in single query
        $manufacturerCodes = array_keys($manufacturersData);
        $manufacturers = \App\Models\Manufacturer::query()
            ->whereIn('code', $manufacturerCodes)
            ->get()
            ->keyBy('code');

        // Prepare weapon data with manufacturer IDs
        $weaponBulkData = [];
        foreach ($weaponsData as $weaponString => $parsed) {
            $manufacturer = $manufacturers[$parsed['manufacturer_code']] ?? null;
            if ($manufacturer) {
                $weaponBulkData[] = [
                    'slug' => $parsed['slug'],
                    'name' => $parsed['name'],
                    'manufacturer_id' => $manufacturer->id,
                ];
            }
        }

        if (! empty($weaponBulkData)) {
            // Bulk upsert weapons
            Weapon::upsert(
                $weaponBulkData,
                ['slug'],  // Unique key
                ['name', 'manufacturer_id']  // Update on conflict
            );
        }

        // Fetch all weapons in single query and map by original weapon string
        $slugs = array_column($weaponBulkData, 'slug');
        $weapons = Weapon::query()->whereIn('slug', $slugs)->get()->keyBy('slug');

        // Map back to original weapon strings
        $weaponMap = [];
        foreach ($weaponsData as $weaponString => $parsed) {
            $weapon = $weapons[$parsed['slug']] ?? null;
            if ($weapon) {
                $weaponMap[$weaponString] = $weapon;
            }
        }

        return $weaponMap;
    }

    private function batchInsertKills(array $foundEntries, array $playerMap, array $weaponMap, LogUpload $logUpload): int
    {
        $killsCreated = 0;

        Log::debug('[GAMELOG PARSER] batchInsertKills starting', [
            'entriesCount' => count($foundEntries),
            'playersCount' => count($playerMap),
            'weaponsCount' => count($weaponMap),
        ]);

        // Deduplicate entries within the batch itself to prevent race conditions
        $seenSignatures = [];
        $deduplicatedEntries = [];

        foreach ($foundEntries as $entry) {
            $killer = $playerMap[$entry['killer']] ?? null;
            $victim = $playerMap[$entry['victim']] ?? null;
            $weapon = $weaponMap[$entry['killWeapon']] ?? null;

            if (! $killer || ! $victim || ! $weapon) {
                continue;
            }

            // Create unique signature for this kill
            // Note: We DON'T include weapon_id because the same kill can be logged with different weapons
            $shipId = $entry['killType'] === Kill::TYPE_VEHICLE ? $entry['vehicle']?->id : null;
            $signature = implode('|', [
                $entry['timestamp'],
                $killer->id,
                $victim->id,
                $entry['killType'],
                $entry['location'],
                $shipId ?? 'null',
            ]);

            if (! isset($seenSignatures[$signature])) {
                $seenSignatures[$signature] = true;
                $deduplicatedEntries[] = $entry;
            } else {
                Log::debug('[GAMELOG PARSER] Skipping duplicate within batch', [
                    'killer' => $entry['killer'],
                    'victim' => $entry['victim'],
                    'timestamp' => $entry['timestamp'],
                ]);
            }
        }

        Log::debug('[GAMELOG PARSER] Deduplicated batch', [
            'originalCount' => count($foundEntries),
            'deduplicatedCount' => count($deduplicatedEntries),
            'duplicatesRemoved' => count($foundEntries) - count($deduplicatedEntries),
        ]);

        foreach ($deduplicatedEntries as $entry) {
            $timestamp = Carbon::parse($entry['timestamp']);
            $killer = $playerMap[$entry['killer']];
            $victim = $playerMap[$entry['victim']];
            $weapon = $weaponMap[$entry['killWeapon']];

            $kill = $this->findOrCreateKill(
                $timestamp,
                $entry['killType'] === Kill::TYPE_VEHICLE ? $entry['vehicle']?->id : null,
                $weapon->id,
                $victim->id,
                $killer->id,
                $entry['killType'],
                $entry['location']
            );

            if ($kill->wasRecentlyCreated) {
                if ($logUpload->user) {
                    $kill->user()->associate($logUpload->user);
                }
                $kill->logUpload()->associate($logUpload);
                $kill->save();

                $killsCreated++;
                Log::debug('[GAMELOG PARSER] Kill created', [
                    'killId' => $kill->id,
                    'killer' => $entry['killer'],
                    'victim' => $entry['victim'],
                    'killsCreatedSoFar' => $killsCreated,
                ]);
            } else {
                Log::debug('[GAMELOG PARSER] Kill already exists (duplicate)', [
                    'killId' => $kill->id,
                    'killer' => $entry['killer'],
                    'victim' => $entry['victim'],
                ]);
            }
        }

        Log::debug('[GAMELOG PARSER] batchInsertKills completed', ['killsCreated' => $killsCreated]);

        return $killsCreated;
    }

    private function findOrCreateKill(
        Carbon $timestamp,
        ?int $shipId,
        int $weaponId,
        int $victimId,
        int $killerId,
        string $type,
        string $location
    ): Kill {
        // Define time window for duplicate detection (±3 seconds by default)
        $startTime = $timestamp->copy()->subSeconds($this->killTimeToleranceSeconds);
        $endTime = $timestamp->copy()->addSeconds($this->killTimeToleranceSeconds);

        // Try to find existing kill within the time window
        // Note: We DON'T check weapon_id because the same kill can be logged with different weapons
        $existingKill = Kill::query()
            ->where('killer_id', $killerId)
            ->where('victim_id', $victimId)
            ->where('type', $type)
            ->where('location', $location)
            ->where('destroyed_at', '>=', $startTime)
            ->where('destroyed_at', '<=', $endTime)
            ->when($shipId !== null, fn ($query) => $query->where('ship_id', $shipId))
            ->first();

        if ($existingKill) {
            return $existingKill;
        }

        // Create new kill if no duplicate found
        return Kill::query()->create([
            'destroyed_at' => $timestamp,
            'ship_id' => $shipId,
            'weapon_id' => $weaponId,
            'victim_id' => $victimId,
            'killer_id' => $killerId,
            'type' => $type,
            'location' => $location,
        ]);
    }

    private function updatePlayersMetadata(array $players): void
    {
        // Dispatch background jobs for player metadata updates instead of synchronous HTTP calls
        foreach ($players as $player) {
            \App\Jobs\UpdatePlayerMetadataJob::dispatch($player);
        }
    }

    public function recordKill(
        string $timeStamp,
        string $killType,
        string $location,
        string $killer,
        string $victim,
        string $killWeapon,
        ?Ship $vehicle = null,
        ?string $victimGameId = null,
        ?string $killerGameId = null,
        ?User $user = null,
        ?LogUpload $logUpload = null,
    ): ?Kill {
        $timestamp = Carbon::parse($timeStamp);

        $victimModel = Player::query()->updateOrCreate([
            'name' => $victim,
        ], [
            'game_id' => $victimGameId,
        ]);

        $killerModel = Player::query()->updateOrCreate([
            'name' => $killer,
        ], [
            'game_id' => $killerGameId,
        ]);

        if ($victimModel && $killerModel) {
            $killWeaponModel = $this->getOrCreateWeapon($killWeapon, $killType);

            $this->updatePlayerMetadata($killerModel);
            $this->updatePlayerMetadata($victimModel);

            $kill = $this->findOrCreateKill(
                $timestamp,
                $killType === Kill::TYPE_VEHICLE ? $vehicle?->id : null,
                $killWeaponModel->id,
                $victimModel->id,
                $killerModel->id,
                $killType,
                $location
            );

            if ($kill->wasRecentlyCreated) {
                if ($user) {
                    $kill->user()->associate($user);
                }

                if ($logUpload) {
                    $kill->logUpload()->associate($logUpload);
                }

                $kill->save();
            }

            return $kill;
        }

        return null;
    }

    private function getOrCreateWeapon(string $killWeapon, string $killType): Weapon
    {
        // Parse weapon name based on kill type
        $parsedWeapon = $this->parseWeaponName($killWeapon, $killType);

        // Find or create manufacturer
        $manufacturer = \App\Models\Manufacturer::query()->firstOrCreate([
            'code' => $parsedWeapon['manufacturer_code'],
        ], [
            'name' => 'Unknown', // Default to Unknown if not in seeder
        ]);

        return Weapon::query()->firstOrCreate([
            'slug' => $parsedWeapon['slug'],
        ], [
            'manufacturer_id' => $manufacturer->id,
            'name' => $parsedWeapon['name'],
        ]);
    }

    public function updatePlayerMetadata(Player $player): void
    {
        $avatar = $this->getPlayerAvatar($player);

        if ($avatar !== null) {
            $player->update(['avatar' => $avatar]);
        }

        $orgData = $this->getPlayerOrgData($player);

        if ($orgData['name'] !== null) {
            $orgModel = Organization::query()->firstOrCreate([
                'name' => $orgData['name'],
                'icon' => $orgData['icon'],
                'spectrum_id' => $orgData['spectrum_id'],
            ]);

            $player->organization()->associate($orgModel)->save();
        } elseif ($player->organization()->exists()) {
            $player->organization()->dissociate()->save();
        }
    }

    private function getPlayerAvatar(Player $player): ?string
    {
        $playerName = $player->name;

        // Skip if avatar already exists and is valid (contains /media/ or is the default RSI avatar)
        $isValidAvatar = ! empty($player->avatar) && (
            str_contains($player->avatar, '/media/') ||
            str_contains($player->avatar, '/static/images/account/avatar_default')
        );

        if ($isValidAvatar) {
            return null;
        }

        // Cache avatar lookups for 24 hours
        return Cache::remember("player_avatar_{$playerName}", 86400, function () use ($playerName) {
            $response = null;
            $avatar = null;

            try {
                $response = Http::withUrlParameters([
                    'endpoint' => 'https://robertsspaceindustries.com/citizens',
                    'playerName' => $playerName,
                ])->get('{+endpoint}/{playerName}');
            } catch (\Exception $e) {
                Log::error('[RSI.COM PARSER] Unable to check player existence: '.$e->getMessage(), [
                    'playerName' => $playerName,
                ]);
            }

            if ($response !== null && $response->successful()) {
                $dom = new Dom;

                try {
                    $dom->loadStr($response->body());
                    $contents = $dom->find('div[class=profile left-col]');

                    if (! empty($contents)) {
                        $avatarDiv = $contents->find('div[class=thumb]');

                        if (! empty($avatarDiv)) {
                            $img = $avatarDiv->find('img');

                            if (! empty($img)) {
                                $avatar = $img->getAttribute('src');

                                if (! empty($avatar)) {
                                    $avatar = Str::trim($avatar);

                                    // Validate avatar URL - must contain /media/ or be the default RSI avatar
                                    $isValid = str_contains($avatar, '/media/') ||
                                               str_contains($avatar, '/static/images/account/avatar_default');

                                    if (! $isValid) {
                                        $avatar = null;
                                    }
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('[ORG PARSER] Unable to load player data: '.$e->getMessage(), [
                        'playerName' => $playerName,
                        'code' => $e->getCode(),
                        'line' => $e->getLine(),
                        'file' => $e->getFile(),
                        'message' => $e->getMessage(),
                    ]);
                }
            }

            return $avatar;
        });
    }

    private function getPlayerOrgData(Player $player): array
    {
        $playerName = $player->name;

        // Cache organization data for 1 hour (orgs change less frequently than avatars)
        return Cache::remember("player_org_{$playerName}", 3600, function () use ($playerName) {
            $response = null;
            $output = [
                'icon' => Organization::DEFAULT_ORG_PIC_URL,
                'name' => Organization::ORG_NONE,
                'spectrum_id' => Organization::ORG_NONE,
            ];

            try {
                $response = Http::withUrlParameters([
                    'endpoint' => 'https://robertsspaceindustries.com/en/citizens',
                    'playerName' => $playerName,
                ])->get('{+endpoint}/{playerName}/organizations');
            } catch (\Exception $e) {
                Log::error('[RSI.COM PARSER] Unable to check player org: '.$e->getMessage(), [
                    'playerName' => $playerName,
                ]);
            }

            if ($response !== null && $response->successful()) {
                $dom = new Dom;

                try {
                    $dom->loadStr($response->body());
                    $contents = $dom->find('div[class=box-content org main visibility-R]')[0] ?? null;

                    if ($contents !== null) {
                        $output['name'] = Organization::ORG_REDACTED;
                        $output['icon'] = Organization::REDACTED_ORG_PIC_URL;
                        $output['spectrum_id'] = Organization::ORG_REDACTED;
                    } else {
                        $contents = $dom->find('div[class=box-content org main visibility-V]')[0] ?? null;

                        if ($contents !== null) {
                            $thumbDiv = $contents->find('div[class=thumb]')[0] ?? null;

                            if ($thumbDiv !== null) {
                                $img = $thumbDiv->find('img')[0] ?? null;

                                if ($img !== null) {
                                    $output['icon'] = $img->getAttribute('src');
                                }
                            }

                            $orgInfo = $contents->find('div[class=info]')[0] ?? null;

                            if ($orgInfo !== null) {
                                $orgMeta = $orgInfo->find('p[class=entry]');
                                $orgNameHtml = $orgMeta[0] ?? null;

                                if ($orgNameHtml !== null) {
                                    $orgName = $orgNameHtml->find('a')[0] ?? null;
                                    $output['name'] = $orgName->firstChild()?->text ?? null;
                                }

                                $orgSpectrumIdHtml = $orgMeta[1] ?? null;

                                if (! empty($orgSpectrumIdHtml)) {
                                    $orgSpectrumId = $orgSpectrumIdHtml->find('strong[class=value]')[0] ?? null;
                                    $output['spectrum_id'] = $orgSpectrumId->firstChild()?->text ?? null;
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('[ORG PARSER] Unable to load organization: '.$e->getMessage(), [
                        'playerName' => $playerName,
                        'code' => $e->getCode(),
                        'line' => $e->getLine(),
                        'file' => $e->getFile(),
                        'message' => $e->getMessage(),
                    ]);
                }
            }

            return $output;
        });
    }

    private function isNpc(string $playerName): bool
    {
        if (Str::lower($playerName) === self::UNKNOWN) {
            return true;
        }

        // Pattern-based detection (instant) - catches 99% of NPCs from config
        foreach ($this->npcPatterns as $pattern) {
            if (preg_match($pattern, $playerName)) {
                return true;
            }
        }

        // Check database (fast) - existing players are definitely not NPCs
        $playerExists = Player::query()->where('name', $playerName)->exists();

        if ($playerExists) {
            return false;
        }

        // HTTP fallback only for ambiguous cases (cache for 7 days)
        return Cache::remember("is_npc_{$playerName}", 604800, function () use ($playerName) {
            try {
                $response = Http::timeout(5)->withUrlParameters([
                    'endpoint' => 'https://robertsspaceindustries.com/en/citizens',
                    'playerName' => $playerName,
                ])->get('{+endpoint}/{playerName}');

                return ! $response->ok();
            } catch (\Exception $e) {
                Log::warning('[RSI.COM PARSER] Unable to check player existence, assuming NPC: '.$e->getMessage(), [
                    'playerName' => $playerName,
                ]);

                // Fail safe: assume NPC on error to avoid blocking parsing
                return true;
            }
        });
    }

    public function parseWeaponName(string $weaponString, string $killType): array
    {
        // Extract manufacturer code (everything before first underscore)
        $manufacturerCode = Str::contains($weaponString, '_')
            ? Str::before($weaponString, '_')
            : 'NONE';

        // Remove manufacturer prefix for weapon name parsing
        $withoutManufacturer = Str::contains($weaponString, '_')
            ? Str::after($weaponString, '_')
            : $weaponString;

        if ($killType === Kill::TYPE_FPS) {
            $parsed = $this->parseFpsWeaponName($withoutManufacturer);
        } else {
            $parsed = $this->parseVehicleWeaponName($withoutManufacturer);
        }

        // Add manufacturer code to the result
        $parsed['manufacturer_code'] = $manufacturerCode;

        return $parsed;
    }

    private function parseFpsWeaponName(string $weaponString): array
    {
        // First, remove numeric suffix (same as vehicle weapons)
        $parts = explode('_', $weaponString);
        $lastPart = end($parts);

        if (is_numeric($lastPart)) {
            array_pop($parts);
            $weaponString = implode('_', $parts);
        }

        // Now parse the weapon type and pattern
        $parts = explode('_', Str::lower($weaponString));
        $weaponType = null;
        $weaponPattern = null;

        // Find the weapon type (energy, ballistic, melee)
        foreach ($parts as $part) {
            if (in_array($part, $this->fpsWeaponTypes)) {
                $weaponType = $part;
                break;
            }
        }

        // Find the weapon pattern (rifle, lmg, pistol, etc.)
        foreach ($parts as $part) {
            if (in_array($part, $this->fpsWeaponPatterns)) {
                $weaponPattern = $part;
                break;
            }
        }

        // If we found both type and pattern, format as "TYPE WEAPON"
        if ($weaponType && $weaponPattern) {
            $name = Str::title($weaponType).' '.Str::title($weaponPattern);
            $slug = Str::slug($weaponType.'-'.$weaponPattern);

            return [
                'slug' => $slug,
                'name' => $name,
            ];
        }

        // Fallback: use the weapon string as-is
        return [
            'slug' => Str::slug($weaponString),
            'name' => Str::title(Str::replace('_', ' ', $weaponString)),
        ];
    }

    private function parseVehicleWeaponName(string $weaponString): array
    {
        // Remove numeric suffix after last underscore
        $parts = explode('_', $weaponString);
        $lastPart = end($parts);

        // If the last part is purely numeric, remove it
        if (is_numeric($lastPart)) {
            array_pop($parts);
            $weaponString = implode('_', $parts);
        }

        $slug = Str::slug($weaponString);
        $name = Str::title(Str::replace('_', ' ', $weaponString));

        return [
            'slug' => $slug,
            'name' => $name,
        ];
    }
}
