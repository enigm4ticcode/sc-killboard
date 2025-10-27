<?php

namespace App\Livewire;

use App\Services\RsiStatusService;
use Illuminate\View\View;
use Livewire\Component;

class RsiStatus extends Component
{
    public array $status;

    protected $listeners = ['statusUpdated' => 'reloadData'];

    public function mount(): void
    {
        $this->reloadData();
    }

    protected function reloadData(): void
    {
        $rsiStatusService = app(RsiStatusService::class);

        $this->status = $rsiStatusService->getRsiStatus();
    }

    public function render(): View
    {
        return view('livewire.rsi-status');
    }
}
