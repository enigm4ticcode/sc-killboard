<div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900">
    <div class="border-b border-gray-200 bg-gray-50 px-4 py-3 text-sm font-medium text-gray-700 dark:border-white/10 dark:bg-gray-800 dark:text-gray-200">
        Top 10 Orgs Last 7 Days
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-white/10">
            <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Rank</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Logo</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Avg. Kills Per Player</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Total. Kills</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white dark:divide-white/10 dark:bg-gray-900">
            @forelse ($orgs as $i => $org)
                <tr>
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{ $i + 1 }}</td>
                    @if ($org->organization_icon !== null)
                        @php($iconUrl = \Illuminate\Support\Str::contains($org->organization_icon, 'http') ? $org->organization_icon : 'https://robertsspaceindustries.com/' . $org->organization_icon)
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-300"><a href="https://robertsspaceindustries.com/organizations/{{ $org->organization_name }}" target="_blank"><img width="50" height="50" alt="{{ $org->organization_name }}" src="{{ $iconUrl }}" /></a></td>
                    @else
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-300"><img width="50" height="50" src="https://cdn.robertsspaceindustries.com/static/images/account/avatar_default_big.jpg" alt="No image" /></td>
                    @endif
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{ \Illuminate\Support\Number::format($org->average_kills_per_player, 2) }}</td>
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{ $org->total_kills }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No data yet.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
