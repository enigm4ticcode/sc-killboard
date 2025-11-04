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

    public $isSearching = false;

    // Minimum characters before triggering search
    protected const MIN_QUERY_LENGTH = 2;

    // Maximum results to return per type
    protected const MAX_RESULTS_PER_TYPE = 10;

    public function updatedQuery(): void
    {
        // Reset results and dropdown if query is empty
        if (empty($this->query)) {
            $this->results = [];
            $this->showDropdown = false;
            $this->isSearching = false;

            return;
        }

        // Require minimum character length to prevent expensive searches on short queries
        if (strlen(trim($this->query)) < self::MIN_QUERY_LENGTH) {
            $this->results = [];
            $this->showDropdown = false;
            $this->isSearching = false;

            return;
        }

        $this->isSearching = true;

        // Limit results to prevent excessive data transfer
        $players = Player::search($this->query)->take(self::MAX_RESULTS_PER_TYPE)->get();
        $organizations = Organization::search($this->query)->take(self::MAX_RESULTS_PER_TYPE)->get();

        $this->results = [
            'players' => $players->map(function (Player $player) {
                return (object) [
                    'id' => $player->id,
                    'name' => $player->name,
                    'avatar' => $player->avatar,
                    'type' => 'player',
                    'url' => route('player.show', ['name' => $player->name]),
                ];
            }),
            'organizations' => $organizations->map(function (Organization $organization) {
                return (object) [
                    'id' => $organization->id,
                    'name' => $organization->name,
                    'spectrum_id' => $organization->spectrum_id,
                    'icon' => $organization->icon,
                    'type' => 'organization',
                    'url' => route('organization.show', ['name' => $organization->spectrum_id]),
                ];
            }),
        ];

        $this->showDropdown = true;
        $this->isSearching = false;
    }

    public function render(): View
    {
        return view('livewire.global-search');
    }
}
