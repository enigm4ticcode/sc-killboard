@php
if (! isset($scrollTo)) {
    $scrollTo = 'body';
}

$scrollIntoViewJsSnippet = ($scrollTo !== false)
    ? <<<JS
       (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
    JS
    : '';
@endphp

<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
            <div class="flex justify-between flex-1 sm:hidden">
                <span>
                    @if ($paginator->onFirstPage())
                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium border cursor-default leading-5 rounded-md" style="color: rgb(var(--muted)); background-color: rgb(var(--card)); border-color: rgb(var(--card-border));">
                            {!! __('pagination.previous') !!}
                        </span>
                    @else
                        <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.before" class="relative inline-flex items-center px-4 py-2 text-sm font-medium border leading-5 rounded-md focus:outline-none focus:ring transition ease-in-out duration-150" style="color: rgb(var(--fg)); background-color: rgb(var(--card)); border-color: rgb(var(--card-border)); focus-ring-color: rgb(var(--accent) / 0.3);" onmouseover="this.style.backgroundColor='rgb(var(--table-hover))'; this.style.color='rgb(var(--accent))'" onmouseout="this.style.backgroundColor='rgb(var(--card))'; this.style.color='rgb(var(--fg))'">
                            {!! __('pagination.previous') !!}
                        </button>
                    @endif
                </span>

                <span>
                    @if ($paginator->hasMorePages())
                        <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.before" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium border leading-5 rounded-md focus:outline-none focus:ring transition ease-in-out duration-150" style="color: rgb(var(--fg)); background-color: rgb(var(--card)); border-color: rgb(var(--card-border)); focus-ring-color: rgb(var(--accent) / 0.3);" onmouseover="this.style.backgroundColor='rgb(var(--table-hover))'; this.style.color='rgb(var(--accent))'" onmouseout="this.style.backgroundColor='rgb(var(--card))'; this.style.color='rgb(var(--fg))'">
                            {!! __('pagination.next') !!}
                        </button>
                    @else
                        <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium border cursor-default leading-5 rounded-md" style="color: rgb(var(--muted)); background-color: rgb(var(--card)); border-color: rgb(var(--card-border));">
                            {!! __('pagination.next') !!}
                        </span>
                    @endif
                </span>
            </div>

            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm leading-5" style="color: rgb(var(--fg));">
                        {!! __('pagination.showing', [
                            'first' => '<span class="font-medium">' . $paginator->firstItem() . '</span>',
                            'last' => '<span class="font-medium">' . $paginator->lastItem() . '</span>',
                            'total' => '<span class="font-medium">' . $paginator->total() . '</span>'
                        ]) !!}
                    </p>
                </div>

                <div>
                    <span class="relative z-0 inline-flex rtl:flex-row-reverse rounded-md shadow-sm">
                        <span>
                            {{-- Previous Page Link --}}
                            @if ($paginator->onFirstPage())
                                <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                                    <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium border cursor-default rounded-l-md leading-5" style="color: rgb(var(--muted)); background-color: rgb(var(--card)); border-color: rgb(var(--card-border));" aria-hidden="true">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </span>
                            @else
                                <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.after" class="relative inline-flex items-center px-2 py-2 text-sm font-medium border rounded-l-md leading-5 focus:z-10 focus:outline-none focus:ring transition ease-in-out duration-150" style="color: rgb(var(--fg)); background-color: rgb(var(--card)); border-color: rgb(var(--card-border)); focus-ring-color: rgb(var(--accent) / 0.3);" onmouseover="this.style.backgroundColor='rgb(var(--table-hover))'; this.style.color='rgb(var(--accent))'" onmouseout="this.style.backgroundColor='rgb(var(--card))'; this.style.color='rgb(var(--fg))'" aria-label="{{ __('pagination.previous') }}">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            @endif
                        </span>

                        {{-- Pagination Elements --}}
                        @foreach ($elements as $element)
                            {{-- "Three Dots" Separator --}}
                            @if (is_string($element))
                                <span aria-disabled="true">
                                    <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium border cursor-default leading-5" style="color: rgb(var(--fg)); background-color: rgb(var(--card)); border-color: rgb(var(--card-border));">{{ $element }}</span>
                                </span>
                            @endif

                            {{-- Array Of Links --}}
                            @if (is_array($element))
                                @foreach ($element as $page => $url)
                                    <span wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}">
                                        @if ($page == $paginator->currentPage())
                                            <span aria-current="page">
                                                <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-bold border cursor-default leading-5 shadow-md" style="color: rgb(var(--accent-fg)); background-color: rgb(var(--accent)); border-color: rgb(var(--accent));">{{ $page }}</span>
                                            </span>
                                        @else
                                            <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium border leading-5 focus:z-10 focus:outline-none focus:ring transition ease-in-out duration-150" style="color: rgb(var(--fg)); background-color: rgb(var(--card)); border-color: rgb(var(--card-border)); focus-ring-color: rgb(var(--accent) / 0.3);" onmouseover="this.style.backgroundColor='rgb(var(--table-hover))'; this.style.color='rgb(var(--accent))'" onmouseout="this.style.backgroundColor='rgb(var(--card))'; this.style.color='rgb(var(--fg))'" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                                {{ $page }}
                                            </button>
                                        @endif
                                    </span>
                                @endforeach
                            @endif
                        @endforeach

                        <span>
                            {{-- Next Page Link --}}
                            @if ($paginator->hasMorePages())
                                <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.after" class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium border rounded-r-md leading-5 focus:z-10 focus:outline-none focus:ring transition ease-in-out duration-150" style="color: rgb(var(--fg)); background-color: rgb(var(--card)); border-color: rgb(var(--card-border)); focus-ring-color: rgb(var(--accent) / 0.3);" onmouseover="this.style.backgroundColor='rgb(var(--table-hover))'; this.style.color='rgb(var(--accent))'" onmouseout="this.style.backgroundColor='rgb(var(--card))'; this.style.color='rgb(var(--fg))'" aria-label="{{ __('pagination.next') }}">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            @else
                                <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                                    <span class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium border cursor-default rounded-r-md leading-5" style="color: rgb(var(--muted)); background-color: rgb(var(--card)); border-color: rgb(var(--card-border));" aria-hidden="true">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </span>
                            @endif
                        </span>
                    </span>
                </div>
            </div>
        </nav>
    @endif
</div>
