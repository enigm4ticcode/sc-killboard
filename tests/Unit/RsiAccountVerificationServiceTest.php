<?php

use App\Services\RsiAccountVerificationService;
use Illuminate\Support\Facades\Http;

it('verifies biography key when present', function (): void {
    $html = <<<HTML
    <div class="entry bio"><div class="value">Hello [sc-killboard: abc123]</div></div>
    HTML;

    Http::fake([
        'https://robertsspaceindustries.com/citizens/john' => Http::response($html, 200),
    ]);

    $service = new RsiAccountVerificationService([
        'base_url' => 'https://robertsspaceindustries.com/citizens',
        'pattern' => '/\[sc-killboard:\s*([a-zA-Z0-9]+)\]/',
    ]);

    expect($service->verifyBiographyKey('john', 'abc123'))->toBeTrue();
});

it('returns false when biography key missing', function (): void {
    $html = '<div class="entry bio"><div class="value">Nothing here</div></div>';

    Http::fake([
        'https://robertsspaceindustries.com/citizens/jane' => Http::response($html, 200),
    ]);

    $service = new RsiAccountVerificationService([
        'base_url' => 'https://robertsspaceindustries.com/citizens',
        'pattern' => '/\[sc-killboard:\s*([a-zA-Z0-9]+)\]/',
    ]);

    expect($service->verifyBiographyKey('jane', 'abc123'))->toBeFalse();
});

it('returns false and logs when HTTP throws', function (): void {
    Http::fake([
        'https://robertsspaceindustries.com/citizens/bob' => function () {
            throw new Exception('network');
        },
    ]);

    $service = new RsiAccountVerificationService([
        'base_url' => 'https://robertsspaceindustries.com/citizens',
        'pattern' => '/\[sc-killboard:\s*([a-zA-Z0-9]+)\]/',
    ]);

    expect($service->verifyBiographyKey('bob', 'abc123'))->toBeFalse();
});

it('returns false when DOM cannot be parsed', function (): void {
    // Missing expected elements causes empty bio text
    $html = '<div class="no-bio"></div>';

    Http::fake([
        'https://robertsspaceindustries.com/citizens/kelly' => Http::response($html, 200),
    ]);

    $service = new RsiAccountVerificationService([
        'base_url' => 'https://robertsspaceindustries.com/citizens',
        'pattern' => '/\[sc-killboard:\s*([a-zA-Z0-9]+)\]/',
    ]);

    expect($service->verifyBiographyKey('kelly', 'abc123'))->toBeFalse();
});
