<div class="container mx-auto px-2 py-2 text-gray-800 dark:text-gray-200 shadow-xl dark:shadow-2xl rounded-lg mt-8 mb-0">
    <div class="flex flex-col md:flex-row h-full">
        <div class="py-4 h-full min-w-full overflow-x-auto min-w-0">
            <div class="flex items-center space-x-4 p-8 min-w-full mx-auto bg-white dark:bg-gray-800 shadow-lg rounded-lg opacity-75">
                <div class="shrink-0">
                    <a href="https://robertsspaceindustries.com/citizens/{{ $player->name }}" target="_blank">
                        <img class="h-[100px] w-[100px] rounded-full object-cover" src="{{ $player->avatar }}" alt="{{ $player->name }}'s avatar">
                    </a>
                </div>
                <div class="flex flex-col justify-center">
                    <div class="text-lg font-bold">
                        <a href="https://robertsspaceindustries.com/citizens/{{ $player->name }}" target="_blank">{{ $player->name }}</a>
                    </div>
                    <div class="text-lg fond-bold">
                        <a href="https://robertsspaceindustries.com/orgs/{{ $player->organization->spectrum_id }}" target="_blank">{{ $player->organization->name }}</a>
                    </div>
                    <div class="text-md font-semibold">
                        Kills: {{ $totalKills }} / Losses: {{ $totalLosses }}
                    </div>
                    <div class="text-md font-semibold py-2">
                        <div class="relative w-full bg-red-600 dark:bg-red-400 rounded-full h-8 overflow-hidden" role="progressbar" aria-valuenow="{{ $efficiency }}" aria-valuemin="0" aria-valuemax="100">
                            <div class="bg-green-600 dark:bg-green-400 h-full transition-all duration-500" style="width: {{ $efficiency }}%;">
                            </div>
                            <span class="absolute inset-0 flex items-center justify-center text-white font-bold">
                                 {{ $efficiency }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <h1 class="text-3xl font-bold text-indigo-700 dark:text-indigo-400 text-shadow-md p-2">
                {{ $player->name }}'s Activity (Last {{ config('killboard.home_page.most_recent_kills_days')  }} Days)
            </h1>
            <livewire:generic-kill-feed :id="$player->id" :data="$data" />
        </div>
    </div>
</div>
