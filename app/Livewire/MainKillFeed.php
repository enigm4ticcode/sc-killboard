<?php

namespace App\Livewire;

use App\Services\RecentKillsService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class MainKillFeed extends Component
{
    use WithPagination;

    public Collection $data;

    public function render(RecentKillsService $recentKillsService): View
    {
        $kills = $this->loadData($recentKillsService);
        $perPage = config('killboard.pagination.kills_per_page');
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $offset = ($currentPage * $perPage) - $perPage;
        $currentItems = $kills->slice($offset, $perPage);
        $paginated = new LengthAwarePaginator($currentItems, count($kills), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        return view('livewire.main-kill-feed', ['kills' => $paginated]);
    }

    #[On('killboard-updated')]
    public function loadData(RecentKillsService $recentKillsService): Collection
    {
        $this->data = $recentKillsService->getRecentKills();

        return $this->data;
    }
}
