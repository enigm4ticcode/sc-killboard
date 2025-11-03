<div class="relative">
    <input
        type="text"
        wire:model.live.debounce.300ms="query"
        wire:keydown.escape="showDropdown = false"
        wire:focus="showDropdown = true"
        placeholder="{{ __('app.search_placeholder') }}"
        class="form-input w-full rounded-lg shadow-sm p-2.5 border transition-all duration-200"
        style="background-color: rgb(var(--card)); color: rgb(var(--fg)); border-color: rgb(var(--card-border));"
    />

    @if($showDropdown && !empty($query))
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
