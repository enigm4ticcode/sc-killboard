<div>
    <header class="pb-6 mb-6 border-b" style="border-color: rgb(var(--card-border));">
        <h1 class="text-3xl sm:text-4xl font-extrabold mb-2" style="color: rgb(var(--accent));">
            {{ __('profile.title') }}
        </h1>
        <p class="text-base" style="color: rgb(var(--muted));">
            {{ __('profile.description') }}
        </p>
    </header>

    <div class="space-y-6">
        @if(Auth::user()->global_name)
            <div>
                <x-input-label for="global_name" :value="__('profile.display_name')" class="mb-2" style="color: rgb(var(--fg));" />
                <x-text-input
                    id="global_name"
                    name="global_name"
                    type="text"
                    class="mt-1 block w-full"
                    :value="old('global_name', $user->global_name)"
                    required
                    autocomplete="global_name"
                    disabled
                />
                <x-input-error class="mt-2" :messages="$errors->get('global_name')" />
            </div>
        @endif

        <div>
            <x-input-label for="username" :value="__('profile.username')" class="mb-2" style="color: rgb(var(--fg));" />
            <x-text-input
                id="username"
                name="username"
                type="text"
                class="mt-1 block w-full"
                :value="old('username', $user->username)"
                required
                autocomplete="username"
                disabled
            />
            <x-input-error class="mt-2" :messages="$errors->get('username')" />
        </div>

        @if(!Auth::user()->global_name)
            <div>
                <x-input-label for="discriminator" :value="__('profile.discriminator')" class="mb-2" style="color: rgb(var(--fg));" />
                <x-text-input
                    id="discriminator"
                    name="discriminator"
                    type="text"
                    class="mt-1 block w-full"
                    :value="old('discriminator', $user->discriminator)"
                    required
                    autocomplete="discriminator"
                    disabled
                />
                <x-input-error class="mt-2" :messages="$errors->get('discriminator')" />
            </div>
        @endif

        <div>
            <x-input-label for="email" :value="__('profile.email')" class="mb-2" style="color: rgb(var(--fg));" />
            <x-text-input
                id="email"
                name="email"
                type="email"
                class="mt-1 block w-full"
                :value="old('email', $user->email ?? __('profile.unknown'))"
                required
                autocomplete="email"
                disabled
            />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->verified)
                <div>
                    <p class="text-sm mt-2 font-semibold" style="color: rgb(var(--danger));">
                        {{ __('profile.email_unverified') }}
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
