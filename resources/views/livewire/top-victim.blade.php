<div class="card !rounded-xl overflow-hidden">
    <div class="border-b px-4 py-3 text-sm font-bold sc-hud-header sc-grid-pattern sc-hud-glow" style="border-color: rgba(var(--accent), 0.3); background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(239, 68, 68, 0.05) 100%); color: rgb(var(--fg));">
        <div class="flex items-center gap-2">
            <span class="text-xl">☠️</span>
            <span>{{ __('app.top_victims', ['count' => config('killboard.leaderboards.number-of-positions'), 'days' => config('killboard.leaderboards.timespan-days')]) }}</span>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y" style="divide-color: rgb(var(--card-border));">
            <thead style="background-color: rgb(var(--table-header));">
            <tr>
                <th scope="col" class="px-2 py-2 text-center text-[10px] font-semibold uppercase tracking-wider align-middle" style="color: rgb(var(--muted));">#</th>
                <th scope="col" class="px-2 py-2 text-center text-[10px] font-semibold uppercase tracking-wider align-middle" style="color: rgb(var(--muted));">{{ __('app.avatar') }}</th>
                <th scope="col" class="px-2 py-2 text-left text-[10px] font-semibold uppercase tracking-wider align-middle" style="color: rgb(var(--muted));">{{ __('app.name') }}</th>
                <th scope="col" class="px-2 py-2 text-right text-[10px] font-semibold uppercase tracking-wider align-middle" style="color: rgb(var(--muted));">{{ __('app.deaths') }}</th>
            </tr>
            </thead>
            <tbody class="divide-y" style="divide-color: rgb(var(--card-border)); background-color: rgb(var(--card));">
            @forelse ($victims as $i => $victim)
                <tr class="transition-colors" style="background-color: rgba(239, 68, 68, 0.08);" onmouseover="this.style.backgroundColor='rgb(var(--table-hover))'" onmouseout="this.style.backgroundColor='rgba(239, 68, 68, 0.08)'">
                    <td class="px-2 py-3 text-xs text-center align-middle whitespace-nowrap">
                        <div class="inline-flex items-center justify-center w-6 h-6 rounded-full font-bold text-xs" style="background-color: rgba(var(--accent), 0.2); color: rgb(var(--accent));">
                            {{ $i + 1 }}
                        </div>
                    </td>
                    @if (! empty($victim->victim->avatar) && (str_contains($victim->victim->avatar, '/media/') || str_contains($victim->victim->avatar, '/static/images/account/avatar_default')))
                        <td class="px-2 py-3 text-center align-middle">
                            <a href="{{ route('player.show', ['name' => $victim->victim->name]) }}" class="inline-block transition-transform hover:scale-110">
                                <img width="48" height="48" alt="{{ $victim->victim->name }}" src="{{ $victim->victim->avatar }}" class="mx-auto align-middle rounded-full w-12 h-12 object-cover" style="box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);" />
                            </a>
                        </td>
                    @else
                        <td class="px-2 py-3 text-center align-middle">
                            <a href="{{ route('player.show', ['name' => $victim->victim->name]) }}" class="inline-block transition-transform hover:scale-110">
                                <img width="48" height="48" src="https://cdn.robertsspaceindustries.com/static/images/account/avatar_default_big.jpg" alt="{{ $victim->victim->name }}" class="mx-auto align-middle rounded-full w-12 h-12 object-cover" style="box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);" />
                            </a>
                        </td>
                    @endif
                    <td class="px-2 py-3 text-xs align-middle whitespace-nowrap">
                        <a href="{{ route('player.show', ['name' => $victim->victim->name]) }}" class="font-semibold hover:underline transition-colors truncate block max-w-[120px]" style="color: rgb(var(--fg));">{{ $victim->victim->name }}</a>
                    </td>
                    <td class="px-2 py-3 text-right align-middle whitespace-nowrap">
                        <span class="inline-block px-2 py-1 text-xs font-bold rounded" style="color: rgb(var(--fg)); background-color: rgba(239, 68, 68, 0.2);">
                            {{ $victim->death_count }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-3 py-6 text-center text-xs align-middle" style="color: rgb(var(--muted));">{{ __('app.no_data_yet') }}</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
