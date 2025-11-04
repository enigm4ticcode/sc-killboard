@php use App\Models\Kill;use App\Models\Organization;use Carbon\Carbon;use Illuminate\Support\Str;
$defaultOrg = Organization::where('spectrum_id', Organization::ORG_NONE)->first();
@endphp
<div wire:poll class="overflow-hidden rounded-none sm:rounded-xl border border-l-0 border-r-0 sm:border shadow-lg transition-all duration-300" style="background-color: rgb(var(--card)); border-color: rgb(var(--card-border));">

    <div class="px-2 sm:px-4 md:px-6 py-3 border-b" style="border-color: rgb(var(--card-border)); background-color: rgb(var(--card));">
        {{ $kills->links() }}
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y" style="divide-color: rgb(var(--card-border));">
            <thead style="background-color: rgb(var(--table-header));">
            <tr>
                <th scope="col"
                    class="px-2 py-3 lg:px-6 lg:py-4 text-right text-xs lg:text-sm font-semibold uppercase tracking-wider align-middle"
                    style="color: rgb(var(--muted));">
                    {{ __('app.time_utc') }}
                </th>
                <th scope="col" class="px-1 py-3 lg:px-2 lg:py-4 align-middle"></th>
                <th scope="col"
                    class="px-2 py-3 lg:px-6 lg:py-4 text-left text-xs lg:text-sm font-semibold uppercase tracking-wider align-middle"
                    style="color: rgb(var(--muted));">
                    {{ __('app.victim') }}
                </th>
                <th scope="col"
                    class="px-2 py-3 lg:px-6 lg:py-4 text-center text-xs lg:text-sm font-semibold uppercase tracking-wider align-middle"
                    style="color: rgb(var(--muted));">
                    {{ __('app.organization') }}
                </th>
                <th scope="col"
                    class="px-2 py-3 lg:px-6 lg:py-4 text-right text-xs lg:text-sm font-semibold uppercase tracking-wider align-middle"
                    style="color: rgb(var(--muted));">
                    {{ __('app.vehicle_fps') }}
                </th>
                <th scope="col" class="px-1 py-3 lg:px-2 lg:py-4 align-middle"></th>
                <th scope="col"
                    class="px-2 py-3 lg:px-6 lg:py-4 text-left text-xs lg:text-sm font-semibold uppercase tracking-wider align-middle"
                    style="color: rgb(var(--muted));">
                    {{ __('app.final_blow') }}
                </th>
                <th scope="col"
                    class="px-2 py-3 lg:px-6 lg:py-4 text-center text-xs lg:text-sm font-semibold uppercase tracking-wider align-middle"
                    style="color: rgb(var(--muted));">
                    {{ __('app.organization') }}
                </th>
            </tr>
            </thead>
            <tbody class="divide-y" style="divide-color: rgb(var(--card-border)); background-color: rgb(var(--card));">
            @php($currentDate = null)
            @forelse ($kills as $kill)
                @php($victimOrg = $kill->victim->organization)
                @php($killerOrg = $kill->killer->organization)
                @php($killType = $kill->type)
                @php($killDate = Carbon::parse($kill->destroyed_at)->format('Y-m-d'))
                @php($displayDate = Carbon::parse($kill->destroyed_at)->format('l, F j, Y'))

                @if($currentDate !== $killDate)
                    @php($currentDate = $killDate)
                    <tr style="background-color: rgb(var(--bg));">
                        <td colspan="8" class="px-4 py-3 text-sm font-bold border-t border-b" style="color: rgb(var(--accent)); border-color: rgb(var(--card-border));">
                            📅 {{ $displayDate }}
                        </td>
                    </tr>
                @endif

                <tr class="transition-colors" style="background-color: {{ $killType !== Kill::TYPE_VEHICLE ? 'rgba(99, 102, 241, 0.08)' : 'rgba(34, 197, 94, 0.08)' }};" onmouseover="this.style.backgroundColor='rgb(var(--table-hover))'" onmouseout="this.style.backgroundColor='{{ $killType !== Kill::TYPE_VEHICLE ? 'rgba(99, 102, 241, 0.08)' : 'rgba(34, 197, 94, 0.08)' }}'">
                    <td class="px-2 py-3 lg:px-6 lg:py-5 text-sm lg:text-base text-right font-mono align-middle whitespace-nowrap" style="color: rgb(var(--fg));">
                        {{ Carbon::parse($kill->destroyed_at)->format('H:i:s') }}
                    </td>
                    @if (! empty($kill->victim->avatar) && (str_contains($kill->victim->avatar, '/media/') || str_contains($kill->victim->avatar, '/static/images/account/avatar_default')))
                        <td class="px-1 py-2 lg:px-2 lg:py-3 text-center align-middle">
                            <a href="{{ route('player.show', ['name' => $kill->victim->name]) }}" class="inline-block transition-transform hover:scale-110">
                                <img width="40" height="40" alt="{{ $kill->victim->name }}" src="{{ $kill->victim->avatar }}" class="mx-auto align-middle rounded-full shadow-sm w-10 h-10 lg:w-12 lg:h-12" />
                            </a>
                        </td>
                    @else
                        <td class="px-1 py-2 lg:px-2 lg:py-3 text-center align-middle">
                            <a href="{{ route('player.show', ['name' => $kill->victim->name]) }}" class="inline-block transition-transform hover:scale-110">
                                <img width="40" height="40" src="https://cdn.robertsspaceindustries.com/static/images/account/avatar_default_big.jpg" alt="{{ $kill->victim->name }}" class="mx-auto align-middle rounded-full shadow-sm w-10 h-10 lg:w-12 lg:h-12" />
                            </a>
                        </td>
                    @endif
                    <td class="px-2 py-3 lg:px-6 lg:py-5 text-sm lg:text-base align-middle whitespace-nowrap">
                        <b><a href="{{ route('player.show', ['name' => $kill->victim->name]) }}" class="hover:underline transition-colors" style="color: rgb(var(--accent));">{{ $kill->victim->name }}</a></b>
                    </td>
                    @if($victimOrg && $victimOrg->spectrum_id !== Organization::ORG_NONE && $victimOrg->spectrum_id !== Organization::ORG_REDACTED)
                        <td class="px-2 py-3 lg:px-6 lg:py-5 text-sm lg:text-base text-center align-middle">
                            <a href="{{ route('organization.show', ['name' => $victimOrg->spectrum_id]) }}" class="inline-block transition-transform hover:scale-110">
                                <img width="50" height="50" src="{{ $victimOrg->icon }}" alt="{{ $victimOrg->name }}" class="mx-auto align-middle w-12 h-12 lg:w-16 lg:h-16 rounded-lg shadow-sm"/>
                            </a>
                        </td>
                    @elseif($victimOrg)
                        <td class="px-2 py-3 lg:px-6 lg:py-5 text-sm lg:text-base text-center align-middle">
                            <img width="50" height="50" src="{{ $victimOrg->icon }}" alt="{{ $victimOrg->name }}" class="mx-auto align-middle w-12 h-12 lg:w-16 lg:h-16 rounded-lg shadow-sm"/>
                        </td>
                    @else
                        <td class="px-2 py-3 lg:px-6 lg:py-5 text-sm lg:text-base text-center align-middle">
                            <img width="50" height="50" src="{{ $defaultOrg->icon }}" alt="{{ $defaultOrg->name }}" class="mx-auto align-middle w-12 h-12 lg:w-16 lg:h-16 rounded-lg shadow-sm"/>
                        </td>
                    @endif
                    @if($killType === Kill::TYPE_VEHICLE)
                        <td class="px-2 py-3 lg:px-6 lg:py-5 text-sm lg:text-base text-right align-middle font-medium max-w-[120px] lg:max-w-[180px]" style="color: rgb(var(--fg));">
                            {{ $kill->ship->name }}
                        </td>
                    @else
                        <td class="px-2 py-3 lg:px-6 lg:py-5 text-sm lg:text-base text-right align-middle whitespace-nowrap font-medium" style="color: rgb(var(--fg));">
                            FPS
                        </td>
                    @endif
                    @if (! empty($kill->killer->avatar) && (str_contains($kill->killer->avatar, '/media/') || str_contains($kill->killer->avatar, '/static/images/account/avatar_default')))
                        <td class="px-1 py-2 lg:px-2 lg:py-3 text-center align-middle">
                            <a href="{{ route('player.show', ['name' => $kill->killer->name]) }}" class="inline-block transition-transform hover:scale-110">
                                <img width="40" height="40" alt="{{ $kill->killer->name }}" src="{{ $kill->killer->avatar }}" class="mx-auto align-middle rounded-full shadow-sm w-10 h-10 lg:w-12 lg:h-12" />
                            </a>
                        </td>
                    @else
                        <td class="px-1 py-2 lg:px-2 lg:py-3 text-center align-middle">
                            <a href="{{ route('player.show', ['name' => $kill->killer->name]) }}" class="inline-block transition-transform hover:scale-110">
                                <img width="40" height="40" src="https://cdn.robertsspaceindustries.com/static/images/account/avatar_default_big.jpg" alt="{{ $kill->killer->name }}" class="mx-auto align-middle rounded-full shadow-sm w-10 h-10 lg:w-12 lg:h-12" />
                            </a>
                        </td>
                    @endif
                    <td class="px-2 py-3 lg:px-6 lg:py-5 text-sm lg:text-base align-middle whitespace-nowrap">
                        <b><a href="{{ route('player.show', ['name' => $kill->killer->name]) }}" class="hover:underline transition-colors" style="color: rgb(var(--accent));">{{ $kill->killer->name }}</a></b>
                    </td>
                    @if($killerOrg && $killerOrg->spectrum_id !== Organization::ORG_NONE && $killerOrg->spectrum_id !== Organization::ORG_REDACTED)
                        <td class="px-2 py-3 lg:px-6 lg:py-5 text-sm lg:text-base text-center align-middle">
                            <a href="{{ route('organization.show', ['name' => $killerOrg->spectrum_id]) }}" class="inline-block transition-transform hover:scale-110">
                                <img width="50" height="50" src="{{ $killerOrg->icon }}" alt="{{ $killerOrg->name }}" class="mx-auto align-middle w-12 h-12 lg:w-16 lg:h-16 rounded-lg shadow-sm"/>
                            </a>
                        </td>
                    @elseif($killerOrg)
                        <td class="px-2 py-3 lg:px-6 lg:py-5 text-sm lg:text-base text-center align-middle">
                            <img width="50" height="50" src="{{ $killerOrg->icon }}" alt="{{ $killerOrg->name }}" class="mx-auto align-middle w-12 h-12 lg:w-16 lg:h-16 rounded-lg shadow-sm"/>
                        </td>
                    @else
                        <td class="px-2 py-3 lg:px-6 lg:py-5 text-sm lg:text-base text-center align-middle">
                            <img width="50" height="50" src="{{ $defaultOrg->icon }}" alt="{{ $defaultOrg->name }}" class="mx-auto align-middle w-12 h-12 lg:w-16 lg:h-16 rounded-lg shadow-sm"/>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-10 text-center text-sm align-middle" style="color: rgb(var(--muted));">
                        {{ __('app.no_data_yet') }}
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t rounded-b-xl px-4 py-3 sm:px-6" style="border-color: rgb(var(--card-border)); background-color: rgb(var(--card));">
        {{ $kills->links() }}
    </div>
</div>
