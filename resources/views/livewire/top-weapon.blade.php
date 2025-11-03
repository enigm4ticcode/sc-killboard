<div class="card !rounded-xl overflow-hidden">
    <div class="border-b px-3 py-2 text-xs font-semibold" style="border-color: rgb(var(--card-border)); background-color: rgb(var(--table-header)); color: rgb(var(--fg));">
        🔫 {{ __('app.top_weapons', ['count' => config('killboard.leaderboards.number-of-positions'), 'days' => config('killboard.leaderboards.timespan-days')]) }}
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
                <tr class="transition-colors" onmouseover="this.style.backgroundColor='rgb(var(--table-hover))'" onmouseout="this.style.backgroundColor='rgb(var(--card))'">
                    <td class="px-2 py-2 text-xs text-center align-middle whitespace-nowrap font-bold" style="color: rgb(var(--accent));">{{ $i + 1 }}</td>
                    <td class="px-2 py-2 text-xs align-middle font-mono truncate max-w-[140px]" style="color: rgb(var(--fg));">{{ $weapon->weapon->name }}</td>
                    <td class="px-2 py-2 text-xs text-right align-middle whitespace-nowrap font-mono font-semibold" style="color: rgb(var(--fg));">{{ $weapon->weapon_kill_count }}</td>
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
