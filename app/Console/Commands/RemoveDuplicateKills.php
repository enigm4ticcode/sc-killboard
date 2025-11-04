<?php

namespace App\Console\Commands;

use App\Models\Kill;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RemoveDuplicateKills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kills:remove-duplicates {--dry-run : Show duplicates without removing them} {--force : Skip confirmation prompt} {--tolerance=3 : Time tolerance in seconds for duplicate detection}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find and remove duplicate kills within a time window (default ±3 seconds)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $tolerance = (int) $this->option('tolerance');

        $this->info("Searching for duplicate kills (±{$tolerance} second tolerance)...");
        $this->newLine();

        // Get all kills ordered by timestamp
        $allKills = Kill::query()
            ->with(['killer', 'victim', 'weapon', 'ship'])
            ->orderBy('destroyed_at')
            ->orderBy('id')
            ->get();

        if ($allKills->isEmpty()) {
            $this->info('✅ No kills found in database.');

            return self::SUCCESS;
        }

        $this->info("Analyzing {$allKills->count()} kills...");

        $duplicateGroups = [];
        $processedIds = [];

        foreach ($allKills as $kill) {
            // Skip if already processed as part of a duplicate group
            if (isset($processedIds[$kill->id])) {
                continue;
            }

            // Find potential duplicates within the time window
            $duplicates = $this->findDuplicatesForKill($kill, $allKills, $tolerance);

            if (count($duplicates) > 1) {
                // Mark all as processed
                foreach ($duplicates as $dup) {
                    $processedIds[$dup->id] = true;
                }

                $duplicateGroups[] = [
                    'keep' => $duplicates[0], // Keep the oldest (first in array)
                    'remove' => array_slice($duplicates, 1), // Remove the rest
                ];
            }
        }

        if (empty($duplicateGroups)) {
            $this->info('✅ No duplicate kills found!');

            return self::SUCCESS;
        }

        $totalToRemove = array_sum(array_map(fn ($group) => count($group['remove']), $duplicateGroups));

        $this->warn("Found {$totalToRemove} duplicate kills in ".count($duplicateGroups).' groups');
        $this->newLine();

        // Show details of duplicates
        $tableData = [];
        foreach ($duplicateGroups as $index => $group) {
            $keep = $group['keep'];
            $removeIds = array_map(fn ($k) => $k->id, $group['remove']);

            $tableData[] = [
                $index + 1,
                count($group['remove']) + 1,
                $keep->destroyed_at,
                $keep->type,
                "{$keep->killer->name} → {$keep->victim->name}",
                $keep->id,
                implode(', ', $removeIds),
            ];
        }

        $this->table(
            ['Group', 'Count', 'Timestamp', 'Type', 'Kill', 'Keep ID', 'Remove IDs'],
            $tableData
        );

        if ($isDryRun) {
            $this->newLine();
            $this->info('🔍 DRY RUN - No kills were removed.');
            $this->info("Run without --dry-run to remove {$totalToRemove} duplicate kills.");

            return self::SUCCESS;
        }

        // Confirm before deletion (unless --force is used)
        if (! $this->option('force') && ! $this->confirm("Are you sure you want to remove {$totalToRemove} duplicate kills?", false)) {
            $this->info('Operation cancelled.');

            return self::SUCCESS;
        }

        // Remove duplicates
        $removed = 0;
        $bar = $this->output->createProgressBar(count($duplicateGroups));
        $bar->start();

        foreach ($duplicateGroups as $group) {
            $removeIds = array_map(fn ($k) => $k->id, $group['remove']);
            $deleted = Kill::query()->whereIn('id', $removeIds)->delete();
            $removed += $deleted;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Successfully removed {$removed} duplicate kills!");

        // Refresh caches
        $this->info('Refreshing caches...');
        app(\App\Services\RecentKillsService::class)->refreshCache();
        app(\App\Services\LeaderboardService::class)->refreshLeaderboards();

        $this->info('✅ Caches refreshed!');

        return self::SUCCESS;
    }

    /**
     * Find all kills that are duplicates of the given kill within the time tolerance
     */
    private function findDuplicatesForKill(Kill $kill, $allKills, int $tolerance): array
    {
        $killTime = Carbon::parse($kill->destroyed_at);
        $startTime = $killTime->copy()->subSeconds($tolerance);
        $endTime = $killTime->copy()->addSeconds($tolerance);

        $duplicates = [];

        foreach ($allKills as $otherKill) {
            $otherTime = Carbon::parse($otherKill->destroyed_at);

            // Check if within time window
            if ($otherTime->lt($startTime) || $otherTime->gt($endTime)) {
                continue;
            }

            // Check if core identifying attributes match
            // Note: We DON'T check weapon_id because the same kill event can be logged with different weapons
            // Note: We properly handle null ship_id by checking both null and non-null cases
            $shipMatches = ($kill->ship_id === null && $otherKill->ship_id === null) ||
                           ($kill->ship_id !== null && $kill->ship_id === $otherKill->ship_id);

            if ($kill->killer_id === $otherKill->killer_id &&
                $kill->victim_id === $otherKill->victim_id &&
                $kill->type === $otherKill->type &&
                $kill->location === $otherKill->location &&
                $shipMatches) {
                $duplicates[] = $otherKill;
            }
        }

        return $duplicates;
    }
}
