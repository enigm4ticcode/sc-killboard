<footer class="surface w-full py-8 mt-auto border-t">
    <div class="container-prose flex flex-col md:flex-row items-center justify-between text-sm">
        <div class="flex items-center gap-4 mb-6 md:mb-0">
            <a href="https://robertsspaceindustries.com" aria-label="Star Citizen Community" class="transition-transform hover:scale-110">
                <img src="{{ asset('img/logos/made_by_the_community.png') }}" alt="{{ __('app.made_by_community') }}" class="w-12 h-auto rounded-full object-cover shadow-md">
            </a>
            <p class="text-gray-600 dark:text-gray-400">
                {{ __('app.brought_to_you_by') }} <a class="font-medium underline hover:no-underline transition-colors" style="color: rgb(var(--accent));" href="https://robertsspaceindustries.com/citizens/ENIGM4" target="_blank" rel="noopener">ENIGM4</a>
            </p>
        </div>
        <nav class="flex flex-wrap justify-center gap-6 md:gap-8 text-gray-600 dark:text-gray-400">
            <a href="{{ route('legal') }}#legal-disclaimer" class="hover:text-gray-900 dark:hover:text-gray-100 transition-colors">{{ __('app.legal') }}</a>
            <a href="{{ route('legal') }}#cookie-policy" class="hover:text-gray-900 dark:hover:text-gray-100 transition-colors">{{ __('app.cookie_policy') }}</a>
            <a href="{{ route('legal') }}#privacy-policy" class="hover:text-gray-900 dark:hover:text-gray-100 transition-colors">{{ __('app.privacy_policy') }}</a>
            <a href="{{ route('api-documentation') }}" class="hover:text-gray-900 dark:hover:text-gray-100 transition-colors">{{ __('app.api_documentation') }}</a>
            <a href="https://github.com/enigm4ticcode/sc-killboard" target="_blank" rel="noopener" class="hover:text-gray-900 dark:hover:text-gray-100 transition-colors inline-flex items-center gap-1">
                <span>{{ __('app.github') }}</span>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z"></path>
                    <path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z"></path>
                </svg>
            </a>
        </nav>
    </div>
</footer>
