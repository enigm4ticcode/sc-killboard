<div class="card overflow-hidden rounded-none sm:rounded-xl border border-l-0 border-r-0 sm:border mb-4" style="border-color: rgb(var(--card-border));">
    <div class="border-b px-4 py-3 text-sm font-bold sc-hud-header sc-grid-pattern sc-hud-glow" style="border-color: rgba(var(--accent), 0.3); background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(239, 68, 68, 0.05) 100%); color: rgb(var(--fg));">
        <div class="flex items-center gap-2">
            <span class="text-xl">💰</span>
            <span>{{ __('app.big_kills', ['days' => config('killboard.leaderboards.timespan-days')]) }}</span>
        </div>
    </div>

    <div class="p-4" style="background-color: rgb(var(--card));">
        @if($bigKills->isNotEmpty())
            <div class="flex flex-wrap lg:flex-nowrap gap-2 md:gap-3 lg:gap-5 justify-center">
                @foreach($bigKills as $kill)
                    <div class="flex flex-col items-center p-1.5 md:p-2 lg:p-4 rounded-lg transition-all hover:scale-105 flex-shrink-0 w-20 md:w-24 lg:w-40 {{ $loop->index >= 4 ? 'hidden md:flex' : '' }}" style="background-color: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.2);">
                        {{-- Ship Image --}}
                        <div class="w-full mb-1 md:mb-1.5 lg:mb-3">
                            @if($kill->ship->icon)
                                <img src="{{ $kill->ship->icon_url ?? $kill->ship->icon }}" alt="{{ $kill->ship->name }}" class="w-full h-12 md:h-16 lg:h-28 object-contain" style="box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);" />
                            @else
                                <img src="{{ Vite::asset('resources/images/ships/default.jpg') }}" alt="{{ $kill->ship->name }}" class="w-full h-12 md:h-16 lg:h-28 object-contain" style="box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);" />
                            @endif
                        </div>

                        {{-- Ship Name --}}
                        <div class="text-[10px] md:text-xs lg:text-sm font-bold text-center mb-1 lg:mb-3 w-full break-words" style="color: rgb(var(--fg));">
                            {{ $kill->ship->name }}
                        </div>

                        {{-- Victim Info --}}
                        <div class="flex items-center w-full justify-center">
                            <a href="{{ route('player.show', ['name' => $kill->victim->name]) }}" class="text-[7px] lg:text-sm font-semibold hover:underline transition-colors truncate max-w-[120px]" style="color: rgb(var(--accent));">
                                {{ $kill->victim->name }}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="py-6 text-center text-xs" style="color: rgb(var(--muted));">
                {{ __('app.no_big_kills_yet') }}
            </div>
        @endif
    </div>
</div>
