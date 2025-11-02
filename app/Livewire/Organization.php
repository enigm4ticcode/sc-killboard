<?php

namespace App\Livewire;

use App\Models\Organization as OrgModel;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;
use Illuminate\View\View;
use Livewire\Component;

class Organization extends Component
{
    public OrgModel $organization;

    public Collection $data;

    public int $totalKills;

    public int $totalLosses;

    public float $efficiency;

    public function mount(?string $name): void
    {
        $this->organization = OrgModel::query()->where('spectrum_id', $name)->firstOrFail();
        $days = config('killboard.home_page.most_recent_kills_days');
        $dateTime = Carbon::now()->subDays($days)->startOfDay();
        $kills = $this->organization->kills()->where('destroyed_at', '>=', $dateTime)->get();
        $losses = $this->organization->losses()->where('destroyed_at', '>=', $dateTime)->get();
        $this->data = $kills->merge($losses)->sortByDesc('destroyed_at');
        $this->totalKills = $this->organization->kills()->count();
        $this->totalLosses = $this->organization->losses()->count();

        $efficiency = ($this->totalKills + $this->totalLosses) > 0
            ? ($this->totalKills / ($this->totalKills + $this->totalLosses)) * 100
            : 0.0;

        if (extension_loaded('intl')) {
            $this->efficiency = Number::format($efficiency, 2);
        } else {
            $this->efficiency = number_format($efficiency, 2);
        }
    }

    public function render(): View
    {
        return view('livewire.organization');
    }
}
