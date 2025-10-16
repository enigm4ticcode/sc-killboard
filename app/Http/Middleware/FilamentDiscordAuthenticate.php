<?php

namespace App\Http\Middleware;

use Filament\Http\Middleware\Authenticate as FilamentAuthenticate;

class FilamentDiscordAuthenticate extends FilamentAuthenticate
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request): ?string
    {
        // Prefer Larascord login route if available, otherwise fallback to prefix-based URL
        try {
            return route('larascord.login');
        } catch (\Throwable $e) {
            $prefix = (string) config('larascord.route_prefix', 'larascord');

            return url('/'.trim($prefix, '/').'/login');
        }
    }
}
