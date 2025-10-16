<?php

namespace App\Filament\Admin\Pages;

use App\Services\GameLogService;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

class UploadLog extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-up-tray';

    protected static ?string $navigationLabel = 'Upload Log';

    protected static ?string $title = 'Upload Log File';

    protected static ?string $slug = 'upload-log';

    protected string $view = 'filament.admin.pages.upload-log';

    public ?array $data = [];

    protected GameLogService $gameLogService;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        $disk = config('filesystems.default', 'local');

        return $schema
            ->statePath('data')
            ->components([
                FileUpload::make('log_file')
                    ->label('Log file (.log)')
                    ->required()
                    ->disk($disk)
                    ->directory('uploads/logs')
                    ->preserveFilenames()
                    ->getUploadedFileNameForStorageUsing(function ($file): string {
                        $original = $file->getClientOriginalName();
                        $safe = preg_replace('/[^A-Za-z0-9._-]/', '_', $original);

                        return uniqid('log_', true).'-'.$safe;
                    })
                    ->rules(['required', 'file', 'mimetypes:text/plain', 'extensions:log'])
                    ->helperText('Only .log files are accepted.')
                    ->deletable(false),
            ]);
    }

    public function save(): void
    {
        $gameLogService = app(GameLogService::class);
        $this->validate();
        $data = $this->form->getState();

        $disk = config('filesystems.default', 'local');
        $storedPath = null;
        $file = $data['log_file'] ?? null;

        if ($file instanceof \Illuminate\Http\UploadedFile) {
            $original = $file->getClientOriginalName();
            $safe = preg_replace('/[^A-Za-z0-9._-]/', '_', $original);
            $name = uniqid('log_', true).'-'.$safe;
            $storedPath = $file->storeAs('uploads/logs', $name, $disk);
        } elseif (is_string($file)) {
            $storedPath = $file;
        }

        if (! $storedPath || ! Storage::disk($disk)->exists($storedPath)) {
            Notification::make()
                ->title('Upload failed')
                ->body('There was a problem uploading the log file.')
                ->danger()
                ->send();

            return;
        }

        $totalKills = $gameLogService->processGameLog($storedPath);

        Notification::make()
            ->title('Log file uploaded')
            ->body("Log file uploaded successfully. Processed $totalKills Kills.")
            ->success()
            ->send();

        $this->reset('data');
        $this->form->fill();
    }

    public function upload(): void
    {
        // Alias method expected by tests; delegates to save()
        $this->save();
    }

    public function getActions(): array
    {
        return [
            Action::make('save')
                ->label('Process Game.log')
                ->rateLimit(5)
                ->icon('heroicon-o-arrow-up-tray')
                ->action(fn () => $this->save()),
        ];
    }
}
