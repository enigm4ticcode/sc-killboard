<?php

namespace App\Console\Commands;

use App\Models\Kill;
use App\Models\Manufacturer;
use App\Models\Weapon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BackfillWeaponManufacturers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weapons:backfill-manufacturers {--dry-run : Preview changes without applying them}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill existing weapons to include manufacturer in names and slugs, and clean duplicates';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('DRY RUN MODE - No changes will be saved');
            $this->newLine();
        }

        // Step 1: Clean duplicates first
        $this->info('Step 1: Cleaning duplicate weapons...');
        $duplicatesRemoved = $this->cleanDuplicates($dryRun);

        // Step 2: Update weapon names and slugs
        $this->newLine();
        $this->info('Step 2: Updating weapon names and slugs with manufacturers...');
        $updated = $this->updateWeaponNames($dryRun);

        // Final summary
        $this->newLine();
        $this->info('=== Final Summary ===');
        $this->table(
            ['Action', 'Count'],
            [
                ['Duplicate weapons merged', $duplicatesRemoved],
                ['Weapons updated with manufacturer', $updated],
            ]
        );

        if ($dryRun) {
            $this->newLine();
            $this->info('Run without --dry-run to apply changes.');
        } else {
            $this->newLine();
            $this->info('✅ Migration complete!');
        }

        return self::SUCCESS;
    }

    private function cleanDuplicates(bool $dryRun): int
    {
        // Find weapons with same slug and manufacturer_id
        $duplicates = DB::table('weapons')
            ->select('slug', 'manufacturer_id', DB::raw('COUNT(*) as count'), DB::raw('MIN(id) as keep_id'))
            ->whereNotNull('manufacturer_id')
            ->groupBy('slug', 'manufacturer_id')
            ->having('count', '>', 1)
            ->get();

        if ($duplicates->isEmpty()) {
            $this->line('  No duplicates found.');

            return 0;
        }

        $totalRemoved = 0;

        foreach ($duplicates as $duplicate) {
            // Get all weapon IDs for this slug+manufacturer combo
            $weaponIds = Weapon::query()
                ->where('slug', $duplicate->slug)
                ->where('manufacturer_id', $duplicate->manufacturer_id)
                ->pluck('id')
                ->toArray();

            // Keep the first one (lowest ID), remove others
            $keepId = $duplicate->keep_id;
            $removeIds = array_diff($weaponIds, [$keepId]);

            if (empty($removeIds)) {
                continue;
            }

            $manufacturer = Manufacturer::find($duplicate->manufacturer_id);
            $manufacturerName = $manufacturer ? $manufacturer->name : 'Unknown';

            if ($dryRun) {
                $this->line("  Would merge {$duplicate->count} duplicates of '{$duplicate->slug}' ({$manufacturerName})");
                $this->line("    Keep: weapon #{$keepId}");
                $this->line('    Remove: weapons #'.implode(', #', $removeIds));
            } else {
                // Update all kills using the duplicate weapons to use the primary weapon
                Kill::query()->whereIn('weapon_id', $removeIds)->update(['weapon_id' => $keepId]);

                // Delete the duplicate weapons
                Weapon::query()->whereIn('id', $removeIds)->delete();

                $this->line("  ✓ Merged {$duplicate->count} duplicates of '{$duplicate->slug}' ({$manufacturerName})");
            }

            $totalRemoved += count($removeIds);
        }

        return $totalRemoved;
    }

    private function updateWeaponNames(bool $dryRun): int
    {
        $weapons = Weapon::with('manufacturer')->get();

        if ($weapons->isEmpty()) {
            $this->warn('  No weapons found in database.');

            return 0;
        }

        $bar = $this->output->createProgressBar($weapons->count());
        $updated = 0;
        $skipped = 0;
        $noManufacturer = 0;

        foreach ($weapons as $weapon) {
            $bar->advance();

            // Skip if no manufacturer linked
            if (! $weapon->manufacturer) {
                $noManufacturer++;

                continue;
            }

            $manufacturerName = $weapon->manufacturer->name;
            $manufacturerCode = $weapon->manufacturer->code;
            $currentName = $weapon->name;
            $currentSlug = $weapon->slug;

            // Skip if weapon name already starts with manufacturer name
            if (str_starts_with($currentName, $manufacturerName.' ')) {
                $skipped++;

                continue;
            }

            // Create new name and slug with manufacturer prefix
            $newName = $manufacturerName.' '.$currentName;
            $newSlug = Str::slug(Str::lower($manufacturerCode).'-'.$currentSlug);

            if ($dryRun) {
                $this->newLine();
                $this->line('  Would update:');
                $this->line("    Name: <comment>{$currentName}</comment> → <info>{$newName}</info>");
                $this->line("    Slug: <comment>{$currentSlug}</comment> → <info>{$newSlug}</info>");
            } else {
                $weapon->update([
                    'name' => $newName,
                    'slug' => $newSlug,
                ]);
            }

            $updated++;
        }

        $bar->finish();
        $this->newLine();

        if ($noManufacturer > 0) {
            $this->newLine();
            $this->warn("  ⚠️  {$noManufacturer} weapons have no manufacturer linked.");
            $this->info("  Run 'sail artisan manufacturers:migrate-weapons' to link manufacturers first.");
        }

        return $updated;
    }
}
