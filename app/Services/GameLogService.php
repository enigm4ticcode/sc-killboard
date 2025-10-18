<?php

namespace App\Services;

use App\Models\Kill;
use App\Models\Organization;
use App\Models\Player;
use App\Models\Weapon;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;
use PHPHtmlParser\Dom;

class GameLogService
{
    private const UNKNOWN = 'unknown';

    protected string $actorKillString;

    protected string $isoTimestampPattern;

    protected array $arenaCommanderZonePrefixes;

    protected VehicleService $vehicleService;

    public function __construct(VehicleService $vehicleService, array $config)
    {
        $this->vehicleService = $vehicleService;
        $this->actorKillString = Str::upper($config['actor_kill_string']);
        $this->isoTimestampPattern = $config['iso_timestamp_pattern'];
        $this->arenaCommanderZonePrefixes = Arr::map(
            $config['arena_commander_zone_prefixes'],
            function (string $value, string $key) {
                return Str::lower($value);
            }
        );
    }

    public function processGameLog(string $path): int
    {
        $filePath = Storage::path($path);
        $uuid = Str::uuid()->toString();
        $cacheKey = "total_kills_$uuid";
        Cache::put($cacheKey, 0);

        LazyCollection::make(function () use ($filePath) {
            $handle = fopen($filePath, 'r');
            while (($line = fgets($handle)) !== false) {
                yield $line;
            }
            fclose($handle);
        })->each(function ($line) use ($cacheKey) {
            if (Str::contains(Str::upper($line), $this->actorKillString)) {
                $explodedLine = explode(' ', $line);
                $timestamp = Carbon::parse(Str::match($this->isoTimestampPattern, $explodedLine[0]));
                $victim = Str::remove("'", $explodedLine[5]);
                $victimGameId = (int) Str::remove('[', Str::remove(']', $explodedLine[6]));
                $killerGameId = (int) Str::remove('[', Str::remove(']', $explodedLine[13]));
                $killWeapon = Str::slug(Str::beforeLast($explodedLine[15], '_'));
                $killer = Str::remove("'", $explodedLine[12]);
                $victimZone = Str::trim(Str::beforeLast($explodedLine[9], '_'), "'\" ");

                // Omit NPC kills and environment deaths
                if ($victim !== $killer
                    && ! $this->isNpc($victim)
                    && ! $this->isNpc($killer)
                    && ! Str::startsWith(Str::lower($victimZone), $this->arenaCommanderZonePrefixes)
                ) {
                    $killType = Kill::TYPE_FPS;
                    $vehicle = $this->vehicleService->getVehicleByClass($victimZone);

                    // Try searching for the closest match
                    if ($vehicle) {
                        $killType = Kill::TYPE_VEHICLE;
                    }

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
                        $killWeaponModel = Weapon::query()->firstOrCreate([
                            'slug' => $killWeapon,
                        ], [
                            'name' => Str::title(Str::replace('-', ' ', $killWeapon)),
                        ]);

                        $killerAvatar = $this->getPlayerAvatar($killerModel);
                        $victimAvatar = $this->getPlayerAvatar($victimModel);

                        if ($killerAvatar !== null) {
                            $killerModel->update(['avatar' => $killerAvatar]);
                        }

                        if ($victimAvatar !== null) {
                            $victimModel->update(['avatar' => $victimAvatar]);
                        }

                        $killerOrgData = $this->getPlayerOrgData($killerModel);
                        $victimOrgData = $this->getPlayerOrgData($victimModel);

                        if ($killerOrgData['name'] !== null) {
                            $killerOrgModel = Organization::query()->firstOrCreate([
                                'name' => $killerOrgData['name'],
                                'icon' => $killerOrgData['icon'],
                                'spectrum_id' => $killerOrgData['spectrum_id'],
                            ]);

                            $killerModel->organization()->associate($killerOrgModel)->save();
                        } elseif ($killerModel->organization()->exists()) {
                            $killerModel->organization()->dissociate()->save();
                        }

                        if ($victimOrgData['name'] !== null) {
                            $victimOrgModel = Organization::query()->firstOrCreate([
                                'name' => $victimOrgData['name'],
                                'icon' => $victimOrgData['icon'],
                                'spectrum_id' => $victimOrgData['spectrum_id'],
                            ]);

                            $victimModel->organization()->associate($victimOrgModel)->save();
                        } elseif ($victimModel->organization()->exists()) {
                            $victimModel->organization()->dissociate()->save();
                        }

                        Kill::query()->firstOrCreate([
                            'destroyed_at' => $timestamp,
                            'ship_id' => $killType === Kill::TYPE_VEHICLE ? $vehicle->id : null,
                            'weapon_id' => $killWeaponModel->id,
                            'victim_id' => $victimModel->id,
                            'killer_id' => $killerModel->id,
                            'type' => $killType,
                        ]);

                        Cache::increment($cacheKey);
                    }
                }
            }
        });

        return (int) Cache::pull($cacheKey);
    }

    private function getPlayerAvatar(Player $player): ?string
    {
        $avatar = null;

        $playerName = $player->name;

        $response = Http::withUrlParameters([
            'endpoint' => 'https://robertsspaceindustries.com/citizens',
            'playerName' => $playerName,
        ])->get('{+endpoint}/{playerName}');

        if ($response->successful()) {
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
    }

    private function getPlayerOrgData(Player $player): array
    {
        $output = [
            'icon' => Organization::DEFAULT_ORG_PIC_URL,
            'name' => Organization::ORG_NONE,
            'spectrum_id' => Organization::ORG_NONE,
        ];

        $playerName = $player->name;
        $response = Http::withUrlParameters([
            'endpoint' => 'https://robertsspaceindustries.com/en/citizens',
            'playerName' => $playerName,
        ])->get('{+endpoint}/{playerName}/organizations');

        if ($response->successful()) {
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
    }

    private function isNpc(string $playerName): bool
    {
        if (Str::lower($playerName) === self::UNKNOWN) {
            return true;
        }

        $playerExists = Player::query()->where('name', $playerName)->exists();

        if ($playerExists) {
            return false;
        }

        try {
            $response = Http::withUrlParameters([
                'endpoint' => 'https://robertsspaceindustries.com/en/citizens',
                'playerName' => $playerName,
            ])->get('{+endpoint}/{playerName}');

            return ! $response->ok();
        } catch (\Exception $e) {
            Log::error('[IS_NPC PARSER] Unable to check player existence: '.$e->getMessage(), [
                'playerName' => $playerName,
            ]);
        }

        return true;
    }
}
