<div class="card !rounded-xl overflow-hidden">
    <div class="border-b px-3 py-2 text-xs font-semibold" style="border-color: rgb(var(--card-border)); background-color: rgb(var(--table-header)); color: rgb(var(--fg));">
        🏆 {{ __('app.top_killers', ['count' => config('killboard.leaderboards.number-of-positions'), 'days' => config('killboard.leaderboards.timespan-days')]) }}
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y" style="divide-color: rgb(var(--card-border));">
            <thead style="background-color: rgb(var(--table-header));">
            <tr>
                <th scope="col" class="px-2 py-2 text-center text-[10px] font-semibold uppercase tracking-wider align-middle" style="color: rgb(var(--muted));">#</th>
                <th scope="col" class="px-2 py-2 text-center text-[10px] font-semibold uppercase tracking-wider align-middle" style="color: rgb(var(--muted));">{{ __('app.avatar') }}</th>
                <th scope="col" class="px-2 py-2 text-left text-[10px] font-semibold uppercase tracking-wider align-middle" style="color: rgb(var(--muted));">{{ __('app.name') }}</th>
                <th scope="col" class="px-2 py-2 text-right text-[10px] font-semibold uppercase tracking-wider align-middle" style="color: rgb(var(--muted));">{{ __('app.kills') }}</th>
            </tr>
            </thead>
            <tbody class="divide-y" style="divide-color: rgb(var(--card-border)); background-color: rgb(var(--card));">
            @forelse ($killers as $i => $killer)
                <tr class="transition-colors" style="background-color: rgba(34, 197, 94, 0.08);" onmouseover="this.style.backgroundColor='rgb(var(--table-hover))'" onmouseout="this.style.backgroundColor='rgba(34, 197, 94, 0.08)'">
                    <td class="px-2 py-2 text-xs text-center align-middle whitespace-nowrap font-bold" style="color: rgb(var(--accent));">{{ $i + 1 }}</td>
                    @if (! empty($killer->killer->avatar))
                        <td class="px-2 py-2 text-xs text-center align-middle">
                            <a href="{{ route('player.show', ['name' => $killer->killer->name]) }}" class="inline-block transition-transform hover:scale-110">
                                <img width="40" height="40" alt="{{ $killer->killer->name }}" src="{{ $killer->killer->avatar }}" class="mx-auto align-middle rounded-full shadow-sm" />
                            </a>
                        </td>
                    @else
                        <td class="px-2 py-2 text-xs text-center align-middle">
                            <img width="40" height="40" src="https://cdn.robertsspaceindustries.com/static/images/account/avatar_default_big.jpg" alt="{{ $killer->killer->name }}" class="mx-auto align-middle rounded-full shadow-sm" />
                        </td>
                    @endif
                    <td class="px-2 py-2 text-xs align-middle whitespace-nowrap">
                        <a href="{{ route('player.show', ['name' => $killer->killer->name]) }}" class="font-semibold hover:underline transition-colors truncate block max-w-[120px]" style="color: rgb(var(--fg));">{{ $killer->killer->name }}</a>
                    </td>
                    <td class="px-2 py-2 text-xs text-right align-middle whitespace-nowrap font-mono font-semibold" style="color: rgb(var(--fg));">{{ $killer->kill_count }}</td>
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
