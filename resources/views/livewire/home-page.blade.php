<div class="min-h-screen w-full px-6 lg:px-10 py-8">
    <h1 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">
        {{ config('app.name') }}
    </h1>
    <p class="mt-3 text-gray-600 dark:text-gray-300">
        Latest kills from around the 'verse.
    </p>

    <div class="flex h-screen">
        <div class="flex-1 p-2 space-y-4">
            @foreach($recentDates as $date)
                <livewire:panel-table date="{{ $date }}"/>
            @endforeach
        </div>
        <div class="w-[240px] p-2 space-y-4">
            <div>
                <livewire:top-killer />
            </div>
            <div>
                <livewire:top-weapon />
            </div>
            <div>
                <livewire:top-victim />
            </div>
        </div>
    </div>
</div>
