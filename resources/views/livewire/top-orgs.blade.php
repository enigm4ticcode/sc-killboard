<div class="card !rounded-xl overflow-hidden">
    <div class="border-b px-3 py-2 text-xs font-semibold" style="border-color: rgb(var(--card-border)); background-color: rgb(var(--table-header)); color: rgb(var(--fg));">
        🏢 {{ __('app.top_org_killers', ['count' => config('killboard.leaderboards.number-of-positions'), 'days' => config('killboard.leaderboards.timespan-days')]) }}
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y" style="divide-color: rgb(var(--card-border));">
            <thead style="background-color: rgb(var(--table-header));">
            <tr>
                <th scope="col" class="px-2 py-2 text-center text-[10px] font-semibold uppercase tracking-wider align-middle" style="color: rgb(var(--muted));">#</th>
                <th scope="col" class="px-2 py-2 text-center text-[10px] font-semibold uppercase tracking-wider align-middle" style="color: rgb(var(--muted));">{{ __('app.logo') }}</th>
                <th scope="col" class="px-2 py-2 text-right text-[10px] font-semibold uppercase tracking-wider align-middle" style="color: rgb(var(--muted));">{{ __('app.kills') }}</th>
                <th scope="col" class="px-2 py-2 text-right text-[10px] font-semibold uppercase tracking-wider align-middle" style="color: rgb(var(--muted));">{{ __('app.avg') }}</th>
            </tr>
            </thead>
            <tbody class="divide-y" style="divide-color: rgb(var(--card-border)); background-color: rgb(var(--card));">
            @forelse ($orgs as $i => $org)
                @if($org->spectrum_id !== \App\Models\Organization::ORG_NONE)
                    <tr class="transition-colors" onmouseover="this.style.backgroundColor='rgb(var(--table-hover))'" onmouseout="this.style.backgroundColor='rgb(var(--card))'">
                        @php($iconUrl = \Illuminate\Support\Str::contains($org->organization_icon, 'http') ? $org->organization_icon : 'https://robertsspaceindustries.com/' . $org->organization_icon)

                        <td class="px-2 py-2 text-xs text-center align-middle whitespace-nowrap font-bold" style="color: rgb(var(--accent));">{{ $i + 1 }}</td>
                        <td class="px-2 py-2 text-xs text-center align-middle">
                            <a href="{{ route('organization.show', ['name' => $org->spectrum_id]) }}" class="inline-block transition-transform hover:scale-110">
                                <img width="40" height="40" alt="{{ $org->organization_name }}" src="{{ $iconUrl }}" class="mx-auto align-middle rounded shadow-sm" />
                            </a>
                        </td>
                        <td class="px-2 py-2 text-xs text-right align-middle whitespace-nowrap font-mono font-semibold" style="color: rgb(var(--fg));">{{ $org->total_kills }}</td>
                        <td class="px-2 py-2 text-xs text-right align-middle whitespace-nowrap font-mono" style="color: rgb(var(--muted));">{{ \Illuminate\Support\Number::format($org->average_kills_per_player, 1) }}</td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="4" class="px-3 py-6 text-center text-xs align-middle" style="color: rgb(var(--muted));">{{ __('app.no_data_yet') }}</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
