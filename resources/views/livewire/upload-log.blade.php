<div class="flex min-w-full mx-auto min-h-screen justify-center items-center p-6 rounded-lg shadow-xl">
    <form class="w-full max-w-md p-6 bg-white rounded-lg shadow-xl bg-gray-100 dark:bg-gray-900/40" wire:submit="save">
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">
            {{ __('Upload Game Log') }}
        </h1>
        <x-filepond::upload wire:model="file" required="true" class="bg-gray-200 dark:bg-gray-900/40"/>

        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out" wire:loading.attr="disabled" wire:target="save">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" wire:loading wire:target="save">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
            </svg>
            <span wire:loading.remove wire:target="save">
                {{ __('Process Game Log') }}
            </span>
            <span wire:loading wire:target="save">
                {{ __('Processing...') }}
            </span>
        </button>
    </form>
    @filepondScripts
</div>
