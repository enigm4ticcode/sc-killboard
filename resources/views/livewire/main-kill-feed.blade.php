@php use App\Models\Kill;use App\Models\Organization;use Carbon\Carbon; @endphp
<div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900">
    <div
        class="border-b border-gray-200 bg-gray-50 px-4 py-3 text-sm font-medium text-gray-700 dark:border-white/10 dark:bg-gray-800 dark:text-gray-200">
        Displaying Last {{ config('killboard.home_page.most_recent_kills_days') }} Days of Kills
    </div>
    <div class="bg-white dark:bg-gray-900 px-4 py-3 sm:px-6">
        {{ $kills->links() }}
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-white/10">
            <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
                <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                    {{ __('Date / Time (UTC)') }}
                </th>
                <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                    {{ __('Victim') }}
                </th>
                <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                    {{ __('Organization') }}
                </th>
                <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                    {{ __('Vehicle / FPS') }}
                </th>
                <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                    {{ __('Final Blow') }}
                </th>
                <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                    {{ __('Organization') }}
                </th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white dark:divide-white/10 dark:bg-gray-900">
            @forelse ($kills as $kill)
                @php($victimOrg = $kill->victim->organization)
                @php($killerOrg = $kill->killer->organization)
                @php($killType = $kill->type)
                @php($victimOrgIcon = ($victimOrg && ! empty($victimOrg->icon)) ? \Illuminate\Support\Str::contains($victimOrg->icon, 'http') ? $victimOrg->icon : 'https://robertsspaceindustries.com/' . $victimOrg->icon : 'https://cdn.robertsspaceindustries.com/static/images/Temp/default-image.png')
                )
                @php($killerOrgIcon = ($killerOrg && ! empty($killerOrg->icon)) ? \Illuminate\Support\Str::contains($killerOrg->icon, 'http') ? $killerOrg->icon : 'https://robertsspaceindustries.com/' . $killerOrg->icon : 'https://cdn.robertsspaceindustries.com/static/images/Temp/default-image.png')
                )
                <tr style="{{ $killType !== Kill::TYPE_VEHICLE ? 'background-color: indigo' : '' }}">
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ Carbon::parse($kill->destroyed_at)->format('D, M j Y H:i') }}</td>
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-300"><b><a
                                href="https://robertsspaceindustries.com/citizens/{{ $kill->victim->name }}"
                                target="_blank">{{ $kill->victim->name }}</a></b></td>
                    @if($victimOrg->spectrum_id !== Organization::ORG_NONE && $victimOrg->spectrum_id !== Organization::ORG_REDACTED)
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-300"><a
                                href="https://robertsspaceindustries.com/orgs/{{ $victimOrg->spectrum_id }}"
                                target="_blank"><img width="50" height="50" src="{{ $victimOrgIcon }}"
                                                     alt="{{ $victimOrg->name }}"/></a></td>
                    @else
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-300"><img width="50" height="50" src="{{ $victimOrgIcon }}" alt="{{ $victimOrg->name }}"/>
                        </td>
                    @endif
                    @if($killType === Kill::TYPE_VEHICLE)
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $kill->ship->name }}</td>
                    @else
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">FPS</td>
                    @endif
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-300"><b><a
                                href="https://robertsspaceindustries.com/citizens/{{ $kill->killer->name }}"
                                target="_blank">{{ $kill->killer->name }}</a></b></td>
                    @if($killerOrg->spectrum_id !== Organization::ORG_NONE && $killerOrg->spectrum_id !== Organization::ORG_REDACTED)
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-300"><a
                                href="https://robertsspaceindustries.com/orgs/{{ $killerOrg->spectrum_id }}"
                                target="_blank"><img width="50" height="50" src="{{ $killerOrgIcon }}"
                                                     alt="{{ $killerOrg->name }}"/></a></td>
                    @else
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-300"><img width="50" height="50" src="{{ $killerOrgIcon }}" alt="None"/>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No data
                        yet.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div
        class="border-t border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900 px-4 py-3 sm:px-6">
        {{ $kills->links() }}
    </div>
</div>
