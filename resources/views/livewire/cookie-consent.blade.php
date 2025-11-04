<div
    x-data="{
        show: false,
        init() {
            this.show = !localStorage.getItem('cookieConsent');
        },
        accept() {
            localStorage.setItem('cookieConsent', 'accepted');
            this.show = false;
        }
    }"
    x-show="show"
    class="fixed inset-0 z-50 flex items-end justify-center p-4 sm:p-6 pointer-events-none"
    x-cloak
>
    <!-- Modal -->
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="translate-y-full opacity-0 scale-95"
        x-transition:enter-end="translate-y-0 opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="translate-y-0 opacity-100 scale-100"
        x-transition:leave-end="translate-y-full opacity-0 scale-95"
        class="relative w-full max-w-2xl rounded-xl border mb-4 sm:mb-8 pointer-events-auto"
        style="background-color: rgb(var(--card)); border-color: rgb(var(--card-border)); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);"
        role="dialog"
        aria-label="Cookie Consent"
    >
        <div class="px-4 py-4 sm:px-6 sm:py-5">
            <div class="flex flex-row items-center justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm leading-relaxed" style="color: rgb(var(--fg));">
                        🍪 {{ __('app.cookie_consent_message', ['site' => config('app.name')]) }}
                    </p>
                </div>
                <div class="flex-shrink-0">
                    <button
                        @click="accept()"
                        class="px-4 py-2 rounded-lg font-medium text-xs sm:text-sm transition-all duration-200 hover:shadow-lg hover:scale-105 whitespace-nowrap cursor-pointer"
                        style="background-color: rgb(var(--accent)); color: rgb(var(--card));"
                        aria-label="Accept cookies"
                    >
                        {{ __('app.accept_cookies') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
