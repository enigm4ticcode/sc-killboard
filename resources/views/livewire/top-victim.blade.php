<div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900">
    <div class="border-b border-gray-200 bg-gray-50 px-4 py-3 text-sm font-medium text-gray-700 dark:border-white/10 dark:bg-gray-800 dark:text-gray-200">
        Top 10 Victims Last 7 Days
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-white/10">
            <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Rank</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Avatar</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Name</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Deaths</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white dark:divide-white/10 dark:bg-gray-900">
            @forelse ($victims as $i => $victim)
                <tr>
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{ $i + 1 }}</td>
                    @if ($victim->victim->avatar !== null)
                        @php($avatarUrl = \Illuminate\Support\Str::contains($victim->victim->avatar, 'http') ? $victim->victim->avatar : 'https://robertsspaceindustries.com/' . $victim->victim->avatar)
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-300"><a href="https://robertsspaceindustries.com/citizens/{{ $victim->victim->name }}" target="_blank"><img width="50" height="50" alt="{{ $victim->victim->name }}" src="{{ $avatarUrl }}" /></a></td>
                    @else
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-300">No Image</td>
                    @endif
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-300"><b><a href="https://robertsspaceindustries.com/citizens/{{ $victim->victim->name }}" target="_blank">{{ $victim->victim->name }}</a></b></td>
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{ $victim->death_count }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No data yet.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>w
</div>
