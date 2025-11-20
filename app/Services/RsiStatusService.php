<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RsiStatusService
{
    protected const AC = 'Arena Commander';

    protected const PU = 'Persistent Universe';

    protected const PLATFORM = 'Platform';

    protected string $baseUrl;

    protected string $indexJsonUri;

    protected string $cacheKey;

    protected int $ttl;

    public function __construct(string $baseUrl, string $indexJsonUri, string $cacheKey, int $ttl)
    {
        $this->baseUrl = $baseUrl;
        $this->indexJsonUri = $indexJsonUri;
        $this->cacheKey = $cacheKey;
        $this->ttl = $ttl;
    }

    public function getRsiStatus(): array
    {
        return Cache::remember($this->cacheKey, $this->ttl, function () {
            return $this->retrieveRsiStatus();
        });
    }

    private function retrieveRsiStatus(): array
    {
        $platformSlug = Str::slug(self::PLATFORM, '_');
        $puSlug = Str::slug(self::PU, '_');
        $acSlug = Str::slug(self::AC, '_');

        $output = [
            $platformSlug => 'unknown',
            $puSlug => 'unknown',
            $acSlug => 'unknown',
        ];

        try {
            $response = Http::get($this->baseUrl.'/'.$this->indexJsonUri);

            if ($response->successful()) {
                $responseJson = $response->json();
                $systems = $responseJson['systems'];

                foreach ($systems as $system) {
                    switch ($system['name']) {
                        case self::AC:
                            $output[$acSlug] = Str::remove('app.', $system['status'] ?? 'unknown');
                            break;
                        case self::PU:
                            $output[$puSlug] = Str::remove('app.', $system['status'] ?? 'unknown');
                            break;
                        case self::PLATFORM:
                            $output[$platformSlug] = Str::remove('app.', $system['status'] ?? 'unknown');
                            break;
                        default:
                            break;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('[RSI STATUS SERVICE] Unable to retrieve RSI status: '.$e->getMessage(), [
                'baseUrl' => $this->baseUrl,
                'indexJsonUri' => $this->indexJsonUri,
            ]);
        }

        return $output;
    }
}
