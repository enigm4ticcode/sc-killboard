<div class="relative">
    <div class="relative">
        <input
            type="text"
            wire:model.live.debounce.500ms="query"
            wire:keydown.escape="showDropdown = false"
            wire:focus="showDropdown = true"
            placeholder="{{ __('app.search_placeholder') }}"
            class="form-input w-full rounded-lg shadow-sm p-2.5 pr-10 border transition-all duration-200"
            style="background-color: rgb(var(--card)); color: rgb(var(--fg)); border-color: rgb(var(--card-border));"
        />

        {{-- Loading spinner --}}
        <div wire:loading wire:target="query" class="absolute right-3 top-1/2 transform -translate-y-1/2">
            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="color: rgb(var(--muted));">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    </div>

    @if($showDropdown && !empty($query) && strlen($query) >= 2)
        <div class="absolute z-10 mt-1 w-full rounded-lg shadow-lg border overflow-hidden" style="background-color: rgb(var(--card)); border-color: rgb(var(--card-border));">
            @if(empty($results['players']) && empty($results['organizations']))
                <div class="p-4" style="color: rgb(var(--fg));">{{ __('app.no_results_found', ['query' => $query]) }}</div>
            @else
                @foreach(['players', 'organizations'] as $type)
                    @if(count($results[$type]) > 0)
                        <div class="p-2 px-4 font-semibold text-sm border-b" style="color: rgb(var(--muted)); border-color: rgb(var(--card-border));">{{ __('app.' . $type) }}</div>
                        @foreach($results[$type] as $result)
                            <a href="{{ $result->url }}" class="block px-4 py-2.5 text-sm transition-colors" style="color: rgb(var(--fg));" onmouseover="this.style.backgroundColor='rgb(var(--dropdown-hover))'" onmouseout="this.style.backgroundColor='transparent'">
                                {{ $result->name }} <span class="text-xs opacity-75">({{ $result->type }})</span>
                            </a>
                        @endforeach
                    @endif
                @endforeach
            @endif
        </div>
    @endif
</div>
