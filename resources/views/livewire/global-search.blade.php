<div class="relative">
    <input
        type="text"
        wire:model.live.debounce.300ms="query"
        wire:keydown.escape="showDropdown = false"
        wire:focus="showDropdown = true"
        placeholder="Search for players or orgs..."
        class="form-input w-full rounded-md shadow-sm"
    />

    @if($showDropdown && !empty($query))
        <div class="absolute z-10 mt-1 w-full bg-white rounded-md shadow-lg">
            @if(empty($results['players']) && empty($results['organizations']))
                <div class="p-4 text-gray-500">No results found for "{{ $query }}"</div>
            @else
                @foreach(['players', 'organizations'] as $type)
                    @if(count($results[$type]) > 0)
                        <div class="p-2 font-semibold text-gray-700 border-b">{{ ucfirst($type) }}</div>
                        @foreach($results[$type] as $result)
                            <a href="{{ $result->url }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                {{ $result->title }} ({{ $result->type }})
                            </a>
                        @endforeach
                    @endif
                @endforeach
            @endif
        </div>
    @endif
</div>
