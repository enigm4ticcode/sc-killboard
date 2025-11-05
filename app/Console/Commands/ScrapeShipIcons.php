<?php

namespace App\Console\Commands;

use App\Models\Ship;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ScrapeShipIcons extends Command
{
    protected $signature = 'app:scrape-ship-icons {--force : Force update all icons even if already set} {--limit= : Limit number of ships to process for testing}';

    protected $description = 'Scrape and update ship icons from starcitizen.tools';

    public function handle(): int
    {
        $this->info('Fetching ship icons from starcitizen.tools...');

        try {
            // Get all ships from database
            $query = Ship::query();
            if (! $this->option('force')) {
                $query->whereNull('icon');
            }

            if ($limit = $this->option('limit')) {
                $query->limit((int) $limit);
            }

            $ships = $query->get();
            $this->info('Processing '.$ships->count().' ships...');

            $updated = 0;
            $notFound = 0;
            $progressBar = $this->output->createProgressBar($ships->count());

            foreach ($ships as $ship) {
                $iconUrl = $this->fetchShipIcon($ship->name);

                if ($iconUrl) {
                    $ship->update(['icon' => $iconUrl]);
                    $updated++;
                } else {
                    $notFound++;
                }

                $progressBar->advance();

                // Add delay to avoid overwhelming the server
                usleep(100000); // 100ms delay
            }

            $progressBar->finish();
            $this->newLine(2);

            $this->info("✓ Updated {$updated} ship icons");
            if ($notFound > 0) {
                $this->warn("⚠ {$notFound} ships without matching icons");
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error: '.$e->getMessage());

            return Command::FAILURE;
        }
    }

    private function fetchShipIcon(string $shipName): ?string
    {
        // Try different variations of the ship name on the wiki
        $variations = $this->generateNameVariations($shipName);

        foreach ($variations as $name) {
            $wikiUrl = 'https://starcitizen.tools/'.urlencode(str_replace(' ', '_', $name));

            try {
                $response = Http::timeout(10)->get($wikiUrl);

                if (! $response->successful()) {
                    continue;
                }

                $html = $response->body();

                // Look for the infobox image
                if (preg_match('/<img[^>]*class="[^"]*infobox-image[^"]*"[^>]*src="([^"]*)"/', $html, $match)) {
                    $iconUrl = $match[1];
                    if (! str_starts_with($iconUrl, 'http')) {
                        $iconUrl = 'https://starcitizen.tools'.$iconUrl;
                    }

                    return $iconUrl;
                }

                // Alternative: look for main content image
                if (preg_match('/<img[^>]*src="(https?:\/\/media\.starcitizen\.tools\/[^"]*)"/', $html, $match)) {
                    return $match[1];
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return null;
    }

    private function generateNameVariations(string $shipName): array
    {
        $variations = [$shipName];

        // Remove manufacturer prefix
        $withoutManufacturer = preg_replace('/^(Aegis|Anvil|Origin|Drake|MISC|Crusader|RSI|Consolidated Outland|Esperia|Banu|Vanduul|Argo|Tumbril|Greycat|Aopoa|C\.O\.|ARGO)\s+/i', '', $shipName);
        if ($withoutManufacturer !== $shipName) {
            $variations[] = $withoutManufacturer;
        }

        // Remove edition suffixes
        $withoutEdition = preg_replace('/\s+(Edition|Pirate Edition|Renegade|Valiant|Dunlevy|Snowblind|Dunestalker)$/i', '', $shipName);
        if ($withoutEdition !== $shipName) {
            $variations[] = $withoutEdition;
        }

        return array_unique($variations);
    }
}
