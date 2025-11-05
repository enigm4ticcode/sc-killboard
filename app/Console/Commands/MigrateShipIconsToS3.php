<?php

namespace App\Console\Commands;

use App\Models\Ship;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateShipIconsToS3 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-ship-icons-to-s3 {--dry-run : Show what would be migrated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate ship icon files from local public disk to S3 storage';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Migrating ship icon files to S3...');

        // Check if we're actually using S3 in production
        $defaultDisk = config('filesystems.default');
        if ($defaultDisk === 'public' || $defaultDisk === 'local') {
            $this->warn("Default disk is '{$defaultDisk}' - this command is designed for S3 migration.");
            $this->warn('If you meant to migrate to S3, set FILESYSTEM_DISK=s3 in your .env file.');

            if (! $this->confirm('Continue anyway?', false)) {
                return Command::SUCCESS;
            }
        }

        // Get all ships with icons
        $ships = Ship::query()
            ->whereNotNull('icon')
            ->get();

        if ($ships->isEmpty()) {
            $this->info('No ships with icons found.');

            return Command::SUCCESS;
        }

        $this->info("Found {$ships->count()} ships with icons.");

        if ($this->option('dry-run')) {
            $this->warn('DRY RUN MODE - No files will be copied');
            $this->newLine();
        }

        $migrated = 0;
        $skipped = 0;
        $errors = 0;

        $progressBar = $this->output->createProgressBar($ships->count());

        foreach ($ships as $ship) {
            $iconPath = $ship->icon;

            // Strip old storage/ prefix if present
            $cleanPath = str_starts_with($iconPath, 'storage/')
                ? substr($iconPath, 8)
                : $iconPath;

            try {
                // Check if file exists in public disk
                if (! Storage::disk('public')->exists($cleanPath)) {
                    if ($this->option('dry-run') && $this->output->isVerbose()) {
                        $this->newLine();
                        $this->warn("Not found in public disk: {$cleanPath}");
                    }
                    $skipped++;
                    $progressBar->advance();

                    continue;
                }

                // Check if already exists in default disk (S3)
                if (Storage::exists($cleanPath)) {
                    if ($this->option('dry-run') && $this->output->isVerbose()) {
                        $this->newLine();
                        $this->line("Already exists in S3: {$cleanPath}");
                    }
                    $skipped++;
                    $progressBar->advance();

                    continue;
                }

                if ($this->option('dry-run')) {
                    if ($this->output->isVerbose()) {
                        $this->newLine();
                        $this->line("Would copy: {$cleanPath}");
                    }
                } else {
                    // Copy file from public disk to default disk (S3)
                    $fileContents = Storage::disk('public')->get($cleanPath);
                    Storage::put($cleanPath, $fileContents);
                }

                $migrated++;
            } catch (\Exception $e) {
                $errors++;
                if (! $this->option('dry-run')) {
                    $this->newLine();
                    $this->error("Failed to migrate {$cleanPath}: ".$e->getMessage());
                }
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Summary
        if ($this->option('dry-run')) {
            $this->info("Would migrate {$migrated} files to S3");
            $this->info("Would skip {$skipped} files (already exist or not found)");
        } else {
            $this->info("✓ Successfully migrated {$migrated} files to S3");
            $this->info("Skipped {$skipped} files (already exist or not found)");

            if ($errors > 0) {
                $this->error("Failed to migrate {$errors} files");
            }
        }

        return Command::SUCCESS;
    }
}
