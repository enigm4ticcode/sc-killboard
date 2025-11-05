<div class="card !rounded-xl overflow-hidden">
    <div class="border-b px-4 py-3 text-sm font-bold sc-hud-header sc-grid-pattern sc-hud-glow" style="border-color: rgba(var(--accent), 0.3); background: linear-gradient(135deg, rgba(var(--accent), 0.15) 0%, rgba(var(--accent), 0.05) 100%); color: rgb(var(--fg));">
        <div class="flex items-center gap-2">
            <span class="text-xl">🔫</span>
            <span>{{ __('app.top_weapons', ['count' => config('killboard.leaderboards.number-of-positions'), 'days' => config('killboard.leaderboards.timespan-days')]) }}</span>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y" style="divide-color: rgb(var(--card-border));">
            <thead style="background-color: rgb(var(--table-header));">
            <tr>
                <th scope="col" class="px-2 py-2 text-center text-[10px] font-semibold uppercase tracking-wider align-middle" style="color: rgb(var(--muted));">#</th>
                <th scope="col" class="px-2 py-2 text-left text-[10px] font-semibold uppercase tracking-wider align-middle" style="color: rgb(var(--muted));">{{ __('app.weapon') }}</th>
                <th scope="col" class="px-2 py-2 text-right text-[10px] font-semibold uppercase tracking-wider align-middle" style="color: rgb(var(--muted));">{{ __('app.kills') }}</th>
            </tr>
            </thead>
            <tbody class="divide-y" style="divide-color: rgb(var(--card-border)); background-color: rgb(var(--card));">
            @forelse ($weapons as $i => $weapon)
                <tr class="transition-colors" style="background-color: rgba(var(--accent), 0.05);" onmouseover="this.style.backgroundColor='rgb(var(--table-hover))'" onmouseout="this.style.backgroundColor='rgba(var(--accent), 0.05)'">
                    <td class="px-2 py-3 text-xs text-center align-middle whitespace-nowrap">
                        <div class="inline-flex items-center justify-center w-6 h-6 rounded-full font-bold text-xs" style="background-color: rgba(var(--accent), 0.2); color: rgb(var(--accent));">
                            {{ $i + 1 }}
                        </div>
                    </td>
                    <td class="px-2 py-3 text-xs align-middle font-mono truncate max-w-[140px]" style="color: rgb(var(--fg));">{{ $weapon->weapon->name }}</td>
                    <td class="px-2 py-3 text-right align-middle whitespace-nowrap">
                        <span class="inline-block px-2 py-1 text-xs font-bold rounded" style="color: rgb(var(--fg)); background-color: rgba(var(--accent), 0.15);">
                            {{ $weapon->weapon_kill_count }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-3 py-6 text-center text-xs align-middle" style="color: rgb(var(--muted));">{{ __('app.no_data_yet') }}</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
