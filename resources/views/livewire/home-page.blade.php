<div class="container mx-auto p-2 text-gray-800 dark:text-gray-200 rounded-lg mt-8 mb-0">
    <header>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-indigo-700 dark:text-indigo-400 p-2 text-shadow-md">
            Most Recent Kills (Last {{ config('killboard.home_page.most_recent_kills_days')  }} Days)
        </h1>
        <p class="text-gray-600 dark:text-gray-400 text-sm px-2">
            (Updated Live)
        </p>
    </header>
    <div class="flex flex-col md:flex-row h-full">
        <section class="flex-1 py-4 h-full overflow-x-auto">
            <livewire:main-kill-feed />
        </section>

        <section class="p-4 space-y-4 h-full w-full md:w-xs lg:w-md flex-none">
            <livewire:rsi-status />
            <livewire:top-killer />
            <livewire:top-fps-killers />
            <livewire:top-orgs />
            <livewire:top-weapon />
            <livewire:top-victim />
            <livewire:top-fps-victims />
            <livewire:victim-orgs />
        </section>
    </div>
</div>
