<?php

use App\Http\Middleware\FilamentDiscordAuthenticate;

class TestableFilamentDiscordAuthenticate extends FilamentDiscordAuthenticate
{
    public function __construct() {}

    public function callRedirectTo($request): ?string
    {
        return $this->redirectTo($request);
    }
}

it('redirectTo returns Larascord login URL using route or prefix fallback', function () {
    $middleware = new TestableFilamentDiscordAuthenticate;

    $request = \Illuminate\Http\Request::create('/admin', 'GET');

    /** @var string|null $result */
    $result = $middleware->callRedirectTo($request);

    $prefix = (string) config('larascord.route_prefix', 'larascord');
    $fallback = url('/'.trim($prefix, '/').'/login');

    $routeUrl = null;
    try {
        $routeUrl = route('larascord.login');
    } catch (Throwable $e) {
        // ignore
    }

    if ($routeUrl !== null) {
        expect($result)->toBe($routeUrl);
    } else {
        expect($result)->toBe($fallback);
    }
});
