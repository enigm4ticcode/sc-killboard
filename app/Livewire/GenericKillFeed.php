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
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        // Slice the data for the current page
        $currentPageItems = $this->data->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $data = new LengthAwarePaginator(
            $currentPageItems,
            $this->data->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return view('livewire.generic-kill-feed', ['feed' => $data]);
    }
}
