<?php

namespace App\Livewire;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class GenericKillFeed extends Component
{
    use WithPagination;

    public int $id;

    public Collection $data;

    public string $type;

    public function mount(int $id, Collection $data, string $type = 'individual'): void
    {
        $this->id = $id;
        $this->data = $data;
        $this->type = $type;
    }

    public function render(): View
    {
        $perPage = config('killboard.pagination.kills_per_page');
        $data = new LengthAwarePaginator($this->data, $this->data->count(), $perPage);

        return view('livewire.generic-kill-feed', ['feed' => $data]);
    }
}
