<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\View\View;
use Livewire\Component;

class HomePage extends Component
{
    public array $recentDates = [];

    public function mount(): void
    {
        $this->recentDates = [
            Carbon::today()->startOfDay()->toDateString(),
            Carbon::yesterday()->startOfDay()->toDateString(),
            Carbon::today()->subDays(2)->startOfDay()->toDateString(),
            Carbon::today()->subDays(3)->startOfDay()->toDateString(),
            Carbon::today()->subDays(4)->startOfDay()->toDateString(),
            Carbon::today()->subDays(5)->startOfDay()->toDateString(),
            Carbon::today()->subDays(6)->startOfDay()->toDateString(),
            Carbon::today()->subDays(7)->startOfDay()->toDateString(),
        ];
    }

    public function render(): View
    {
        return view('livewire.home-page')->layout('layouts.guest');
    }
}
