<?php

namespace App\Console\Commands;

use App\Services\StarCitizenWikiService;
use App\Services\VehicleService;
use Illuminate\Console\Command;

class GetAllVehiclesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wiki:get-all-vehicles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get all vehicles from the Star Citizen Wiki.';

    public function __construct(
        protected StarCitizenWikiService $starCitizenWikiService,
        protected VehicleService $vehicleService,
    )
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info("Get all vehicles from Star Citizen Wiki...\r\n");
        $initialData = $this->starCitizenWikiService->getVehicles() ?? [];

        if (! empty($initialData)) {
            $data = $initialData['data'];
            $totalPages = (int) $initialData['meta']['last_page'];
            $totalVehicles = (int) $initialData['meta']['total'];

            if (! empty($data)) {
                $bar =$this->output->createProgressBar($totalVehicles);
                foreach ($data as $vehicle) {
                    $this->processShipData($vehicle);
                    $bar->advance();
                }

                for ($page = 2; $page <= $totalPages; $page++) {
                    $data = $this->starCitizenWikiService->getVehicles($page)['data'] ?? [];

                    if (! empty($data)) {
                        foreach ($data as $vehicle) {
                            $this->processShipData($vehicle);
                            $bar->advance();
                        }
                    }
                }

                $bar->finish();
                $this->info("Done.\r\n");
            }
        } else {
            $this->error('Unable to scrape Star Citizen Wiki.');
        }
    }

    private function processShipData(array $vehicleData): void
    {
        $id = $vehicleData['uuid'] ?? '';

        if (! empty($id)) {
            $wikiData = $this->starCitizenWikiService->getVehicleById($id)['data'] ?? [];

            if (! empty($wikiData)) {
                $this->vehicleService->updateOrCreateVehicle($wikiData);
            }
        }
    }
}
