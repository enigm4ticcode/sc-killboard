<div class="card p-4 sm:p-6 lg:p-8 max-w-2xl mx-4 sm:mx-auto">
    <form class="space-y-6" wire:submit.prevent="save">
        <header class="pb-6 mb-6 border-b" style="border-color: rgb(var(--card-border));">
            <h1 class="text-3xl sm:text-4xl font-extrabold mb-2" style="color: rgb(var(--accent));">
                {{ __('app.verify_rsi_account') }}
            </h1>
            <p class="text-base" style="color: rgb(var(--muted));">
                {{ __('app.verification_description') }}
            </p>
        </header>

        <div class="space-y-4">
            <p style="color: rgb(var(--muted));">
                {!! __('app.verification_step_1', [
                    'link' => '<a href="https://robertsspaceindustries.com/account/profile" target="_blank" class="font-medium underline" style="color: rgb(var(--accent));" onmouseover="this.style.opacity=\'0.8\'" onmouseout="this.style.opacity=\'1\'">' . __('app.roberts_space_industries_profile') . '</a>',
                    'strong' => '<strong class="font-semibold">' . __('app.exactly') . '</strong>'
                ]) !!}
            </p>

            <div id="code" class="flex items-center justify-between p-4 border border-dashed rounded-lg shadow-inner" style="background-color: rgb(var(--bg)); border-color: rgb(var(--card-border));">
                <pre id="copyTarget" class="text-md font-mono text-shadow-md overflow-x-auto whitespace-pre-wrap break-all mr-4" style="color: rgb(var(--warning));">[sc-killboard: {{ $verificationKey }}]</pre>

                <div class="flex items-center space-x-2">
                    <span id="successMessage" class="hidden text-sm font-semibold transition-opacity duration-300" style="color: rgb(var(--success));">
                        {{ __('app.copied') }}
                    </span>

                    <button type="button" onclick="copyCode(event)" class="p-2 rounded-md transition duration-150 ease-in-out cursor-pointer flex-shrink-0" style="color: rgb(var(--muted));" onmouseover="this.style.backgroundColor='rgb(var(--table-hover))'; this.style.color='rgb(var(--accent))'" onmouseout="this.style.backgroundColor='transparent'; this.style.color='rgb(var(--muted))'" title="Copy code to clipboard">
                        <svg id="copyButtonIcon" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>

                        <svg id="successIcon" class="hidden h-5 w-5" style="color: rgb(var(--success));" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <p style="color: rgb(var(--muted));">
                {{ __('app.verification_step_2') }}
            </p>
            <div id="playerName" class="space-y-2">
                <x-input-label for="playerNameInput" style="color: rgb(var(--fg));">{{ __('app.your_player_name') }}</x-input-label>
                <x-text-input id="playerNameInput" name="playerName" value="{{ $playerName }}" wire:model="playerName" class="mt-1 block w-full"></x-text-input>
            </div>
        </div>

        <div>
            <p style="color: rgb(var(--muted));">
                {{ __('app.verification_step_3') }}
            </p>
        </div>

        <button type="submit" class="btn-primary w-full flex items-center justify-center py-3 px-4 disabled:opacity-50 disabled:cursor-not-allowed" wire:loading.attr="disabled" wire:target="save">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" wire:loading wire:target="save">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
            </svg>
            <span wire:loading.remove wire:target="save">
                {{ __('app.verify_account') }}
            </span>
            <span wire:loading wire:target="save">
                {{ __('app.verifying') }}
            </span>
        </button>
    </form>
</div>
