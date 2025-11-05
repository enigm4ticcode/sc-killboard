<?php

namespace App\Console\Commands;

use App\Models\Ship;
use Illuminate\Console\Command;

class MigrateShipIconPaths extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-ship-icon-paths {--dry-run : Show what would be updated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate ship icon paths from old format (storage/ships/...) to new format (ships/...)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Migrating ship icon paths...');

        // Find all ships with the old storage/ prefix
        $ships = Ship::query()
            ->whereNotNull('icon')
            ->where('icon', 'like', 'storage/%')
            ->get();

        if ($ships->isEmpty()) {
            $this->info('No ships found with old icon paths.');

            return Command::SUCCESS;
        }

        $this->info("Found {$ships->count()} ships with old icon paths.");

        if ($this->option('dry-run')) {
            $this->warn('DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        $updated = 0;

        foreach ($ships as $ship) {
            $oldPath = $ship->icon;
            $newPath = str_starts_with($oldPath, 'storage/')
                ? substr($oldPath, 8) // Remove 'storage/' prefix
                : $oldPath;

            if ($this->option('dry-run')) {
                $this->line("Would update {$ship->name}: {$oldPath} → {$newPath}");
            } else {
                $ship->update(['icon' => $newPath]);
                $updated++;
            }
        }

        $this->newLine();

        if ($this->option('dry-run')) {
            $this->info("Would update {$ships->count()} ship icon paths.");
        } else {
            $this->info("✓ Successfully updated {$updated} ship icon paths.");
        }

        return Command::SUCCESS;
    }
}
