@php use Illuminate\Support\Facades\Auth; @endphp
<form class="min-w-full p-4 bg-white dark:bg-gray-800 rounded-xl shadow-2xl space-y-6" wire:submit.prevent="save">
    <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-indigo-600 dark:text-indigo-400 text-shadow-md">
        {{ __('API Key Management') }}
    </h1>

    <div class="space-y-4">
        <p class="text-gray-600 dark:text-gray-400">
            Username:
        </p>

        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900 border border-dashed border-gray-300 dark:border-gray-600 rounded-lg shadow-inner">
            <pre class="text-md font-mono text-yellow-600 text-shadow-md overflow-x-auto whitespace-pre-wrap break-all mr-4">{{ Auth::user()->username }}</pre>
        </div>
    </div>

    <div class="space-y-4">
        <p class="text-gray-600 dark:text-gray-400">
            Here you can find (or regenerate) your API key for use with the killboard API:
        </p>

        <div id="code" class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900 border border-dashed border-gray-300 dark:border-gray-600 rounded-lg shadow-inner">
            <pre id="copyTarget" class="text-md font-mono text-yellow-600 text-shadow-md overflow-x-auto whitespace-pre-wrap break-all mr-4">{{ Auth::user()->api_key ?? Auth::user()->generateApiKey() }}</pre>

            <div class="flex items-center space-x-2">
                <span id="successMessage" class="hidden text-sm font-semibold text-green-600 dark:text-green-400 transition-opacity duration-300">
                    Copied!
                </span>

                <button type="button" onclick="copyCode(event)" class="p-2 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition duration-150 ease-in-out cursor-pointer flex-shrink-0" title="Copy code to clipboard">
                    <svg id="copyButtonIcon" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>

                    <svg id="successIcon" class="hidden h-5 w-5 text-green-600 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <button type="submit" class="w-full flex items-center justify-center py-3 px-4 border border-transparent rounded-lg shadow-md text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 focus:ring-offset-white dark:focus:ring-offset-gray-800 transition duration-150 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed" wire:loading.attr="disabled" wire:target="save">
        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" wire:loading wire:target="save">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
        </svg>
        <span wire:loading.remove wire:target="save">
            {{ __('Regenerate API Key') }}
        </span>
        <span wire:loading wire:target="save">
            {{ __('Regenerating API Key...') }}
        </span>
    </button>
</form>
