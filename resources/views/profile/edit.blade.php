<x-app-layout>
    <div class="space-y-4 sm:space-y-6">
        <div class="card p-4 sm:p-6 lg:p-8 mx-4 sm:mx-0">
            <div class="min-w-full">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        @if(\Illuminate\Support\Facades\Auth::user()->rsi_verified)
            <div class="card p-4 sm:p-6 lg:p-8 mx-4 sm:mx-0">
                <div class="min-w-full">
                    <livewire:api-key />
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
