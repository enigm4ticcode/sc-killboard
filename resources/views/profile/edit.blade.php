<x-app-layout>
    <div class="mt-8 mb-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="min-w-full">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            @if(\Illuminate\Support\Facades\Auth::user()->rsi_verified)
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="min-w-full">
                        <livewire:api-key />
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
