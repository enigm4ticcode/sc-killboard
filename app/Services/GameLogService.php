<?php

namespace App\Services;

use App\Models\Kill;
use App\Models\LogUpload;
use App\Models\Organization;
use App\Models\Player;
use App\Models\User;
use App\Models\Weapon;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PHPHtmlParser\Dom;

class GameLogService
{
    private const UNKNOWN = 'unknown';

    private const COMBAT = 'COMBAT';

    private const JOIN_MATCH_STRING = 'JOIN MATCH';

    private const DAMAGE_TYPE_FPS = 'BULLET';

    protected string $actorKillString;

    protected string $vehicleDestructionString;

    protected array $validPvpDamageTypes;

    protected string $isoTimestampPattern;

    protected VehicleService $vehicleService;

    protected int $linesToRead;

    public function __construct(VehicleService $vehicleService, array $config)
    {
        $this->vehicleService = $vehicleService;
        $this->actorKillString = Str::upper($config['actor_kill_string']);
        $this->vehicleDestructionString = Str::upper($config['vehicle_destruction_string']);
        $this->isoTimestampPattern = $config['iso_timestamp_pattern'];
        $this->validPvpDamageTypes = Arr::map($config['valid_pvp_damage_types'], function (string $value, string $key) {
            return Str::upper($value);
        });
        $this->linesToRead = $config['lines_to_read'];
    }

    public function processGameLog(string $filePath, LogUpload $logUpload): array
    {
        $out = [
            'total_kills' => 0,
            'has_arena_commander_kills' => false,
        ];

        $uuid = Str::uuid7()->toString();
        $cacheKey = "total_kills_$uuid";
        Cache::put($cacheKey, 0);

        $foundEntries = [];
        $previousLine = null;

        if (Storage::exists($filePath)) {
            $handle = Storage::readStream($filePath);

            if ($handle) {
                while (($currentLine = fgets($handle)) !== false) {
                    $trimmedCurrent = trim($currentLine);

                    if (Str::contains(Str::upper($currentLine), self::JOIN_MATCH_STRING)) {
                        $out['has_arena_commander_kills'] = true;

                        break;
                    }

                    if (Str::contains(Str::upper($trimmedCurrent), $this->actorKillString)) {
                        $explodedKillString = explode(' ', $trimmedCurrent);
                        $damageType = Str::upper(Str::trim($explodedKillString[21], "'\" "));

                        if (Str::contains($damageType, $this->validPvpDamageTypes)) {
                            $explodedLine = explode(' ', $trimmedCurrent);
                            $timestamp = Carbon::parse(Str::match($this->isoTimestampPattern, $explodedLine[0]));
                            $victim = Str::remove("'", $explodedLine[5]);
                            $victimGameId = (int) Str::remove('[', Str::remove(']', $explodedLine[6]));
                            $killerGameId = (int) Str::remove('[', Str::remove(']', $explodedLine[13]));
                            $killWeapon = Str::slug(Str::beforeLast($explodedLine[15], '_'));
                            $killer = Str::remove("'", $explodedLine[12]);
                            $victimZoneRaw = Str::trim($explodedLine[9], "'\" ");
                            $victimZone = Str::trim(Str::beforeLast($victimZoneRaw, '_'), "'\" ");

                            if ($victim !== $killer && ! $this->isNpc($victim) && ! $this->isNpc($killer)) {
                                $entry = [
                                    'timestamp' => $timestamp,
                                    'victim' => $victim,
                                    'killer' => $killer,
                                    'location' => $victimZone,
                                    'victimGameId' => $victimGameId,
                                    'killerGameId' => $killerGameId,
                                    'killWeapon' => $killWeapon,
                                    'vehicle' => null,
                                    'killType' => $damageType === self::DAMAGE_TYPE_FPS ? Kill::TYPE_FPS : Kill::TYPE_VEHICLE,
                                ];

                                if ($previousLine !== null
                                    && $damageType !== self::DAMAGE_TYPE_FPS
                                    && Str::contains(Str::upper($previousLine), $this->vehicleDestructionString)
                                ) {
                                    $explodedDestructionString = explode(' ', $previousLine);
                                    $deathZoneRaw = Str::trim($explodedDestructionString[6], "'\" ");
                                    $deathLocation = Str::trim($explodedDestructionString[10], "'\" ");
                                    $isCombat = Str::upper(Str::trim($explodedDestructionString[41], "'\" ")) === self::COMBAT;

                                    if ($isCombat && $deathZoneRaw === $victimZoneRaw) {
                                        $entry['location'] = $deathLocation;
                                        $entry['vehicle'] = $this->vehicleService->getVehicleByClass($victimZone);
                                    }
                                }

                                if (($entry['killType'] === Kill::TYPE_VEHICLE && ($entry['vehicle'] !== null))
                                    || $entry['killType'] === Kill::TYPE_FPS
                                ) {
                                    $foundEntries[] = $entry;
                                }
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

        foreach ($foundEntries as $batch) {
            $timestamp = Carbon::parse($batch['timestamp']);
            $killType = $batch['killType'];
            $location = $batch['location'];
            $vehicle = $batch['vehicle'];
            $victimGameId = $batch['victimGameId'];
            $victim = $batch['victim'];
            $killerGameId = $batch['killerGameId'];
            $killer = $batch['killer'];
            $killWeapon = $batch['killWeapon'];

            $kill = $this->recordKill(
                $timestamp,
                $killType,
                $location,
                $killer,
                $victim,
                $killWeapon,
                $vehicle,
                $victimGameId,
                $killerGameId,
                $logUpload->user,
                $logUpload,
            );

            if ($kill) {
                Cache::increment($cacheKey);
            }
        }

        $out['total_kills'] = (int) Cache::pull($cacheKey);

        return $out;
    }

    public function recordKill(
        string $timeStamp,
        string $killType,
        string $location,
        string $killer,
        string $victim,
        string $killWeapon,
        ?string $vehicle = null,
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

            $kill = Kill::query()->firstOrCreate([
                'destroyed_at' => $timestamp,
                'ship_id' => $killType === Kill::TYPE_VEHICLE ? $vehicle->id : null,
                'weapon_id' => $killWeaponModel->id,
                'victim_id' => $victimModel->id,
                'killer_id' => $killerModel->id,
                'type' => $killType,
                'location' => $location,
            ]);

            if ($user) {
                $kill->user()->associate($user);
                $kill->save();
            }

            if ($logUpload) {
                $kill->logUpload()->associate($logUpload);
                $kill->save();
            }

            return $kill;
        }

        return null;
    }

    private function getPlayerAvatar(Player $player): ?string
    {
        $response = null;
        $avatar = null;
        $playerName = $player->name;

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
        $playerName = $player->name;
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
            Log::error('[RSI.COM PARSER] Unable to check player existence: '.$e->getMessage(), [
                'playerName' => $playerName,
            ]);
        }

        return true;
    }
}
