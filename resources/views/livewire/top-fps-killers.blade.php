<div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900">
    <div class="border-b border-gray-200 bg-gray-50 px-4 py-3 text-sm font-medium text-gray-700 dark:border-white/10 dark:bg-gray-800 dark:text-gray-200">
        Top {{ config('killboard.leaderboards.number-of-positions') }} FPS Killers (Last {{ config('killboard.leaderboards.timespan-days') }} Days)
    </div>
    <table class="min-w-full divide-y divide-gray-200 dark:divide-white/10">
        <thead class="bg-gray-50 dark:bg-gray-800">
        <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Rank</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Avatar</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Name</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Kills</th>
        </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white dark:divide-white/10 dark:bg-gray-900">
        @forelse ($killers as $i => $killer)
            <tr class="bg-indigo-200/20 dark:bg-indigo-900/20">
                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{ $i + 1 }}</td>
                @if ($killer->killer->avatar !== null)
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-300"><a href="{{ route('player.show', ['name' => $killer->killer->name]) }}" target="_blank"><img width="50" height="50" alt="{{ $killer->killer->name }}" src="{{ $killer->killer->avatar }}" /></a></td>
                @else
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-300"><img width="50" height="50" src="https://cdn.robertsspaceindustries.com/static/images/account/avatar_default_big.jpg" alt="No image" /></td>
                @endif
                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-300"><b><a href="{{ route('player.show', ['name' => $killer->killer->name]) }}" target="_blank">{{ $killer->killer->name }}</a></b></td>
                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{ $killer->kill_count }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No data yet.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
