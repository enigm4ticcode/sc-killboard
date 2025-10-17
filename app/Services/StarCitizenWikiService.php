<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StarCitizenWikiService
{
    protected string $baseUrl;

    protected int $perPage;

    public function __construct(array $config)
    {
        $this->baseUrl = $config['base_url'];
        $this->perPage = $config['per_page'];
    }

    public function getVehicles(int $page = 1, int $limit = 0): array
    {
        $url = $this->baseUrl . '/vehicles';
        $options = [
            'page' => $page,
            'limit' => $limit === 0 ? $this->perPage : $limit,
        ];

        try {
            $response = Http::get($url, $options);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('[WIKI SERVICE] Error: ' . $e->getMessage());
        }

        return [];
    }

    public function getVehicleById(string $uuid): array
    {
        $url = $this->baseUrl . '/vehicles/' . $uuid;

        try {
            $response = Http::get($url);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('[WIKI SERVICE] Error: ' . $e->getMessage());
        }

        return [];
    }

    public function search(string $query): array
    {
        $url = $this->baseUrl . '/vehicles/search';

        try {
            $response = Http::post($url, ['query' => $query]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('[WIKI SERVICE] Error: ' . $e->getMessage());
        }

        return [];
    }
}
