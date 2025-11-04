<?php

namespace App\Console\Commands;

use App\Services\ManufacturerService;
use App\Services\StarCitizenWikiService;
use Illuminate\Console\Command;

class GetAllManufacturersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wiki:get-all-manufacturers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get all manufacturers from the Star Citizen Wiki.';

    public function __construct(
        protected StarCitizenWikiService $starCitizenWikiService,
        protected ManufacturerService $manufacturerService,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info("Get all manufacturers from Star Citizen Wiki...\r\n");
        $initialData = $this->starCitizenWikiService->getManufacturers() ?? [];

        if (empty($initialData)) {
            $this->error('Unable to fetch data from Star Citizen Wiki API.');

            return;
        }

        // Check for error response
        if (isset($initialData['code']) && $initialData['code'] >= 400) {
            $this->error('API Error: '.$initialData['message'] ?? 'Unknown error');

            return;
        }

        // Check for expected data structure
        if (! isset($initialData['data']) || ! isset($initialData['meta'])) {
            $this->error('Unexpected API response structure.');
            $this->line('Response: '.json_encode($initialData));

            return;
        }

        $data = $initialData['data'];
        $totalPages = (int) $initialData['meta']['last_page'];
        $totalManufacturers = (int) $initialData['meta']['total'];

        if (empty($data)) {
            $this->warn('No manufacturer data found.');

            return;
        }

        $bar = $this->output->createProgressBar($totalManufacturers);

        foreach ($data as $manufacturer) {
            $this->processManufacturerData($manufacturer);
            $bar->advance();
        }

        for ($page = 2; $page <= $totalPages; $page++) {
            $pageData = $this->starCitizenWikiService->getManufacturers($page);

            if (! isset($pageData['data']) || empty($pageData['data'])) {
                continue;
            }

            foreach ($pageData['data'] as $manufacturer) {
                $this->processManufacturerData($manufacturer);
                $bar->advance();
            }
        }

        $bar->finish();
        $this->info("\r\nDone.\r\n");
    }

    private function processManufacturerData(array $manufacturerData): void
    {
        // API v2 manufacturers list provides code and name directly
        // No need to fetch by ID
        $this->manufacturerService->updateOrCreateManufacturer($manufacturerData);
    }
}
