<?php

it('renders opaque navigation and footer (surface) instead of glass', function () {
    $response = $this->get('/');

    $response->assertSuccessful();

    // Should include the opaque surface class
    $response->assertSee('class="surface', false);

    // Should not include the translucent glass class on layout containers
    $response->assertDontSee('class="glass', false);
});
