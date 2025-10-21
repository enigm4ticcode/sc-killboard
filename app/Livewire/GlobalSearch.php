<?php

namespace App\Livewire;

use App\Models\Organization;
use App\Models\Player;
use Illuminate\View\View;
use Livewire\Component;

class GlobalSearch extends Component
{
    public $query = '';
    public $results = [];
    public $showDropdown = false;

    public function updatedQuery(): void
    {
        if (empty($this->query)) {
            $this->results = [];
            $this->showDropdown = false;

            return;
        }

        $players = Player::search($this->query)->get();
        $organizations = Organization::search($this->query)->get();

        $this->results = [
            'posts' => $players->map(function (Player $player) {
                return (object) [
                    'id' => $player->id,
                    'name' => $player->name,
                    'avatar' => $player->avatar,
                    'type' => 'Player',
                    'url' => route('player.show', $player), // Define your routes accordingly
                ];
            }),
            'products' => $organizations->map(function (Organization $organization) {
                return (object) [
                    'id' => $organization->id,
                    'title' => $organization->name,
                    'spectrum_id' => $organization->spectrum_id,
                    'icon' => $organization->icon,
                    'type' => 'Product',
                    'url' => route('organization.show', $organization), // Define your routes accordingly
                ];
            }),
        ];

        $this->showDropdown = true;
    }

    public function render(): View
    {
        return view('livewire.global-search');
    }
}
