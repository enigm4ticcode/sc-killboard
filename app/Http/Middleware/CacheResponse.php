<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CacheResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, int $ttl = 300): Response
    {
        // Only cache GET requests
        if (! $request->isMethod('GET')) {
            return $next($request);
        }

        // Don't cache requests with query parameters (except 'page' for pagination)
        $queryParams = $request->query();
        unset($queryParams['page']);

        if (! empty($queryParams)) {
            return $next($request);
        }

        // Generate cache key from request
        $cacheKey = 'response:'.md5($request->fullUrl());

        // Return cached response if available
        if (Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);

            return response($cached['content'])
                ->withHeaders(array_merge($cached['headers'], [
                    'X-Cache-Hit' => 'true',
                ]));
        }

        // Process request
        $response = $next($request);

        // Only cache successful responses
        if ($response->isSuccessful()) {
            Cache::put($cacheKey, [
                'content' => $response->getContent(),
                'headers' => $response->headers->all(),
            ], now()->addSeconds($ttl));
        }

        return $response->withHeaders(['X-Cache-Hit' => 'false']);
    }
}
