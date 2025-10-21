@php use App\Models\Kill;use App\Models\Organization;use Carbon\Carbon; @endphp
<div>
    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900">
        <div class="px-4 py-2 sm:px-6 border-b border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-800">
            {{ $feed->links() }}
        </div>

        <table class="min-w-full divide-y divide-gray-200 dark:divide-white/10">
            <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
                <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300 rounded-tl-lg">
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
                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300 rounded-tr-lg">
                    {{ __('Organization') }}
                </th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white dark:divide-white/10 dark:bg-gray-900">
            @forelse ($feed as $kill)
                @php($victimOrg = $kill->victim->organization)
                @php($killerOrg = $kill->killer->organization)
                @php($killType = $kill->type)
                @if ($type === 'organization')
                    <tr class="{{ $id === $victimOrg->id ? 'bg-red-100/20 dark:bg-red-900/20' : 'bg-green-200/20 dark:bg-green-900/20' }}">
                @else
                    <tr class="{{ $id === $kill->victim->id ? 'bg-red-100/20 dark:bg-red-900/20' : 'bg-green-200/20 dark:bg-green-900/20' }}">
                @endif
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ Carbon::parse($kill->destroyed_at)->format('D, M j Y H:i') }}</td>
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-300"><b><a
                                href="{{ route('player.show', ['name' => $kill->victim->name]) }}"
                               >{{ $kill->victim->name }}</a></b></td>
                    @if($victimOrg->spectrum_id !== Organization::ORG_NONE && $victimOrg->spectrum_id !== Organization::ORG_REDACTED)
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-300"><a
                                href="{{ route('organization.show', ['name' => $victimOrg->spectrum_id]) }}"
                               ><img width="50" height="50" src="{{ $victimOrg->icon }}"
                                                     alt="{{ $victimOrg->name }}"/></a></td>
                    @else
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-300"><img width="50" height="50" src="{{ $victimOrg->icon }}" alt="{{ $victimOrg->name }}"/>
                        </td>
                    @endif
                    @if($killType === Kill::TYPE_VEHICLE)
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $kill->ship->name }}</td>
                    @else
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">FPS</td>
                    @endif
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-300"><b><a href="{{ route('player.show', ['name' => $kill->killer->name]) }}">{{ $kill->killer->name }}</a></b></td>
                    @if($killerOrg->spectrum_id !== Organization::ORG_NONE && $killerOrg->spectrum_id !== Organization::ORG_REDACTED)
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-300"><a href="{{ route('organization.show', ['name' => $killerOrg->spectrum_id]) }}"><img width="50" height="50" src="{{ $killerOrg->icon }}" alt="{{ $killerOrg->name }}"/></a></td>
                    @else
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-300"><img width="50" height="50" src="{{ $killerOrg->icon }}" alt="None"/>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No data
                        yet.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="border-t border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-800 rounded-b-lg px-4 py-3 sm:px-6">
            {{ $feed->links() }}
        </div>
    </div>
</div>
