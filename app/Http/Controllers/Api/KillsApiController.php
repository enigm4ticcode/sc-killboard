<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateKillApiRequest;
use App\Models\User;
use App\Services\GameLogService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class KillsApiController extends Controller
{
    protected GameLogService $gameLogService;

    public function __construct(GameLogService $gameLogService)
    {
        $this->gameLogService = $gameLogService;
    }

    public function create(CreateKillApiRequest $request): JsonResponse
    {
        $username = $request->input('username');
        $user = User::where('username', $username)->firstOrFail();

        $kill = $this->gameLogService->recordKill(
            $request->string('timestamp'),
            $request->string('kill_type'),
            $request->string('location'),
            $request->string('killer'),
            $request->string('victim'),
            $request->string('weapon'),
            $request->string('vehicle'),
            null,
            null,
            $user,
        );

        return response()->json([
            'success' => true,
            'data' => $kill,
        ])->setStatusCode(ResponseAlias::HTTP_CREATED);
    }
}
