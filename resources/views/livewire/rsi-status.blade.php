<div class="card !rounded-xl overflow-hidden">
    <div class="border-b px-4 py-3 text-sm font-semibold" style="border-color: rgb(var(--card-border)); background-color: rgb(var(--table-header)); color: rgb(var(--fg));">
        🌐 {{ __('app.rsi_status') }}
    </div>

    <div class="px-4 py-5 sm:p-6">
        <dl class="grid grid-cols-1 gap-y-3">
            <div class="sm:col-span-1">
                <div class="flex items-center justify-between gap-x-4">
                    <dt class="text-sm font-medium" style="color: rgb(var(--muted));">
                        {{ __('app.platform') }}
                    </dt>
                    <dd class="text-sm">
                        <span @class([
                            'inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold shadow-sm',
                            'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-200 ring-1 ring-yellow-600/20' => $status['platform'] == 'degraded',
                            'bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200 ring-1 ring-green-600/20' => $status['platform'] == 'operational',
                            'bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-200 ring-1 ring-red-600/20' => $status['platform'] == 'outage',
                            'bg-gray-100 dark:bg-[#142034] text-gray-700 dark:text-slate-300 ring-1 ring-gray-600/20' => $status['platform'] == 'unknown',
                        ])>
                            {{ __('app.' . $status['platform']) }}
                        </span>
                    </dd>
                </div>
            </div>

            <div class="sm:col-span-1">
                <div class="flex items-center justify-between gap-x-4">
                    <dt class="text-sm font-medium" style="color: rgb(var(--muted));">
                        {{ __('app.persistent_universe') }}
                    </dt>
                    <dd class="text-sm">
                        <span @class([
                            'inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold shadow-sm',
                            'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-200 ring-1 ring-yellow-600/20' => $status['persistent_universe'] == 'degraded',
                            'bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200 ring-1 ring-green-600/20' => $status['persistent_universe'] == 'operational',
                            'bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-200 ring-1 ring-red-600/20' => $status['persistent_universe'] == 'outage',
                            'bg-gray-100 dark:bg-[#142034] text-gray-700 dark:text-slate-300 ring-1 ring-gray-600/20' => $status['persistent_universe'] == 'unknown',
                        ])>
                            {{ __('app.' . $status['persistent_universe']) }}
                        </span>
                    </dd>
                </div>
            </div>

            <div class="sm:col-span-1">
                <div class="flex items-center justify-between gap-x-4">
                    <dt class="text-sm font-medium" style="color: rgb(var(--muted));">
                        {{ __('app.arena_commander') }}
                    </dt>
                    <dd class="text-sm">
                        <span @class([
                            'inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold shadow-sm',
                            'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-200 ring-1 ring-yellow-600/20' => $status['arena_commander'] == 'degraded',
                            'bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200 ring-1 ring-green-600/20' => $status['arena_commander'] == 'operational',
                            'bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-200 ring-1 ring-red-600/20' => $status['arena_commander'] == 'outage',
                            'bg-gray-100 dark:bg-[#142034] text-gray-700 dark:text-slate-300 ring-1 ring-gray-600/20' => $status['arena_commander'] == 'unknown',
                        ])>
                            {{ __('app.' . $status['arena_commander']) }}
                        </span>
                    </dd>
                </div>
            </div>
        </dl>
    </div>
</div>
