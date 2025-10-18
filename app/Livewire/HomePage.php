<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class HomePage extends Component
{
    #[Layout('layouts.guest')]
    public function render(): View
    {
        return view('livewire.home-page');
    }
}
