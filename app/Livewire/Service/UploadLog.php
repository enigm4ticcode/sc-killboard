<?php

namespace App\Livewire\Service;

use App\Models\LogUpload;
use App\Services\GameLogService;
use Illuminate\Support\Facades\Auth;
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

    #[Validate('required|file|mimetypes:text/plain|extensions:log|max:100000')]
    public $file;

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimetypes:text/plain|extensions:log|max:100000',
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
        $original = $this->file->getClientOriginalName();
        $safe = preg_replace('/[^A-Za-z0-9._-]/', '_', $original);
        $name = uniqid('log_', true).'-'.$safe;
        $storedPath = $this->file->storeAs('uploads/logs', $name);

        if (! $storedPath || ! Storage::exists($storedPath)) {
            Toaster::error(__('app.upload_failed'));
            $this->reset('file');
            $this->dispatch('refreshSelf');

            return;
        }

        $logUpload = new LogUpload([
            'filename' => $safe,
            'path' => $storedPath,
        ]);

        $logUpload->user()->associate(Auth::user());
        $logUpload->save();

        $result = $gameLogService->processGameLog($storedPath, $logUpload);
        Storage::delete($storedPath);

        if ($result['has_arena_commander_kills']) {
            Toaster::error(__('app.arena_commander_detected'));

            return;
        }

        $totalKills = $result['total_kills'];
        if ($totalKills > 0) {
            $message = $totalKills === 1
                ? __('app.log_processed_success_single', ['count' => $totalKills])
                : __('app.log_processed_success', ['count' => $totalKills]);
            Toaster::success($message);
            $this->dispatch('killboard-updated');
        } else {
            Toaster::info(__('app.log_processed_no_kills'));
        }

        $this->dispatch('refreshSelf');
    }
}
