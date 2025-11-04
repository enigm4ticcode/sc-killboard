<?php

namespace App\Jobs;

use App\Models\Player;
use App\Services\GameLogService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdatePlayerMetadataJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Player $player,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(GameLogService $gameLogService): void
    {
        // Call the public method directly
        $gameLogService->updatePlayerMetadata($this->player);
    }
}
