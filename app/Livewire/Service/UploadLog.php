<?php

namespace App\Livewire\Service;

use App\Services\GameLogService;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Masmerise\Toaster\Toaster;
use Spatie\LivewireFilepond\WithFilePond;

class UploadLog extends Component
{
    use WithFilePond;

    #[Validate('required|file|mimetypes:text/plain|extensions:log')]
    public $file;

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimetypes:text/plain|extensions:log',
        ];
    }

    public function validateUploadedFile(): bool
    {
        $this->validate();

        return true;
    }

    #[On('refreshSelf')]
    public function resetSelf(): void
    {
        $this->reset('file');
    }

    public function render(): View
    {
        return view('livewire.upload-log');
    }

    public function save(GameLogService $gameLogService): void
    {
        $this->validate();
        $disk = config('filesystems.default', 'local');
        $original = $this->file->getClientOriginalName();
        $safe = preg_replace('/[^A-Za-z0-9._-]/', '_', $original);
        $name = uniqid('log_', true).'-'.$safe;
        $storedPath = $this->file->storeAs('uploads/logs', $name, $disk);

        if (! $storedPath || ! Storage::disk($disk)->exists($storedPath)) {
            $this->reset('file');
        }

        if (! $storedPath || ! Storage::disk($disk)->exists($storedPath)) {
            Toaster::error('Upload failed.');
            $this->dispatch('refreshSelf');

            return;
        }

        $result = $gameLogService->processGameLog($storedPath);

        if ($result['has_arena_commander_kills']) {
            Toaster::error('Arena Commander log detected. Kills were not recorded.');

            return;
        }

        $totalKills = $result['total_kills'];
        if ($totalKills > 0) {
            Toaster::success("Log file processed successfully. Processed $totalKills Kills.");
            $this->dispatch('killboard-updated');
        } else {
            Toaster::info('Log file was processed successfully, however, no kills were found in it.');
        }

        Storage::disk($disk)->delete($storedPath);
        $this->dispatch('refreshSelf');
    }
}
