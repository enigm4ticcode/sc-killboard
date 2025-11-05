@props(['action' => route('logs.upload.store')])

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-slate-200 leading-tight">
            {{ __('Upload Log File') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-[#0d1424] overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-slate-100">
                    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        <div>
                            <label for="log_file" class="block text-sm font-medium text-gray-700 dark:text-slate-300">{{ __('Upload .log file') }}</label>
                            <input id="log_file" name="log_file" type="file" accept=".log" class="mt-1 block w-full text-sm text-gray-900 dark:text-slate-100 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 dark:file:bg-[#142034] dark:file:text-slate-100 dark:hover:file:bg-[#111b2d]" required>
                            @error('log_file')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-primary-button>{{ __('Upload') }}</x-primary-button>
                        </div>

                        @if (session('status'))
                            <p class="mt-2 text-sm text-green-600">{{ session('status') }}</p>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
