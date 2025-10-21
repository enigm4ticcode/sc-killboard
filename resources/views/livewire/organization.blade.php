<div class="container mx-auto px-2 py-2 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 shadow-xl dark:shadow-2xl rounded-lg mt-8 mb-0">
    <div class="flex flex-col md:flex-row h-full">
        <div class="py-4 h-full min-w-full overflow-x-auto min-w-0">
            <div class="flex items-center space-x-4 p-8 min-w-full mx-auto bg-white dark:bg-gray-800 shadow-lg rounded-lg">
                <div class="flex-shrink-0">
                    <a href="https://robertsspaceindustries.com/orgs/{{ $organization->spectrum_id }}" target="_blank">
                        <img class="h-[100px] w-[100px] rounded-full object-cover" src="{{ $organization->icon }}" alt="{{ $organization->name }}'s logo">
                    </a>
                </div>
                <div class="flex flex-col justify-center">
                    <div class="text-lg font-bold">
                        <a href="https://robertsspaceindustries.com/orgs/{{ $organization->spectrum_id }}" target="_blank">{{ $organization->name }}</a>
                    </div>
                </div>
            </div>
            <h1 class="text-3xl font-bold text-indigo-700 dark:text-indigo-400 p-2">
                {{ $organization->name }} Activity (Last {{ config('killboard.home_page.most_recent_kills_days')  }} Days)
            </h1>
            <livewire:generic-kill-feed :id="$organization->id" :data="$data" type="organization" />
        </div>
    </div>
</div>
