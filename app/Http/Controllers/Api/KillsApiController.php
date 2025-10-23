<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateKillApiRequest;
use App\Models\Kill;
use App\Models\User;
use App\Services\GameLogService;
use App\Services\VehicleService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class KillsApiController extends Controller
{
    protected GameLogService $gameLogService;

    protected VehicleService $vehicleService;

    public function __construct(GameLogService $gameLogService, VehicleService $vehicleService)
    {
        $this->vehicleService = $vehicleService;
        $this->gameLogService = $gameLogService;
    }

    public function create(CreateKillApiRequest $request): JsonResponse
    {
        $username = $request->input('username');
        $user = User::where('username', $username)->firstOrFail();
        $killType = $request->string('kill_type');
        $vehicleName = $request->string('vehicle');
        $vehicle = null;

        if ($killType == Kill::TYPE_VEHICLE) {
            $vehicle = $this->vehicleService->getVehicleByClass($vehicleName);

            if (! $vehicle) {
                return response()->json(['message' => 'Vehicle not found.'], ResponseAlias::HTTP_NOT_FOUND);
            }
        }

        $kill = $this->gameLogService->recordKill(
            $request->string('timestamp'),
            $request->string('kill_type'),
            $request->string('location'),
            $request->string('killer'),
            $request->string('victim'),
            $request->string('weapon'),
            $vehicle,
            null,
            null,
            $user,
        );

        return response()->json(['success' => true, 'data' => $kill], ResponseAlias::HTTP_CREATED);
    }
}
