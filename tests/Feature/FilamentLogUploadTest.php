<?php

use App\Filament\Admin\Pages\UploadLog;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

it('uploads a .log file via the Filament page', function () {
    Storage::fake('local');

    $user = User::factory()->create();
    $this->actingAs($user);

    $file = UploadedFile::fake()->create('combat.log', 2, 'text/plain');

    Livewire::test(UploadLog::class)
        ->set('data.log_file', $file)
        ->call('upload')
        ->assertHasNoErrors();

    $files = Storage::disk('local')->allFiles('uploads/logs');

    expect($files)->toBeEmpty();
});

it('rejects non-log files', function () {
    Storage::fake('local');

    $user = User::factory()->create();
    $this->actingAs($user);

    $file = UploadedFile::fake()->create('image.png', 1, 'image/png');

    Livewire::test(UploadLog::class)
        ->set('data.log_file', $file)
        ->call('upload')
        ->assertHasErrors(['data.log_file']);
});
