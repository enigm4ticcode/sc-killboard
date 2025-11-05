<footer class="surface w-full py-8 mt-auto border-t">
    <div class="container-prose flex flex-col md:flex-row items-center justify-between text-sm">
        <div class="flex items-center gap-4 mb-6 md:mb-0">
            <a href="https://robertsspaceindustries.com" aria-label="Star Citizen Community" class="transition-transform hover:scale-110">
                <img src="{{ Vite::asset('resources/images/logos/made_by_the_community.png') }}" alt="{{ __('app.made_by_community') }}" class="w-12 h-auto rounded-full object-cover shadow-md">
            </a>
            <p class="text-gray-600 dark:text-gray-400 inline-flex items-center gap-1 flex-wrap">
                <span>{{ __('app.brought_to_you_by') }}</span>
                <a class="font-medium underline hover:no-underline transition-colors opacity-70 hover:opacity-100 inline-flex items-center gap-1" style="color: rgb(var(--fg));" href="https://robertsspaceindustries.com/citizens/ENIGM4" target="_blank" rel="noopener">
                    <span>ENIGM4</span>
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z"></path>
                        <path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z"></path>
                    </svg>
                </a>
            </p>
        </div>
        <nav class="flex flex-wrap justify-center gap-6 md:gap-8 text-gray-600 dark:text-gray-400 opacity-70">
            <a href="{{ route('legal') }}#legal-disclaimer" class="hover:opacity-100 transition-all" style="color: rgb(var(--fg));">{{ __('app.legal') }}</a>
            <a href="{{ route('legal') }}#cookie-policy" class="hover:opacity-100 transition-all" style="color: rgb(var(--fg));">{{ __('app.cookie_policy') }}</a>
            <a href="{{ route('legal') }}#privacy-policy" class="hover:opacity-100 transition-all" style="color: rgb(var(--fg));">{{ __('app.privacy_policy') }}</a>
            <a href="{{ route('api-documentation') }}" class="hover:opacity-100 transition-all" style="color: rgb(var(--fg));">{{ __('app.api_documentation') }}</a>
            <a href="https://github.com/enigm4ticcode/sc-killboard" target="_blank" rel="noopener" class="hover:opacity-100 transition-all inline-flex items-center gap-1" style="color: rgb(var(--fg));">
                <span>{{ __('app.github') }}</span>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z"></path>
                    <path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z"></path>
                </svg>
            </a>
        </nav>
    </div>
</footer>
