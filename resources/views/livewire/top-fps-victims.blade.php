<div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900">

    <div class="border-b border-gray-200 bg-gray-50 px-4 py-3 text-sm font-medium text-gray-700 dark:border-white/10 dark:bg-gray-800 dark:text-gray-200">
        Top {{ config('killboard.leaderboards.number-of-positions') }} FPS Victims (Last {{ config('killboard.leaderboards.timespan-days') }} Days)
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-white/10">
            <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
                <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300 align-middle">Rank</th>
                <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300 align-middle">Avatar</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300 align-middle">Name</th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300 align-middle">Deaths</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white dark:divide-white/10 dark:bg-gray-900">
            @forelse ($victims as $i => $victim)
                <tr class="bg-indigo-200/20 dark:bg-indigo-900/20">
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200 text-center align-middle whitespace-nowrap font-mono">{{ $i + 1 }}</td>
                    @if ($victim->victim->avatar !== null)
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300 text-center align-middle">
                            <a href="{{ route('player.show', ['name' => $victim->victim->name]) }}">
                                <img width="50" height="50" alt="{{ $victim->victim->name }}" src="{{ $victim->victim->avatar }}" class="mx-auto align-middle" />
                            </a>
                        </td>
                    @else
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300 text-center align-middle">
                            <img width="50" height="50" src="https://cdn.robertsspaceindustries.com/static/images/account/avatar_default_big.jpg" alt="No image" class="mx-auto align-middle" />
                        </td>
                    @endif
                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300 align-middle whitespace-nowrap">
                        <b><a href="{{ route('player.show', ['name' => $victim->victim->name]) }}">{{ $victim->victim->name }}</a></b>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200 text-right align-middle whitespace-nowrap font-mono">{{ $victim->death_count }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400 align-middle">No data yet.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
