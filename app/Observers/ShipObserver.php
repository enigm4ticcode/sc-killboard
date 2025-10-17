<?php

namespace App\Observers;

use App\Models\Ship;
use App\Services\VehicleService;

class ShipObserver
{
    protected VehicleService $vehicleService;

    public function __construct(VehicleService $vehicleService)
    {
        $this->vehicleService = $vehicleService;
    }

    /**
     * Handle the Ship "created" event.
     */
    public function created(Ship $ship): void
    {
        $this->refreshCache();
    }

    /**
     * Handle the Ship "updated" event.
     */
    public function updated(Ship $ship): void
    {
        $this->refreshCache();
    }

    /**
     * Handle the Ship "deleted" event.
     */
    public function deleted(Ship $ship): void
    {
        $this->refreshCache();
    }

    /**
     * Handle the Ship "restored" event.
     */
    public function restored(Ship $ship): void
    {
        $this->refreshCache();
    }

    /**
     * Handle the Ship "force deleted" event.
     */
    public function forceDeleted(Ship $ship): void
    {
        $this->refreshCache();
    }

    private function refreshCache(): void
    {
        $this->vehicleService->getAllVehicles();
    }
}
