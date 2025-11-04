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

    public function index(): JsonResponse
    {
        // Paginate kills with eager loading to prevent N+1 queries
        $kills = Kill::query()
            ->with([
                'killer:id,name,avatar',
                'victim:id,name,avatar',
                'weapon:id,name,slug',
                'weapon.manufacturer:id,name,code',
                'ship:id,name,slug',
            ])
            ->orderByDesc('destroyed_at')
            ->paginate(50); // 50 kills per page

        return response()->json([
            'success' => true,
            'data' => $kills->items(),
            'meta' => [
                'current_page' => $kills->currentPage(),
                'last_page' => $kills->lastPage(),
                'per_page' => $kills->perPage(),
                'total' => $kills->total(),
            ],
            'links' => [
                'first' => $kills->url(1),
                'last' => $kills->url($kills->lastPage()),
                'prev' => $kills->previousPageUrl(),
                'next' => $kills->nextPageUrl(),
            ],
        ]);
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
