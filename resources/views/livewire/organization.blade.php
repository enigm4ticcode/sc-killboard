<div class="card p-4 sm:p-6 lg:p-8 mx-4 sm:mx-0">
    <header class="pb-6 mb-6 border-b" style="border-color: rgb(var(--card-border));">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-6">
            <div class="shrink-0">
                <a href="https://robertsspaceindustries.com/orgs/{{ $organization->spectrum_id }}" target="_blank" rel="noopener noreferrer" class="inline-block transition-transform hover:scale-105">
                    <img class="h-24 w-24 sm:h-32 sm:w-32 rounded-lg object-cover ring-4 ring-offset-4 shadow-lg transition-all" style="ring-color: rgb(var(--accent)); ring-offset-color: rgb(var(--card));" src="{{ $organization->icon }}" alt="{{ $organization->name }}">
                </a>
            </div>
            <div class="flex flex-col justify-center flex-1">
                <h1 class="text-2xl sm:text-3xl font-extrabold mb-2" style="color: rgb(var(--accent));">
                    <a href="https://robertsspaceindustries.com/orgs/{{ $organization->spectrum_id }}" target="_blank" rel="noopener noreferrer" class="hover:underline inline-flex items-center gap-2">
                        {{ $organization->name }}
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                    </a>
                </h1>
                <div class="text-base font-semibold mb-3" style="color: rgb(var(--fg));">
                    <span style="color: rgb(var(--success));">{{ __('app.kills') }}: {{ $totalKills }}</span> /
                    <span style="color: rgb(var(--danger));">{{ __('app.losses') }}: {{ $totalLosses }}</span>
                </div>
                <div class="text-sm font-semibold mb-2" style="color: rgb(var(--muted));">
                    {{ __('app.efficiency') }}
                </div>
                <div class="relative w-full rounded-full h-8 overflow-hidden shadow-inner" style="background-color: rgba(var(--danger), 0.2);" role="progressbar" aria-valuenow="{{ $efficiency }}" aria-valuemin="0" aria-valuemax="100">
                    <div class="h-full transition-all duration-500" style="width: {{ $efficiency }}%; background-color: rgb(var(--success));"></div>
                    <span class="absolute inset-0 flex items-center justify-center font-bold text-shadow-md" style="color: rgb(var(--fg));">
                        {{ $efficiency }}%
                    </span>
                </div>
            </div>
        </div>
    </header>

    <section>
        <h2 class="text-2xl font-bold mb-4" style="color: rgb(var(--fg));">
            {{ __('app.organization_activity', ['name' => $organization->name, 'days' => config('killboard.home_page.most_recent_kills_days')]) }}
        </h2>
        <livewire:generic-kill-feed :id="$organization->id" :data="$data" type="organization" />
    </section>
</div>
