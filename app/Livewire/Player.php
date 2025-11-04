<?php

namespace App\Livewire;

use App\Models\Player as PlayerModel;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;
use Illuminate\View\View;
use Livewire\Component;

class Player extends Component
{
    public PlayerModel $player;

    public Collection $data;

    public int $totalKills;

    public int $totalLosses;

    public float $efficiency;

    public function mount(?string $name): void
    {
        $this->player = PlayerModel::query()->where('name', $name)->firstOrFail();
        $days = config('killboard.home_page.most_recent_kills_days');
        $dateTime = Carbon::now()->subDays($days)->startOfDay();

        // Optimize: Use single query to get both total counts
        $counts = \App\Models\Kill::query()
            ->selectRaw('
                COUNT(CASE WHEN killer_id = ? THEN 1 END) as total_kills,
                COUNT(CASE WHEN victim_id = ? THEN 1 END) as total_losses
            ', [$this->player->id, $this->player->id])
            ->first();

        $this->totalKills = $counts->total_kills ?? 0;
        $this->totalLosses = $counts->total_losses ?? 0;

        // Optimize: Use single query with union for recent kills and losses
        $kills = $this->player->kills()
            ->where('destroyed_at', '>=', $dateTime)
            ->with(['victim', 'weapon', 'ship'])
            ->get();

        $losses = $this->player->losses()
            ->where('destroyed_at', '>=', $dateTime)
            ->with(['killer', 'weapon', 'ship'])
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
        return view('livewire.player');
    }
}
