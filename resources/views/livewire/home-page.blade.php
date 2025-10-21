<div class="container mx-auto px-2 py-2 text-gray-800 dark:text-gray-200 shadow-xl dark:shadow-2xl rounded-lg mt-8 mb-0">
    <header>
        <h1 class="text-3xl font-bold text-indigo-700 dark:text-indigo-400 p-2 text-shadow-md">
            Most Recent Kills (Last {{ config('killboard.home_page.most_recent_kills_days')  }} Days)
        </h1>
    </header>
    <div class="flex flex-col md:flex-row h-full">
        <div class="flex-1 py-4 h-full overflow-x-auto min-w-0 shadow">
            <livewire:main-kill-feed />
        </div>

        <div class="max-w-xl h-full p-4">
            <div class="space-y-4">
                <livewire:top-killer />
                <livewire:top-fps-killers />
                <livewire:top-orgs />
                <livewire:top-weapon />
                <livewire:top-victim />
                <livewire:top-fps-victims />
                <livewire:victim-orgs />
            </div>
        </div>
    </div>
</div>
