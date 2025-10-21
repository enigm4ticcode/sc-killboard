<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use App\Models\Player as PlayerModel;

class Player extends Component
{
    public PlayerModel $player;
    public Collection $data;

    public function mount(string|null $name): void
    {
        $this->player = PlayerModel::query()->where('name', $name)->firstOrFail();
        $days = config('killboard.home_page.most_recent_kills_days');
        $dateTime = Carbon::now()->subDays($days)->startOfDay();
        $kills = $this->player->kills()->where('destroyed_at', '>=', $dateTime)->get();
        $losses = $this->player->losses()->where('destroyed_at', '>=', $dateTime)->get();
        $this->data = $kills->merge(    $losses)->sortByDesc('destroyed_at');
    }

    public function render(): View
    {
        return view('livewire.player');
    }
}
