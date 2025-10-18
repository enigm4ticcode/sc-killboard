<?php

namespace App\Livewire\Service;

use App\Services\GameLogService;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
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
            $this->reset('file');

            return;
        }

        $totalKills = $gameLogService->processGameLog($storedPath);

        if ($totalKills > 0) {
            Toaster::success("Log file uploaded successfully. Processed $totalKills Kills.");
            $this->dispatch('killboard-updated');
        } else {
            Toaster::info("Log file uploaded successfully. Processed $totalKills Kills.");
        }

        Storage::disk($disk)->delete($storedPath);
        $this->reset('file');
    }
}
