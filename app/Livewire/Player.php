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
        $kills = $this->player->kills()->where('destroyed_at', '>=', $dateTime)->get();
        $losses = $this->player->losses()->where('destroyed_at', '>=', $dateTime)->get();
        $this->totalKills = $this->player->kills()->count();
        $this->totalLosses = $this->player->losses()->count();
        $this->data = $kills->merge($losses)->sortByDesc('destroyed_at');
        $this->efficiency = Number::format(($this->totalKills / ($this->totalKills + $this->totalLosses)) * 100, 2);
    }

    public function render(): View
    {
        return view('livewire.player');
    }
}
