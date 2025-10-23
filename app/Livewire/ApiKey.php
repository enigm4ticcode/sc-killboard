<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class ApiKey extends Component
{
    public string $apiKey;

    public function mount(): void
    {
        $this->apiKey = Auth::user()->api_key ?? Auth::user()->generateApiKey();
    }

    public function save(): void
    {
        $this->apiKey = Auth::user()->generateApiKey();
    }

    public function render(): View
    {
        return view('livewire.api-key');
    }
}
