<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('Authorization');
        $bearer = explode(' ', $header)[1] ?? null;
        $name = $request->input('username');

        if ($bearer && $name) {
            $user = User::query()->where('username', $name)->first();

            if ($user && $user->api_key === $bearer) {
                return $next($request);
            }
        }

        abort(Response::HTTP_UNAUTHORIZED);
    }
}
