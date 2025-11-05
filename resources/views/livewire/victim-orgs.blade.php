<div class="card !rounded-xl overflow-hidden">
    <div class="border-b px-4 py-3 text-sm font-bold sc-hud-header sc-grid-pattern sc-hud-glow" style="border-color: rgba(var(--accent), 0.3); background: linear-gradient(135deg, rgba(var(--accent), 0.15) 0%, rgba(var(--accent), 0.05) 100%); color: rgb(var(--fg));">
        <div class="flex items-center gap-2">
            <span class="text-xl">⚰️</span>
            <span>{{ __('app.top_victim_orgs', ['count' => config('killboard.leaderboards.number-of-positions'), 'days' => config('killboard.leaderboards.timespan-days')]) }}</span>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y" style="divide-color: rgb(var(--card-border));">
            <thead style="background-color: rgb(var(--table-header));">
            <tr>
                <th scope="col" class="px-2 py-2 text-center text-[10px] font-semibold uppercase tracking-wider align-middle" style="color: rgb(var(--muted));">#</th>
                <th scope="col" class="px-2 py-2 text-center text-[10px] font-semibold uppercase tracking-wider align-middle" style="color: rgb(var(--muted));">{{ __('app.logo') }}</th>
                <th scope="col" class="px-2 py-2 text-right text-[10px] font-semibold uppercase tracking-wider align-middle" style="color: rgb(var(--muted));">{{ __('app.deaths') }}</th>
                <th scope="col" class="px-2 py-2 text-right text-[10px] font-semibold uppercase tracking-wider align-middle" style="color: rgb(var(--muted));">{{ __('app.avg') }}</th>
            </tr>
            </thead>
            <tbody class="divide-y" style="divide-color: rgb(var(--card-border)); background-color: rgb(var(--card));">
            @forelse ($orgs as $i => $org)
                @if($org->spectrum_id !== \App\Models\Organization::ORG_NONE)
                    <tr class="transition-colors" style="background-color: rgba(var(--accent), 0.05);" onmouseover="this.style.backgroundColor='rgb(var(--table-hover))'" onmouseout="this.style.backgroundColor='rgba(var(--accent), 0.05)'">
                        @php($iconUrl = \Illuminate\Support\Str::contains($org->organization_icon, 'http') ? $org->organization_icon : 'https://robertsspaceindustries.com/' . $org->organization_icon)

                        <td class="px-2 py-3 text-xs text-center align-middle whitespace-nowrap">
                            <div class="inline-flex items-center justify-center w-6 h-6 rounded-full font-bold text-xs" style="background-color: rgba(var(--accent), 0.2); color: rgb(var(--accent));">
                                {{ $i + 1 }}
                            </div>
                        </td>
                        <td class="px-2 py-3 text-center align-middle">
                            <a href="{{ route('organization.show', ['name' => $org->spectrum_id]) }}" class="inline-block transition-transform hover:scale-110">
                                <img width="48" height="48" alt="{{ $org->organization_name }}" src="{{ $iconUrl }}" class="mx-auto align-middle rounded-lg w-12 h-12 object-cover" style="box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);" />
                            </a>
                        </td>
                        <td class="px-2 py-3 text-right align-middle whitespace-nowrap">
                            <span class="inline-block px-2 py-1 text-xs font-bold rounded" style="color: rgb(var(--fg)); background-color: rgba(var(--accent), 0.15);">
                                {{ $org->total_deaths }}
                            </span>
                        </td>
                        <td class="px-2 py-3 text-xs text-right align-middle whitespace-nowrap font-mono" style="color: rgb(var(--muted));">{{ \Illuminate\Support\Number::format($org->average_deaths_per_player, 1) }}</td>
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
