<?php

use App\Models\User;

it('redirects unauthenticated users to Discord login when accessing Filament', function () {
    $response = $this->get('/service');

    $prefix = (string) config('larascord.route_prefix', 'larascord');
    $expected = url('/'.trim($prefix, '/').'/login');

    $location = $response->headers->get('Location');

    $callback = url('/'.trim($prefix, '/').'/callback');

    expect([$expected, $callback])->toContain($location);
});

it('allows authenticated users to access the Filament panel', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->followingRedirects()->get('/service');

    $response->assertSuccessful();
});
