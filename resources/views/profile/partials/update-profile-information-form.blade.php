<div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 shadow-2xl rounded-xl p-4 transition-colors duration-300">
    <header class="pb-6 border-b border-gray-200 dark:border-gray-700 mb-6">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-indigo-600 dark:text-indigo-400 mb-2 text-shadow-md">
            {{ __('Profile Information') }}
        </h1>
        <p class="text-gray-600 dark:text-gray-400 text">
            {{ __('View your Discord account details.') }}
        </p>
    </header>

    <div class="space-y-6">
        @if(Auth::user()->global_name)
            <div>
                <x-input-label for="global_name" :value="__('Display Name')" class="mb-1 text-gray-700 dark:text-gray-300 font-medium" />
                <x-text-input id="global_name" name="global_name" type="text" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-3" :value="old('global_name', $user->global_name)" required autocomplete="global_name" disabled />
                <x-input-error class="mt-2" :messages="$errors->get('global_name')" />
            </div>
        @endif
        <div>
            <x-input-label for="username" :value="__('Username')" class="mb-1 text-gray-700 dark:text-gray-300 font-medium" />
            <x-text-input id="username" name="username" type="text" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-3" :value="old('username', $user->username)" required autocomplete="username" disabled />
            <x-input-error class="mt-2" :messages="$errors->get('username')" />
        </div>

        @if(!Auth::user()->global_name)
            <div>
                <x-input-label for="discriminator" :value="__('Discriminator')" class="mb-1 text-gray-700 dark:text-gray-300 font-medium" />
                <x-text-input id="discriminator" name="discriminator" type="text" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-3" :value="old('discriminator', $user->discriminator)" required autocomplete="discriminator" disabled />
                <x-input-error class="mt-2" :messages="$errors->get('discriminator')" />
            </div>
        @endif

        <div>
            <x-input-label for="email" :value="__('Email')" class="mb-1 text-gray-700 dark:text-gray-300 font-medium" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-3" :value="old('email', $user->email ?? __('Unknown'))" required autocomplete="email" disabled />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->verified)
                <div>
                    <p class="text-sm mt-2 text-red-600 dark:text-red-400 font-semibold">
                        {{ __('Your email address is unverified.') }}
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
