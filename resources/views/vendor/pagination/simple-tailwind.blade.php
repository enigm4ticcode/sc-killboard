@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{!! __('Pagination Navigation') !!}" class="flex justify-between">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium border cursor-default leading-5 rounded-md" style="color: rgb(var(--muted)); background-color: rgb(var(--card)); border-color: rgb(var(--card-border));">
                {!! __('pagination.previous') !!}
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-4 py-2 text-sm font-medium border leading-5 rounded-md focus:outline-none focus:ring transition ease-in-out duration-150" style="color: rgb(var(--fg)); background-color: rgb(var(--card)); border-color: rgb(var(--card-border)); focus-ring-color: rgb(var(--accent) / 0.3);" onmouseover="this.style.backgroundColor='rgb(var(--table-hover))'; this.style.color='rgb(var(--accent))'" onmouseout="this.style.backgroundColor='rgb(var(--card))'; this.style.color='rgb(var(--fg))'">
                {!! __('pagination.previous') !!}
            </a>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-4 py-2 text-sm font-medium border leading-5 rounded-md focus:outline-none focus:ring transition ease-in-out duration-150" style="color: rgb(var(--fg)); background-color: rgb(var(--card)); border-color: rgb(var(--card-border)); focus-ring-color: rgb(var(--accent) / 0.3);" onmouseover="this.style.backgroundColor='rgb(var(--table-hover))'; this.style.color='rgb(var(--accent))'" onmouseout="this.style.backgroundColor='rgb(var(--card))'; this.style.color='rgb(var(--fg))'">
                {!! __('pagination.next') !!}
            </a>
        @else
            <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium border cursor-default leading-5 rounded-md" style="color: rgb(var(--muted)); background-color: rgb(var(--card)); border-color: rgb(var(--card-border));">
                {!! __('pagination.next') !!}
            </span>
        @endif
    </nav>
@endif
