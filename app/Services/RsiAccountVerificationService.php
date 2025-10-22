<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PHPHtmlParser\Dom;

class RsiAccountVerificationService
{
    protected string|null $baseUrl;

    protected string|null $pattern;

    public function __construct(array $config)
    {
        $this->baseUrl = $config['base_url'];
        $this->pattern = $config['pattern'];
    }

    public function verifyBiographyKey(string $playerName, string $playerKey): bool
    {
        $key = '';
        $url = "$this->baseUrl/{$playerName}";
        $response = null;
        try {
            $response = Http::get($url);
        } catch (\Exception $e) {
            Log::error('[RSI VERIFICATIONS SERVICE] Unable to verify player account: '.$e->getMessage(), [
                'playerName' => $playerName,
                'url' => $url,
            ]);
        }

        if ($response !== null && $response->ok()) {
            $dom = new Dom();
            $biographyText = '';

            try {
                $dom->loadStr($response->body());
                $contents = $dom->find('div[class=entry bio]')[0] ?? null;

                if ($contents !== null) {
                    $bio = $contents->find('div[class=value]')[0] ?? null;

                    if ($bio !== null) {
                        $biographyText = $bio->innerHtml();
                    }
                }
            } catch (\Exception $e) {
                Log::error('[RSI VERIFICATIONS SERVICE] Unable to parse player profile: '.$e->getMessage(), [
                    'playerName' => $playerName,
                    'url' => $url,
                ]);
            }

            if (! empty($biographyText)) {
                $matches = [];
                if (preg_match($this->pattern, $biographyText, $matches)) {
                    $key = $matches[1] ?? '';
                }

                if ($key === $playerKey) {
                    return true;
                }
            }
        }

        return false;
    }
}
