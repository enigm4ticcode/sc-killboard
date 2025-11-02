<?php

use App\Livewire\RsiVerification;
use App\Livewire\Service\UploadLog;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('redirects guests to login for service pages', function (): void {
    get(route('service.verify'))->assertRedirect('/login');
    get(route('service.upload-log'))->assertRedirect('/login');
});

it('allows verified users to access upload-log and redirects from verification', function (): void {
    $user = User::factory()->create([
        'rsi_verified' => true,
    ]);

    actingAs($user);

    get(route('service.upload-log'))
        ->assertOk()
        ->assertSeeLivewire(UploadLog::class);

    get(route('service.verify'))
        ->assertRedirect('/');
});

it('forces not-verified users to verification and blocks upload-log', function (): void {
    $user = User::factory()->create([
        'rsi_verified' => false,
        'global_name' => 'TestUser',
        'discriminator' => '1234',
    ]);

    actingAs($user);

    get(route('service.verify'))
        ->assertOk()
        ->assertSeeLivewire(RsiVerification::class);

    get(route('service.upload-log'))
        ->assertRedirect('/');
});
