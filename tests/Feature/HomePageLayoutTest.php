<?php

use function Pest\Laravel\get;

it('renders the homepage as full page', function () {
    $response = get('/');

    $response->assertSuccessful();
    $response->assertSee('data-home-full="1"', false);
});
