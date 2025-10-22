<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RsiNotVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user()->rsi_verified) {
            return redirect('/');
        }

        return $next($request);
    }
}
