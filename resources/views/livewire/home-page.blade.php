<div class="min-h-screen w-full px-6 lg:px-10 py-8 overflow-y-auto">
    <h1 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-gray-100 text-center">
        {{ config('app.name') }}
    </h1>
    <div class="flex h-screen">
        <div class="flex-1 w-auto p-2 space-y-4">
            <livewire:main-kill-feed />
        </div>
        <div class="p-2 space-y-4">
            <div>
                <livewire:top-killer />
            </div>
            <div>
                <livewire:top-fps-killers />
            </div>
            <div>
                <livewire:top-orgs />
            </div>
            <div>
                <livewire:top-weapon />
            </div>
            <div>
                <livewire:top-victim />
            </div>
            <div>
                <livewire:top-fps-victims />
            </div>
            <div>
                <livewire:victim-orgs />
            </div>
        </div>
    </div>
</div>
