<div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
    <div class="border-b border-gray-200 bg-gray-50 px-4 py-3 text-sm font-medium text-gray-700 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
        {{ __('RSI Status') }}
    </div>

    <div class="px-4 py-5 sm:p-6">
        <dl class="grid grid-cols-1 gap-x-4 gap-y-2">
            <div class="sm:col-span-1">
                <div class="flex items-center gap-x-4">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ __('Platform') }}
                    </dt>
                    <dd class="text-sm px-2">
                        <span @class([
                            'inline-flex items-center rounded-full px-3 py-1 text-sm font-medium',
                            'bg-yellow-100 dark:bg-yellow-900' => $status['platform'] == 'degraded',
                            'bg-green-100 dark:bg-green-900' => $status['platform'] == 'operational',
                            'bg-red-100 dark:bg-red-900' => $status['platform'] == 'outage',
                            'bg-gray-100 dark:bg-gray-700' => $status['platform'] == 'unknown',
                            'text-black dark:text-gray-900' => $status['platform'] != 'unknown',
                            'text-gray-800 dark:text-gray-300' => $status['platform'] == 'unknown',
                        ])>
                            {{ $status['platform']}}
                        </span>
                    </dd>
                </div>
            </div>

            <div class="sm:col-span-1">
                <div class="flex items-center gap-x-4">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ __('Persistent Universe') }}
                    </dt>
                    <dd class="text-sm px-2">
                        <span @class([
                            'inline-flex items-center rounded-full px-3 py-1 text-sm font-medium',
                            'bg-yellow-100 dark:bg-yellow-900' => $status['persistent_universe'] == 'degraded',
                            'bg-green-100 dark:bg-green-900' => $status['persistent_universe'] == 'operational',
                            'bg-red-100 dark:bg-red-900' => $status['persistent_universe'] == 'outage',
                            'bg-gray-100 dark:bg-gray-700' => $status['persistent_universe'] == 'unknown',
                            'text-black dark:text-gray-900' => $status['persistent_universe'] != 'unknown',
                            'text-gray-800 dark:text-gray-300' => $status['persistent_universe'] == 'unknown',
                        ])>
                            {{ $status['persistent_universe'] }}
                        </span>
                    </dd>
                </div>
            </div>

            <div class="sm:col-span-1">
                <div class="flex items-center gap-x-4">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ __('Arena Commander') }}
                    </dt>
                    <dd class="text-sm px-2">
                        <span @class([
                            'inline-flex items-center rounded-full px-3 py-1 text-sm font-medium',
                            'bg-yellow-100 dark:bg-yellow-900' => $status['arena_commander'] == 'degraded',
                            'bg-green-100 dark:bg-green-900' => $status['arena_commander'] == 'operational',
                            'bg-red-100 dark:bg-red-900' => $status['arena_commander'] == 'outage',
                            'bg-gray-100 dark:bg-gray-700' => $status['arena_commander'] == 'unknown',
                            'text-black dark:text-gray-900' => $status['arena_commander'] != 'unknown',
                            'text-gray-800 dark:text-gray-300' => $status['arena_commander'] == 'unknown',
                        ])>
                            {{ $status['arena_commander'] }}
                        </span>
                    </dd>
                </div>
            </div>
        </dl>
    </div>
</div>
