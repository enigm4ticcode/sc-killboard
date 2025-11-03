<div class="w-full mt-8 mb-0" style="color: rgb(var(--fg));">
    <header class="mb-6 px-4 sm:px-6 lg:px-8 max-w-[1920px] mx-auto">
        <h1 class="text-3xl sm:text-4xl font-extrabold mb-2" style="color: rgb(var(--accent));">
            ⚔️ {{ __('app.most_recent_kills', ['days' => config('killboard.home_page.most_recent_kills_days')]) }}
        </h1>
        <p class="text-base" style="color: rgb(var(--muted));">
            {{ __('app.updated_live') }}
        </p>
    </header>
    <div class="flex flex-col lg:flex-row h-full lg:gap-4 lg:max-w-[1920px] lg:mx-auto lg:px-4">
        <section class="flex-1 py-4 h-full overflow-x-auto">
            <livewire:main-kill-feed />
        </section>

        <section class="px-4 sm:px-6 lg:px-0 py-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 gap-4 h-full w-full lg:w-96 xl:w-[28rem] flex-none">
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
