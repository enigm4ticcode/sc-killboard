<?php

use App\Livewire\ApiDocumentation;
use App\Livewire\HomePage;
use App\Livewire\HowTo;
use App\Livewire\Legal;

use function Pest\Laravel\get;

it('renders the home page with the HomePage Livewire component', function (): void {
    get('/')->assertOk()->assertSeeLivewire(HomePage::class);
});

it('renders the How It Works page', function (): void {
    get(route('how-it-works'))->assertOk()->assertSeeLivewire(HowTo::class);
});

it('renders the API documentation page', function (): void {
    get(route('api-documentation'))->assertOk()->assertSeeLivewire(ApiDocumentation::class);
});

it('renders the Legal page', function (): void {
    get(route('legal'))->assertOk()->assertSeeLivewire(Legal::class);
});

it('returns 404 for a non-existent player page', function (): void {
    get(route('player.show', ['name' => 'this-player-should-not-exist']))->assertNotFound();
});
