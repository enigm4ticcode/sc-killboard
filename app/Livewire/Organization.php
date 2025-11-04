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

        // Optimize: Get total counts in single query using organization relationships
        $playerIds = $this->organization->players()->pluck('id')->toArray();

        if (! empty($playerIds)) {
            $counts = \App\Models\Kill::query()
                ->selectRaw('
                    COUNT(CASE WHEN killer_id IN ('.implode(',', array_map('intval', $playerIds)).') THEN 1 END) as total_kills,
                    COUNT(CASE WHEN victim_id IN ('.implode(',', array_map('intval', $playerIds)).') THEN 1 END) as total_losses
                ')
                ->first();

            $this->totalKills = $counts->total_kills ?? 0;
            $this->totalLosses = $counts->total_losses ?? 0;
        } else {
            $this->totalKills = 0;
            $this->totalLosses = 0;
        }

        // Optimize: Add eager loading for recent kills and losses
        $kills = $this->organization->kills()
            ->where('destroyed_at', '>=', $dateTime)
            ->with(['victim', 'killer', 'weapon', 'ship'])
            ->get();

        $losses = $this->organization->losses()
            ->where('destroyed_at', '>=', $dateTime)
            ->with(['victim', 'killer', 'weapon', 'ship'])
            ->get();

        $this->data = $kills->merge($losses)->sortByDesc('destroyed_at');

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
