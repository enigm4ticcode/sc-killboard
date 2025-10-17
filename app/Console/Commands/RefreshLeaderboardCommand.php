<?php

namespace App\Console\Commands;

use App\Services\LeaderboardService;
use Illuminate\Console\Command;

class RefreshLeaderboardCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboard:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refreshes leaderboards.';

    public function __construct(protected LeaderboardService $leaderboardService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Refreshing leaderboards...');
        $this->leaderboardService->refreshLeaderboards();
    }
}
