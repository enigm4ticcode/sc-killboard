@php use App\Models\Kill;use App\Models\Organization;use Carbon\Carbon;use Illuminate\Support\Str;
$defaultOrg = Organization::where('spectrum_id', Organization::ORG_NONE)->first();
@endphp
<div class="overflow-hidden rounded-none sm:rounded-xl border border-l-0 border-r-0 sm:border shadow-lg transition-all duration-300" style="background-color: rgb(var(--card)); border-color: rgb(var(--card-border));">

    <div class="px-2 sm:px-4 md:px-6 py-3 border-b" style="border-color: rgb(var(--card-border)); background-color: rgb(var(--card));">
        {{ $feed->links() }}
    </div>

    {{-- Desktop Table Layout --}}
    <div class="overflow-x-auto desktop-table">
        <style>
            .desktop-table { display: none; }
            @media (min-width: 1280px) {
                .desktop-table { display: block; }
            }
            /* Prevent avatar column compression */
            .avatar-cell {
                width: 80px;
                min-width: 80px;
                max-width: 80px;
            }
            /* Prevent org icon column compression */
            .org-cell {
                width: 120px;
                min-width: 120px;
                max-width: 120px;
            }
        </style>
        <table class="min-w-full divide-y table-fixed" style="divide-color: rgb(var(--card-border));">
            <colgroup>
                <col style="width: 120px;"> {{-- Time --}}
                <col style="width: auto; min-width: 150px;"> {{-- Vehicle/FPS --}}
                <col class="avatar-cell"> {{-- Victim Avatar --}}
                <col style="width: auto;"> {{-- Victim Name --}}
                <col class="org-cell"> {{-- Victim Org --}}
                <col class="avatar-cell"> {{-- Killer Avatar --}}
                <col style="width: auto;"> {{-- Killer Name --}}
                <col class="org-cell"> {{-- Killer Org --}}
            </colgroup>
            <thead style="background-color: rgb(var(--table-header));">
            <tr>
                <th scope="col"
                    class="px-2 py-3 lg:px-6 lg:py-4 text-right text-xs lg:text-sm font-semibold uppercase tracking-wider align-middle"
                    style="color: rgb(var(--muted));">
                    {{ __('app.time_utc') }}
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
                    {{ __('app.victim') }}
                </th>
                <th scope="col"
                    class="px-2 py-3 lg:px-6 lg:py-4 text-center text-xs lg:text-sm font-semibold uppercase tracking-wider align-middle"
                    style="color: rgb(var(--muted));">
                    {{ __('app.organization') }}
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
            @forelse ($feed as $kill)
                @php($victimOrg = $kill->victim->organization)
                @php($killerOrg = $kill->killer->organization)
                @php($killType = $kill->type)
                @php($killDate = Carbon::parse($kill->destroyed_at)->format('Y-m-d'))
                @php($displayDate = Carbon::parse($kill->destroyed_at)->format('l, F j, Y'))

                @if($currentDate !== $killDate)
                    @php($currentDate = $killDate)
                    <tr style="background: linear-gradient(90deg, rgba(var(--accent), 0.15) 0%, rgba(var(--accent), 0.05) 100%);">
                        <td colspan="8" class="px-6 py-4 text-sm font-bold border-y" style="color: rgb(var(--accent)); border-color: rgba(var(--accent), 0.3);">
                            <div class="flex items-center gap-2">
                                <span class="text-lg">📅</span>
                                <span>{{ $displayDate }}</span>
                            </div>
                        </td>
                    </tr>
                @endif

                @if ($type === 'organization')
                    <tr class="transition-colors" style="background-color: {{ ($victimOrg && $id === $victimOrg->id) ? 'rgba(239, 68, 68, 0.08)' : 'rgba(34, 197, 94, 0.08)' }};" onmouseover="this.style.backgroundColor='rgb(var(--table-hover))'" onmouseout="this.style.backgroundColor='{{ ($victimOrg && $id === $victimOrg->id) ? 'rgba(239, 68, 68, 0.08)' : 'rgba(34, 197, 94, 0.08)' }}'">
                @else
                    <tr class="transition-colors" style="background-color: {{ $id === $kill->victim->id ? 'rgba(239, 68, 68, 0.08)' : 'rgba(34, 197, 94, 0.08)' }};" onmouseover="this.style.backgroundColor='rgb(var(--table-hover))'" onmouseout="this.style.backgroundColor='{{ $id === $kill->victim->id ? 'rgba(239, 68, 68, 0.08)' : 'rgba(34, 197, 94, 0.08)' }}'">
                @endif
                        <td class="px-2 py-3 lg:px-6 lg:py-5 text-right align-middle whitespace-nowrap">
                            <span class="inline-block px-2 py-1 text-xs font-medium rounded" style="color: rgb(var(--fg)); background-color: rgba(var(--fg), 0.08); font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; letter-spacing: 0.05em;">
                                {{ Carbon::parse($kill->destroyed_at)->format('H:i:s') }}
                            </span>
                        </td>
                        @if($killType === Kill::TYPE_VEHICLE)
                            <td class="px-2 py-3 lg:px-6 lg:py-5 text-center align-middle">
                                <div class="flex flex-col items-center gap-2">
                                    @if($kill->ship->icon)
                                        <img src="{{ $kill->ship->icon_url ?? $kill->ship->icon }}" alt="{{ $kill->ship->name }}" class="mx-auto align-middle w-28 h-16 rounded-lg" style="box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);"/>
                                    @else
                                        <img src="{{ Vite::asset('resources/images/ships/default.jpg') }}" alt="{{ $kill->ship->name }}" class="mx-auto align-middle w-28 h-16 rounded-lg" style="box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);"/>
                                    @endif
                                </div>
                            </td>
                        @else
                            <td class="px-2 py-3 lg:px-6 lg:py-5 text-center align-middle">
                                <span class="inline-block px-3 py-1.5 text-sm font-semibold rounded-md" style="color: rgb(var(--fg)); background-color: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.3);">
                                    FPS
                                </span>
                            </td>
                        @endif
                        @if (! empty($kill->victim->avatar) && (str_contains($kill->victim->avatar, '/media/') || str_contains($kill->victim->avatar, '/static/images/account/avatar_default')))
                            <td class="px-2 py-3 text-center align-middle">
                                <a href="{{ route('player.show', ['name' => $kill->victim->name]) }}" class="inline-block transition-transform hover:scale-110">
                                    <img width="56" height="56" alt="{{ $kill->victim->name }}" src="{{ $kill->victim->avatar }}" class="mx-auto rounded-full w-14 h-14 object-cover" style="box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);" />
                                </a>
                            </td>
                        @else
                            <td class="px-2 py-3 text-center align-middle">
                                <a href="{{ route('player.show', ['name' => $kill->victim->name]) }}" class="inline-block transition-transform hover:scale-110">
                                    <img width="56" height="56" src="https://cdn.robertsspaceindustries.com/static/images/account/avatar_default_big.jpg" alt="{{ $kill->victim->name }}" class="mx-auto rounded-full w-14 h-14 object-cover" style="box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);" />
                                </a>
                            </td>
                        @endif
                        <td class="px-2 py-3 lg:px-6 lg:py-5 text-sm lg:text-base align-middle whitespace-nowrap">
                            <b><a href="{{ route('player.show', ['name' => $kill->victim->name]) }}" class="hover:underline transition-colors" style="color: rgb(var(--accent));">{{ $kill->victim->name }}</a></b>
                        </td>
                        @if($victimOrg && $victimOrg->spectrum_id !== Organization::ORG_NONE && $victimOrg->spectrum_id !== Organization::ORG_REDACTED)
                            <td class="px-2 py-3 lg:px-6 lg:py-5 text-center align-middle">
                                <a href="{{ route('organization.show', ['name' => $victimOrg->spectrum_id]) }}" class="inline-block transition-all hover:scale-110">
                                    <img width="64" height="64" src="{{ $victimOrg->icon }}" alt="{{ $victimOrg->name }}" class="mx-auto align-middle w-16 h-16 rounded-lg" style="box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);"/>
                                </a>
                            </td>
                        @elseif($victimOrg)
                            <td class="px-2 py-3 lg:px-6 lg:py-5 text-center align-middle">
                                <img width="64" height="64" src="{{ $victimOrg->icon }}" alt="{{ $victimOrg->name }}" class="mx-auto align-middle w-16 h-16 rounded-lg" style="box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);"/>
                            </td>
                        @else
                            <td class="px-2 py-3 lg:px-6 lg:py-5 text-center align-middle">
                                <img width="64" height="64" src="{{ $defaultOrg->icon }}" alt="{{ $defaultOrg->name }}" class="mx-auto align-middle w-16 h-16 rounded-lg" style="box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);"/>
                            </td>
                        @endif
                        @if (! empty($kill->killer->avatar) && (str_contains($kill->killer->avatar, '/media/') || str_contains($kill->killer->avatar, '/static/images/account/avatar_default')))
                            <td class="px-2 py-3 text-center align-middle">
                                <a href="{{ route('player.show', ['name' => $kill->killer->name]) }}" class="inline-block transition-transform hover:scale-110">
                                    <img width="56" height="56" alt="{{ $kill->killer->name }}" src="{{ $kill->killer->avatar }}" class="mx-auto rounded-full w-14 h-14 object-cover" style="box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);" />
                                </a>
                            </td>
                        @else
                            <td class="px-2 py-3 text-center align-middle">
                                <a href="{{ route('player.show', ['name' => $kill->killer->name]) }}" class="inline-block transition-transform hover:scale-110">
                                    <img width="56" height="56" src="https://cdn.robertsspaceindustries.com/static/images/account/avatar_default_big.jpg" alt="{{ $kill->killer->name }}" class="mx-auto rounded-full w-14 h-14 object-cover" style="box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);" />
                                </a>
                            </td>
                        @endif
                        <td class="px-2 py-3 lg:px-6 lg:py-5 text-sm lg:text-base align-middle whitespace-nowrap">
                            <b><a href="{{ route('player.show', ['name' => $kill->killer->name]) }}" class="hover:underline transition-colors" style="color: rgb(var(--accent));">{{ $kill->killer->name }}</a></b>
                        </td>
                        @if($killerOrg && $killerOrg->spectrum_id !== Organization::ORG_NONE && $killerOrg->spectrum_id !== Organization::ORG_REDACTED)
                            <td class="px-2 py-3 lg:px-6 lg:py-5 text-center align-middle">
                                <a href="{{ route('organization.show', ['name' => $killerOrg->spectrum_id]) }}" class="inline-block transition-all hover:scale-110">
                                    <img width="64" height="64" src="{{ $killerOrg->icon }}" alt="{{ $killerOrg->name }}" class="mx-auto align-middle w-16 h-16 rounded-lg" style="box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);"/>
                                </a>
                            </td>
                        @elseif($killerOrg)
                            <td class="px-2 py-3 lg:px-6 lg:py-5 text-center align-middle">
                                <img width="64" height="64" src="{{ $killerOrg->icon }}" alt="{{ $killerOrg->name }}" class="mx-auto align-middle w-16 h-16 rounded-lg" style="box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);"/>
                            </td>
                        @else
                            <td class="px-2 py-3 lg:px-6 lg:py-5 text-center align-middle">
                                <img width="64" height="64" src="{{ $defaultOrg->icon }}" alt="{{ $defaultOrg->name }}" class="mx-auto align-middle w-16 h-16 rounded-lg" style="box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);"/>
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

    {{-- Mobile Card Layout --}}
    <div class="mobile-cards">
        <style>
            .mobile-cards { display: block; }
            @media (min-width: 1280px) {
                .mobile-cards { display: none; }
            }
        </style>
        @php($currentDateMobile = null)
        @forelse ($feed as $kill)
            @php($victimOrg = $kill->victim->organization)
            @php($killerOrg = $kill->killer->organization)
            @php($killType = $kill->type)
            @php($killDate = Carbon::parse($kill->destroyed_at)->format('Y-m-d'))
            @php($displayDate = Carbon::parse($kill->destroyed_at)->format('l, F j, Y'))

            @if($currentDateMobile !== $killDate)
                @php($currentDateMobile = $killDate)
                <div class="px-4 py-3 text-sm font-bold border-t border-b" style="color: rgb(var(--accent)); border-color: rgb(var(--card-border)); background-color: rgb(var(--bg));">
                    📅 {{ $displayDate }}
                </div>
            @endif

            <div class="border-b transition-colors p-4" style="background-color: {{ $type === 'organization' ? (($victimOrg && $id === $victimOrg->id) ? 'rgba(239, 68, 68, 0.08)' : 'rgba(34, 197, 94, 0.08)') : ($id === $kill->victim->id ? 'rgba(239, 68, 68, 0.08)' : 'rgba(34, 197, 94, 0.08)') }}; border-color: rgb(var(--card-border));">
                {{-- Time --}}
                <div class="text-xs font-mono text-center mb-3" style="color: rgb(var(--muted));">
                    {{ Carbon::parse($kill->destroyed_at)->format('H:i') }}
                </div>

                {{-- Kill Info Container --}}
                <div class="flex items-center justify-center gap-2">
                    {{-- Victim Side --}}
                    <div class="flex flex-col items-center gap-2 flex-1 min-w-0">
                        @if (! empty($kill->victim->avatar) && (str_contains($kill->victim->avatar, '/media/') || str_contains($kill->victim->avatar, '/static/images/account/avatar_default')))
                            <a href="{{ route('player.show', ['name' => $kill->victim->name]) }}" class="transition-transform hover:scale-110">
                                <img width="48" height="48" alt="{{ $kill->victim->name }}" src="{{ $kill->victim->avatar }}" class="rounded-full shadow-md w-12 h-12 object-cover flex-shrink-0" />
                            </a>
                        @else
                            <a href="{{ route('player.show', ['name' => $kill->victim->name]) }}" class="transition-transform hover:scale-110">
                                <img width="48" height="48" src="https://cdn.robertsspaceindustries.com/static/images/account/avatar_default_big.jpg" alt="{{ $kill->victim->name }}" class="rounded-full shadow-md w-12 h-12 object-cover flex-shrink-0" />
                            </a>
                        @endif
                        <a href="{{ route('player.show', ['name' => $kill->victim->name]) }}" class="text-xs font-semibold text-center hover:underline transition-colors" style="color: rgb(var(--accent));">
                            {{ Str::limit($kill->victim->name, 20) }}
                        </a>
                        @if($victimOrg && $victimOrg->spectrum_id !== Organization::ORG_NONE && $victimOrg->spectrum_id !== Organization::ORG_REDACTED)
                            <a href="{{ route('organization.show', ['name' => $victimOrg->spectrum_id]) }}" class="transition-transform hover:scale-110">
                                <img width="32" height="32" src="{{ $victimOrg->icon }}" alt="{{ $victimOrg->name }}" class="w-8 h-8 rounded-lg shadow-sm"/>
                            </a>
                        @elseif($victimOrg)
                            <img width="32" height="32" src="{{ $victimOrg->icon }}" alt="{{ $victimOrg->name }}" class="w-8 h-8 rounded-lg shadow-sm"/>
                        @else
                            <img width="32" height="32" src="{{ $defaultOrg->icon }}" alt="{{ $defaultOrg->name }}" class="w-8 h-8 rounded-lg shadow-sm"/>
                        @endif
                    </div>

                    {{-- VS Indicator / Ship Info --}}
                    <div class="flex flex-col items-center gap-1 flex-shrink-0 w-28">
                        @if($killType === Kill::TYPE_VEHICLE)
                            @if($kill->ship->icon)
                                <img src="{{ $kill->ship->icon_url ?? $kill->ship->icon }}" alt="{{ $kill->ship->name }}" class="w-28 h-16 object-contain rounded-lg" style="box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);" />
                            @else
                                <img src="{{ Vite::asset('resources/images/ships/default.jpg') }}" alt="{{ $kill->ship->name }}" class="w-28 h-16 object-contain rounded-lg" style="box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);" />
                            @endif
                            <div class="text-xs font-medium px-2 py-1 rounded text-center" style="color: rgb(var(--fg)); background-color: rgba(var(--fg), 0.1); word-break: break-word;">
                                {{ $kill->ship->name }}
                            </div>
                        @else
                            <div class="text-2xl">⚔️</div>
                            <div class="text-xs font-medium px-2 py-1 rounded text-center" style="color: rgb(var(--fg)); background-color: rgba(var(--fg), 0.1);">
                                FPS
                            </div>
                        @endif
                    </div>

                    {{-- Killer Side --}}
                    <div class="flex flex-col items-center gap-2 flex-1 min-w-0">
                        @if (! empty($kill->killer->avatar) && (str_contains($kill->killer->avatar, '/media/') || str_contains($kill->killer->avatar, '/static/images/account/avatar_default')))
                            <a href="{{ route('player.show', ['name' => $kill->killer->name]) }}" class="transition-transform hover:scale-110">
                                <img width="48" height="48" alt="{{ $kill->killer->name }}" src="{{ $kill->killer->avatar }}" class="rounded-full shadow-md w-12 h-12 object-cover flex-shrink-0" />
                            </a>
                        @else
                            <a href="{{ route('player.show', ['name' => $kill->killer->name]) }}" class="transition-transform hover:scale-110">
                                <img width="48" height="48" src="https://cdn.robertsspaceindustries.com/static/images/account/avatar_default_big.jpg" alt="{{ $kill->killer->name }}" class="rounded-full shadow-md w-12 h-12 object-cover flex-shrink-0" />
                            </a>
                        @endif
                        <a href="{{ route('player.show', ['name' => $kill->killer->name]) }}" class="text-xs font-semibold text-center hover:underline transition-colors" style="color: rgb(var(--accent));">
                            {{ Str::limit($kill->killer->name, 20) }}
                        </a>
                        @if($killerOrg && $killerOrg->spectrum_id !== Organization::ORG_NONE && $killerOrg->spectrum_id !== Organization::ORG_REDACTED)
                            <a href="{{ route('organization.show', ['name' => $killerOrg->spectrum_id]) }}" class="transition-transform hover:scale-110">
                                <img width="32" height="32" src="{{ $killerOrg->icon }}" alt="{{ $killerOrg->name }}" class="w-8 h-8 rounded-lg shadow-sm"/>
                            </a>
                        @elseif($killerOrg)
                            <img width="32" height="32" src="{{ $killerOrg->icon }}" alt="{{ $killerOrg->name }}" class="w-8 h-8 rounded-lg shadow-sm"/>
                        @else
                            <img width="32" height="32" src="{{ $defaultOrg->icon }}" alt="{{ $defaultOrg->name }}" class="w-8 h-8 rounded-lg shadow-sm"/>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="px-6 py-10 text-center text-sm" style="color: rgb(var(--muted));">
                {{ __('app.no_data_yet') }}
            </div>
        @endforelse
    </div>

    <div class="border-t rounded-b-none sm:rounded-b-xl px-4 py-3 sm:px-6" style="border-color: rgb(var(--card-border)); background-color: rgb(var(--card));">
        {{ $feed->links() }}
    </div>
</div>
