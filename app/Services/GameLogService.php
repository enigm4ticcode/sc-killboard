<?php

namespace App\Services;

use App\Models\Kill;
use App\Models\Organization;
use App\Models\Player;
use App\Models\Ship;
use App\Models\Weapon;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;
use PHPHtmlParser\Dom;

class GameLogService
{
    protected string $actorKillString;

    protected string $isoTimestampPattern;

    public function __construct(array $config)
    {
        $this->actorKillString = $config['actor_kill_string'];
        $this->isoTimestampPattern = $config['iso_timestamp_pattern'];
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
            if (Str::contains($line, $this->actorKillString)) {
                $explodedLine = explode(' ', $line);
                $timestamp = Carbon::parse(Str::match($this->isoTimestampPattern, $explodedLine[0]));
                $victim = Str::remove("'", $explodedLine[5]);
                $victimGameId = (int) Str::remove('[', Str::remove(']', $explodedLine[6]));
                $victimShip = Str::slug(Str::beforeLast($explodedLine[9], '_'));
                $killer = Str::remove("'", $explodedLine[12]);
                $killerGameId = (int) Str::remove('[', Str::remove(']', $explodedLine[13]));
                $killWeapon = Str::slug(Str::beforeLast($explodedLine[15], '_'));

                $killWeaponModel = Weapon::query()->firstOrCreate([
                    'slug' => $killWeapon,
                ], [
                    'name' => Str::title(Str::replace('-', ' ', $killWeapon)),
                ]);

                $victimShipModel = Ship::query()->firstOrCreate([
                    'slug' => $victimShip,
                ], [
                    'name' => Str::title(Str::replace('-', ' ', $victimShip)),
                ]);

                $victimModel = Player::query()->firstOrCreate([
                    'name' => $victim,
                ], [
                    'game_id' => $victimGameId,
                ]);

                $killerModel = Player::query()->firstOrCreate([
                    'name' => $killer,
                ], [
                    'game_id' => $killerGameId,
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
                    'ship_id' => $victimShipModel->id,
                    'weapon_id' => $killWeaponModel->id,
                    'victim_id' => $victimModel->id,
                    'killer_id' => $killerModel->id,
                ]);

                Cache::increment($cacheKey);

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
                Log::error('[ORG PARSER] Unable to load player data: '.$e->getMessage());
            }
        }

        return $avatar;
    }

    private function getPlayerOrgData(Player $player): array
    {
        $output = [
            'icon' => null,
            'name' => null,
            'spectrum_id' => null,
        ];

        $playerName = $player->name;
        $response = Http::withUrlParameters([
            'endpoint' => 'https://robertsspaceindustries.com/citizens',
            'playerName' => $playerName,
        ])->get('{+endpoint}/{playerName}/organizations');

        if ($response->successful()) {
            $dom = new Dom;

            try {
                $dom->loadStr($response->body());
                $contents = $dom->find('div[class=box-content org main visibility-V]');

                if (! empty($contents)) {
                    $thumbDiv = $contents->find('div[class=thumb]');

                    if (! empty($thumbDiv)) {
                        $img = $thumbDiv->find('img');

                        if (! empty($img)) {
                            $output['icon'] = $img->getAttribute('src');
                        }
                    }

                    $orgInfo = $contents->find('div[class=info]');

                    if (! empty($orgInfo)) {
                        $orgMeta = $orgInfo->find('p[class=entry]');
                        $orgNameHtml = $orgMeta[0] ?? null;

                        if (! empty($orgNameHtml)) {
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
            } catch (\Exception $e) {
                Log::error('[ORG PARSER] Unable to load organization: '.$e->getMessage());
            }
        }

        return $output;
    }
}
