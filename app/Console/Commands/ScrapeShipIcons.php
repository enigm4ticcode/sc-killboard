<?php

namespace App\Console\Commands;

use App\Models\Ship;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ScrapeShipIcons extends Command
{
    protected $signature = 'app:scrape-ship-icons {--force : Force update all icons even if already set} {--limit= : Limit number of ships to process for testing}';

    protected $description = 'Scrape and download ship icons from starcitizen.tools to local storage';

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
                $result = $this->fetchAndSaveShipIcon($ship);

                if ($result) {
                    $ship->update(['icon' => $result]);
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

    /**
     * Fetch ship icon from wiki and save it to local storage
     */
    private function fetchAndSaveShipIcon(Ship $ship): ?string
    {
        // Try different variations of the ship name on the wiki
        $variations = $this->generateNameVariations($ship->name);

        foreach ($variations as $name) {
            $wikiUrl = 'https://starcitizen.tools/'.urlencode(str_replace(' ', '_', $name));

            try {
                $response = Http::timeout(10)->get($wikiUrl);

                if (! $response->successful()) {
                    continue;
                }

                $html = $response->body();

                // Check if this is a true disambiguation page - skip if so
                if ($this->isTrueDisambiguationPage($html)) {
                    continue;
                }

                $iconUrl = $this->extractBestShipImage($html, $name);

                // If we found an icon URL, download and save it
                if ($iconUrl) {
                    return $this->downloadAndSaveImage($iconUrl, $ship);
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return null;
    }

    /**
     * Extract the best ship image from the HTML
     */
    private function extractBestShipImage(string $html, string $shipName): ?string
    {
        // First try to find infobox image
        if (preg_match('/<img[^>]*class="[^"]*infobox-image[^"]*"[^>]*src="([^"]*)"/', $html, $match)) {
            $iconUrl = $match[1];
            if (! str_starts_with($iconUrl, 'http')) {
                $iconUrl = 'https://starcitizen.tools'.$iconUrl;
            }

            if ($this->isValidShipImage($iconUrl)) {
                return $this->convertToFullSizeImage($iconUrl);
            }
        }

        // Find all images from media.starcitizen.tools
        // Match both img src and picture/source srcset
        preg_match_all('/<img[^>]*src="(https?:\/\/media\.starcitizen\.tools\/[^"]*)"/', $html, $imgMatches);
        preg_match_all('/<source[^>]*srcset="(https?:\/\/media\.starcitizen\.tools\/[^,\s"]*)"/', $html, $sourceMatches);

        // Combine all matches
        $matches = [1 => array_merge($imgMatches[1] ?? [], $sourceMatches[1] ?? [])];

        if (! empty($matches[1])) {
            $shipNameSlug = str_replace(' ', '_', $shipName);

            // Score images by relevance
            $scoredImages = [];
            foreach ($matches[1] as $imageUrl) {
                if (! $this->isValidShipImage($imageUrl)) {
                    continue;
                }

                $score = 0;

                // Prefer images with ship name in filename
                $urlLower = strtolower($imageUrl);
                $shipNameLower = strtolower($shipNameSlug);

                if (str_contains($urlLower, $shipNameLower)) {
                    $score += 100;
                }

                // Prefer isometric views
                if (str_contains($urlLower, 'isometric')) {
                    $score += 50;
                }

                // Prefer "in space" images
                if (str_contains($urlLower, 'in_space') || str_contains($urlLower, 'flying')) {
                    $score += 30;
                }

                // Penalize thumbnails (we'll convert them anyway)
                if (str_contains($urlLower, 'thumb')) {
                    $score -= 5;
                }

                if ($score > 0) {
                    $scoredImages[$imageUrl] = $score;
                }
            }

            if (! empty($scoredImages)) {
                // Get highest scored image
                arsort($scoredImages);
                $bestImage = array_key_first($scoredImages);

                return $this->convertToFullSizeImage($bestImage);
            }
        }

        return null;
    }

    /**
     * Check if an image URL is valid for a ship (not UI icons, SVGs, etc.)
     */
    private function isValidShipImage(string $imageUrl): bool
    {
        $urlLower = strtolower($imageUrl);

        // Reject SVG files
        if (str_ends_with($urlLower, '.svg')) {
            return false;
        }

        // Reject UI icons and wiki interface images
        $invalidPatterns = [
            'wikimedia',
            'ui-',
            'icon-',
            'articledisambiguation',
            'search.svg',
            'globe.svg',
            'error.svg',
            'logo',
            'button',
        ];

        foreach ($invalidPatterns as $pattern) {
            if (str_contains($urlLower, $pattern)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Convert thumbnail URL to full-size image URL
     */
    private function convertToFullSizeImage(string $imageUrl): string
    {
        // If it's a thumbnail, convert to full size
        // Format: https://media.starcitizen.tools/thumb/a/bc/Ship.jpg/400px-Ship.jpg.webp
        // To: https://media.starcitizen.tools/a/bc/Ship.jpg
        if (str_contains($imageUrl, '/thumb/')) {
            $parts = explode('/thumb/', $imageUrl);
            if (count($parts) === 2) {
                $pathParts = explode('/', $parts[1]);
                // Remove the last part (the sized version) and reconstruct
                array_pop($pathParts);
                $fullPath = implode('/', $pathParts);

                return 'https://media.starcitizen.tools/'.$fullPath;
            }
        }

        return $imageUrl;
    }

    /**
     * Check if the page is a true disambiguation page (not just a page with hatnotes)
     */
    private function isTrueDisambiguationPage(string $html): bool
    {
        // Check for actual disambiguation page class or ID
        return preg_match('/<div[^>]*id="disambig[^"]*"/', $html) ||
               preg_match('/<div[^>]*class="[^"]*disambiguation[^"]*"/', $html) ||
               preg_match('/<title>[^<]*\(disambiguation\)/', $html);
    }

    /**
     * Download image and save to storage
     */
    private function downloadAndSaveImage(string $imageUrl, Ship $ship): ?string
    {
        try {
            // Download the image
            $imageResponse = Http::timeout(30)->get($imageUrl);

            if (! $imageResponse->successful()) {
                return null;
            }

            // Get file extension from URL or content type
            $extension = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
            if (! $extension || ! in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                $contentType = $imageResponse->header('Content-Type');
                $extension = match ($contentType) {
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/webp' => 'webp',
                    'image/gif' => 'gif',
                    default => 'jpg',
                };
            }

            // Generate filename based on ship slug
            $filename = 'ships/'.Str::slug($ship->name).'.'.$extension;

            // Save to the default configured disk (public locally, S3 in production)
            Storage::put($filename, $imageResponse->body());

            // Return just the filename - Storage::url() will handle the full path
            return $filename;
        } catch (\Exception $e) {
            $this->error("Failed to download image for {$ship->name}: ".$e->getMessage());

            return null;
        }
    }

    private function generateNameVariations(string $shipName): array
    {
        $variations = [$shipName];

        // Remove manufacturer prefix (try this as second option)
        $withoutManufacturer = preg_replace('/^(Aegis|Anvil|Origin|Drake|MISC|Crusader|RSI|Consolidated Outland|Esperia|Banu|Vanduul|Argo|Tumbril|Greycat|Aopoa|C\.O\.|ARGO)\s+/i', '', $shipName);
        if ($withoutManufacturer !== $shipName) {
            $variations[] = $withoutManufacturer;
        }

        // Try with underscores instead of spaces (some wiki pages use this)
        $withUnderscores = str_replace(' ', '_', $shipName);
        if ($withUnderscores !== $shipName) {
            $variations[] = $withUnderscores;
        }

        // Only remove "Edition" suffix, but keep variant names like Pirate, Carbon, Talus, MT, etc.
        // These variants have their own wiki pages
        $withoutEditionSuffix = preg_replace('/\s+Edition$/i', '', $shipName);
        if ($withoutEditionSuffix !== $shipName) {
            $variations[] = $withoutEditionSuffix;
        }

        // Also try without manufacturer AND without Edition
        if ($withoutManufacturer !== $shipName) {
            $withoutBoth = preg_replace('/\s+Edition$/i', '', $withoutManufacturer);
            if ($withoutBoth !== $withoutManufacturer) {
                $variations[] = $withoutBoth;
            }
        }

        // Try uppercasing specific ship models (e.g., "Mole" -> "MOLE")
        // This handles ships like "Argo Mole Carbon" -> "MOLE Carbon"
        $upperCaseModels = ['Mole'];
        foreach ($upperCaseModels as $model) {
            if (stripos($shipName, $model) !== false) {
                $upperCased = str_ireplace($model, strtoupper($model), $shipName);
                $variations[] = $upperCased;

                // Also try without manufacturer
                $upperCasedNoManufacturer = preg_replace('/^(Aegis|Anvil|Origin|Drake|MISC|Crusader|RSI|Consolidated Outland|Esperia|Banu|Vanduul|Argo|Tumbril|Greycat|Aopoa|C\.O\.|ARGO)\s+/i', '', $upperCased);
                if ($upperCasedNoManufacturer !== $upperCased) {
                    $variations[] = $upperCasedNoManufacturer;
                }

                // And without Edition suffix
                $upperCasedNoEdition = preg_replace('/\s+Edition$/i', '', $upperCasedNoManufacturer);
                if ($upperCasedNoEdition !== $upperCasedNoManufacturer) {
                    $variations[] = $upperCasedNoEdition;
                }
            }
        }

        // Special case for Mercury - try "Mercury Star Runner"
        if (stripos($shipName, 'Mercury') !== false && ! stripos($shipName, 'Star Runner')) {
            $variations[] = 'Mercury Star Runner';
        }

        return array_unique($variations);
    }
}
