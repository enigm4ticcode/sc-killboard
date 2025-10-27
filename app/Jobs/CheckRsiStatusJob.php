<?php

namespace App\Jobs;

use App\Services\RsiStatusService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;

class CheckRsiStatusJob implements ShouldQueue
{
    use Queueable;

    protected RsiStatusService $rsiStatusService;

    public function __construct(RsiStatusService $rsiStatusService)
    {
        $this->rsiStatusService = $rsiStatusService;
    }

    public function handle(): array
    {
        $cacheKey = config('killboard.rsi_status.cache_key');
        $ttl = config('killboard.rsi_status.ttl');

        return Cache::remember($cacheKey, $ttl, function () {
            return $this->rsiStatusService->getRsiStatus();
        });
    }
}
