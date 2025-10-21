<?php

namespace App\Livewire;

use App\Models\Kill;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use App\Models\Organization as OrgModel;

class Organization extends Component
{
    public OrgModel $organization;
    public Collection $data;

    public function mount(string $name = null): void
    {
        $this->organization = OrgModel::query()->where('spectrum_id', $name)->firstOrFail();
        $days = config('killboard.home_page.most_recent_kills_days');
        $dateTime = Carbon::now()->subDays($days)->startOfDay();
        $kills = $this->organization->kills()->where('destroyed_at', '>=', $dateTime)->get();
        $losses = $this->organization->losses()->where('destroyed_at', '>=', $dateTime)->get();
        $this->data = $kills->merge($losses)->sortByDesc('destroyed_at');
    }

    public function render(): View
    {
        return view('livewire.organization');
    }
}
