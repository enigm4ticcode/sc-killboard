<nav x-data="{ open: false, isDark: window.isDarkMode() }" @theme-changed.window="isDark = window.isDarkMode()" class="surface border-b sticky top-0 z-50">
    <!-- Primary Navigation Menu -->
    <div class="container-prose">
        <div class="flex justify-between h-16">
            <!-- Hamburger (Left Side) -->
            <div class="flex items-center lg:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-slate-400 hover:text-gray-500 dark:hover:text-slate-300 hover:bg-gray-100 dark:hover:bg-[#142034] focus:outline-none focus:bg-gray-100 dark:focus:bg-[#142034] focus:text-gray-500 dark:focus:text-slate-300 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}">
                        <x-application-logo class="block h-16 w-auto fill-current text-gray-800 dark:text-slate-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 lg:-my-px lg:ml-10 lg:flex">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        {{ __('app.kill_feed') }}
                    </x-nav-link>
                </div>
                <div class="hidden space-x-8 lg:-my-px lg:ml-10 lg:flex">
                    <x-nav-link :href="route('how-it-works')" :active="request()->routeIs('how-it-works')">
                        {{ __('app.how_it_works') }}
                    </x-nav-link>
                </div>

                @if(Auth::user())
                    @if(Auth::user()->rsi_verified)
                        <div class="hidden space-x-8 lg:-my-px lg:ml-10 lg:flex">
                            <x-nav-link :href="route('service.upload-log')" :active="request()->routeIs('service.upload-log')">
                                {{ __('app.upload_log') }}
                            </x-nav-link>
                        </div>
                    @else
                        <div class="hidden space-x-8 lg:-my-px lg:ml-10 lg:flex">
                            <x-nav-link :href="route('service.verify')" :active="request()->routeIs('service.verify')">
                                {{ __('app.rsi_verification') }}
                            </x-nav-link>
                        </div>
                    @endif
                @endif

                <div class="hidden space-x-8 lg:-my-px lg:ml-10 lg:flex">
                    <x-nav-link href="https://robertsspaceindustries.com" target="_blank" rel="noopener noreferrer">
                        <span class="inline-flex items-center gap-1">
                            {{ __('app.rsi_official_website') }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </span>
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden lg:flex lg:items-center lg:ml-6">
                <!-- Global Search Input -->
                <div class="flex items-center">
                    <livewire:global-search />
                </div>

                <!-- Language Switcher -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 ml-3 border border-transparent text-sm leading-4 font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2" style="color: rgb(var(--fg)); focus-ring-color: rgb(var(--accent) / 0.3);" onmouseover="this.style.backgroundColor='rgb(var(--dropdown-hover))'" onmouseout="this.style.backgroundColor='transparent'">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                            </svg>
                            @php
                                $languages = [
                                    'en' => '🇺🇸',
                                    'ru' => '🇷🇺',
                                    'uk' => '🇺🇦',
                                    'es' => '🇪🇸',
                                    'fr' => '🇫🇷',
                                    'de' => '🇩🇪',
                                    'ko' => '🇰🇷',
                                    'zh-TW' => '🇹🇼',
                                    'zh-CN' => '🇨🇳'
                                ];
                                $currentLocale = app()->getLocale();
                            @endphp
                            <span class="text-lg">{{ $languages[$currentLocale] ?? '🇺🇸' }}</span>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <a href="{{ route('language.switch', 'en') }}" class="block w-full px-4 py-2 text-left text-sm leading-5 transition duration-150 ease-in-out {{ app()->getLocale() == 'en' ? 'font-bold' : '' }}" style="color: rgb(var(--fg));" onmouseover="this.style.backgroundColor='rgb(var(--dropdown-hover))'" onmouseout="this.style.backgroundColor='transparent'">
                            <span class="inline-flex items-center gap-2">
                                <span class="text-lg">🇺🇸</span>
                                <span>English</span>
                            </span>
                        </a>
                        <a href="{{ route('language.switch', 'ru') }}" class="block w-full px-4 py-2 text-left text-sm leading-5 transition duration-150 ease-in-out {{ app()->getLocale() == 'ru' ? 'font-bold' : '' }}" style="color: rgb(var(--fg));" onmouseover="this.style.backgroundColor='rgb(var(--dropdown-hover))'" onmouseout="this.style.backgroundColor='transparent'">
                            <span class="inline-flex items-center gap-2">
                                <span class="text-lg">🇷🇺</span>
                                <span>Русский</span>
                            </span>
                        </a>
                        <a href="{{ route('language.switch', 'uk') }}" class="block w-full px-4 py-2 text-left text-sm leading-5 transition duration-150 ease-in-out {{ app()->getLocale() == 'uk' ? 'font-bold' : '' }}" style="color: rgb(var(--fg));" onmouseover="this.style.backgroundColor='rgb(var(--dropdown-hover))'" onmouseout="this.style.backgroundColor='transparent'">
                            <span class="inline-flex items-center gap-2">
                                <span class="text-lg">🇺🇦</span>
                                <span>Українська</span>
                            </span>
                        </a>
                        <a href="{{ route('language.switch', 'es') }}" class="block w-full px-4 py-2 text-left text-sm leading-5 transition duration-150 ease-in-out {{ app()->getLocale() == 'es' ? 'font-bold' : '' }}" style="color: rgb(var(--fg));" onmouseover="this.style.backgroundColor='rgb(var(--dropdown-hover))'" onmouseout="this.style.backgroundColor='transparent'">
                            <span class="inline-flex items-center gap-2">
                                <span class="text-lg">🇪🇸</span>
                                <span>Español</span>
                            </span>
                        </a>
                        <a href="{{ route('language.switch', 'fr') }}" class="block w-full px-4 py-2 text-left text-sm leading-5 transition duration-150 ease-in-out {{ app()->getLocale() == 'fr' ? 'font-bold' : '' }}" style="color: rgb(var(--fg));" onmouseover="this.style.backgroundColor='rgb(var(--dropdown-hover))'" onmouseout="this.style.backgroundColor='transparent'">
                            <span class="inline-flex items-center gap-2">
                                <span class="text-lg">🇫🇷</span>
                                <span>Français</span>
                            </span>
                        </a>
                        <a href="{{ route('language.switch', 'de') }}" class="block w-full px-4 py-2 text-left text-sm leading-5 transition duration-150 ease-in-out {{ app()->getLocale() == 'de' ? 'font-bold' : '' }}" style="color: rgb(var(--fg));" onmouseover="this.style.backgroundColor='rgb(var(--dropdown-hover))'" onmouseout="this.style.backgroundColor='transparent'">
                            <span class="inline-flex items-center gap-2">
                                <span class="text-lg">🇩🇪</span>
                                <span>Deutsch</span>
                            </span>
                        </a>
                        <a href="{{ route('language.switch', 'ko') }}" class="block w-full px-4 py-2 text-left text-sm leading-5 transition duration-150 ease-in-out {{ app()->getLocale() == 'ko' ? 'font-bold' : '' }}" style="color: rgb(var(--fg));" onmouseover="this.style.backgroundColor='rgb(var(--dropdown-hover))'" onmouseout="this.style.backgroundColor='transparent'">
                            <span class="inline-flex items-center gap-2">
                                <span class="text-lg">🇰🇷</span>
                                <span>한국어</span>
                            </span>
                        </a>
                        <a href="{{ route('language.switch', 'zh-TW') }}" class="block w-full px-4 py-2 text-left text-sm leading-5 transition duration-150 ease-in-out {{ app()->getLocale() == 'zh-TW' ? 'font-bold' : '' }}" style="color: rgb(var(--fg));" onmouseover="this.style.backgroundColor='rgb(var(--dropdown-hover))'" onmouseout="this.style.backgroundColor='transparent'">
                            <span class="inline-flex items-center gap-2">
                                <span class="text-lg">🇹🇼</span>
                                <span>繁體中文</span>
                            </span>
                        </a>
                        <a href="{{ route('language.switch', 'zh-CN') }}" class="block w-full px-4 py-2 text-left text-sm leading-5 transition duration-150 ease-in-out {{ app()->getLocale() == 'zh-CN' ? 'font-bold' : '' }}" style="color: rgb(var(--fg));" onmouseover="this.style.backgroundColor='rgb(var(--dropdown-hover))'" onmouseout="this.style.backgroundColor='transparent'">
                            <span class="inline-flex items-center gap-2">
                                <span class="text-lg">🇨🇳</span>
                                <span>简体中文</span>
                            </span>
                        </a>
                    </x-slot>
                </x-dropdown>

                @if(Auth::user())
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2" style="color: rgb(var(--fg)); focus-ring-color: rgb(var(--accent) / 0.3);" onmouseover="this.style.backgroundColor='rgb(var(--dropdown-hover))'" onmouseout="this.style.backgroundColor='transparent'">
                                <img class="h-9 w-9 rounded-full object-cover mr-2 ring-1 transition-all duration-200" style="ring-color: rgb(var(--card-border) / 0.5);" src="{{ Auth::user()->getAvatar(['extension' => 'webp', 'size' => 32]) }}" alt="{{ Auth::user()->getTagAttribute() }}" />
                                <div class="flex flex-col items-start">
                                    <span class="font-medium">{{ Auth::user()->getTagAttribute() }}</span>
                                    @if (Auth::user()->global_name)
                                        <small class="text-xs opacity-75">{{ Auth::user()->username }}</small>
                                    @endif
                                </div>

                                <div class="ml-2">
                                    <svg class="fill-current h-4 w-4 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" :class="{ 'rotate-180': open }">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('app.profile') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('how-it-works')">
                                {{ __('app.how_it_works') }}
                            </x-dropdown-link>
                            @if(Auth::user()->rsi_verified)
                                <x-dropdown-link :href="route('service.upload-log')">
                                    {{ __('app.upload_log') }}
                                </x-dropdown-link>
                            @else
                                <x-dropdown-link :href="route('service.verify')">
                                    {{ __('app.rsi_verification') }}
                                </x-dropdown-link>
                            @endif
                            <x-dropdown-link :href="route('api-documentation')">
                                {{ __('app.api_docs') }}
                            </x-dropdown-link>
                            <x-dropdown-link href="https://robertsspaceindustries.com" target="_blank" rel="noopener noreferrer">
                                <span class="inline-flex items-center gap-1">
                                    {{ __('app.rsi_official_website') }}
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                </span>
                            </x-dropdown-link>

                            <!-- Theme Selector -->
                            <div class="border-t border-gray-200 dark:border-[#0f1828] my-1"></div>
                            <div class="px-4 py-2">
                                <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-slate-400 mb-2">{{ __('app.theme') }}</div>
                                <div class="space-y-1" x-data="{ currentTheme: window.getCurrentTheme() }" @theme-changed.window="currentTheme = window.getCurrentTheme()">
                                    <button @click="window.setTheme('light')" class="w-full text-left px-3 py-2 rounded-lg text-sm transition flex items-center gap-2" :class="currentTheme === 'light' ? 'font-bold' : ''" :style="currentTheme === 'light' ? 'color: rgb(var(--fg)); background-color: rgba(var(--accent), 0.08);' : 'color: rgb(var(--fg));'" onmouseover="if (window.getCurrentTheme() !== 'light') this.style.backgroundColor='rgba(var(--card-border), 0.3)'" onmouseout="if (window.getCurrentTheme() !== 'light') this.style.backgroundColor='transparent'">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ __('app.theme_light') }}
                                    </button>
                                    <button @click="window.setTheme('space-black')" class="w-full text-left px-3 py-2 rounded-lg text-sm transition flex items-center gap-2" :class="currentTheme === 'space-black' ? 'font-bold' : ''" :style="currentTheme === 'space-black' ? 'color: rgb(var(--fg)); background-color: rgba(var(--accent), 0.08);' : 'color: rgb(var(--fg));'" onmouseover="if (window.getCurrentTheme() !== 'space-black') this.style.backgroundColor='rgba(var(--card-border), 0.3)'" onmouseout="if (window.getCurrentTheme() !== 'space-black') this.style.backgroundColor='transparent'">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                                        </svg>
                                        {{ __('app.theme_space_black') }}
                                    </button>
                                    <button @click="window.setTheme('space-blue')" class="w-full text-left px-3 py-2 rounded-lg text-sm transition flex items-center gap-2" :class="currentTheme === 'space-blue' ? 'font-bold' : ''" :style="currentTheme === 'space-blue' ? 'color: rgb(var(--fg)); background-color: rgba(var(--accent), 0.08);' : 'color: rgb(var(--fg));'" onmouseover="if (window.getCurrentTheme() !== 'space-blue') this.style.backgroundColor='rgba(var(--card-border), 0.3)'" onmouseout="if (window.getCurrentTheme() !== 'space-blue') this.style.backgroundColor='transparent'">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                                        </svg>
                                        {{ __('app.theme_space_blue') }}
                                    </button>
                                </div>
                            </div>

                            <!-- Authentication -->
                            <div class="border-t border-gray-200 dark:border-[#0f1828] my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                                 onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                    {{ __('app.log_out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2" style="color: rgb(var(--fg)); focus-ring-color: rgb(var(--accent) / 0.3);" onmouseover="this.style.backgroundColor='rgb(var(--dropdown-hover))'" onmouseout="this.style.backgroundColor='transparent'">
                                <div class="h-9 w-9 rounded-full mr-2 flex items-center justify-center ring-1 transition-all duration-200" style="background-color: rgb(var(--card-border)); ring-color: rgb(var(--card-border) / 0.5);">
                                    <svg class="w-5 h-5" style="color: rgb(var(--muted));" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="flex flex-col items-start">
                                    <span class="font-medium">{{ __('app.guest') }}</span>
                                </div>

                                <div class="ml-2">
                                    <svg class="fill-current h-4 w-4 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" :class="{ 'rotate-180': open }">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('how-it-works')">
                                {{ __('app.how_it_works') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('api-documentation')">
                                {{ __('app.api_docs') }}
                            </x-dropdown-link>
                            <x-dropdown-link href="https://robertsspaceindustries.com" target="_blank" rel="noopener noreferrer">
                                <span class="inline-flex items-center gap-1">
                                    {{ __('app.rsi_official_website') }}
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                </span>
                            </x-dropdown-link>

                            <!-- Theme Selector -->
                            <div class="border-t border-gray-200 dark:border-[#0f1828] my-1"></div>
                            <div class="px-4 py-2">
                                <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-slate-400 mb-2">{{ __('app.theme') }}</div>
                                <div class="space-y-1" x-data="{ currentTheme: window.getCurrentTheme() }" @theme-changed.window="currentTheme = window.getCurrentTheme()">
                                    <button @click="window.setTheme('light')" class="w-full text-left px-3 py-2 rounded-lg text-sm transition flex items-center gap-2" :class="currentTheme === 'light' ? 'font-bold' : ''" :style="currentTheme === 'light' ? 'color: rgb(var(--fg)); background-color: rgba(var(--accent), 0.08);' : 'color: rgb(var(--fg));'" onmouseover="if (window.getCurrentTheme() !== 'light') this.style.backgroundColor='rgba(var(--card-border), 0.3)'" onmouseout="if (window.getCurrentTheme() !== 'light') this.style.backgroundColor='transparent'">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ __('app.theme_light') }}
                                    </button>
                                    <button @click="window.setTheme('space-black')" class="w-full text-left px-3 py-2 rounded-lg text-sm transition flex items-center gap-2" :class="currentTheme === 'space-black' ? 'font-bold' : ''" :style="currentTheme === 'space-black' ? 'color: rgb(var(--fg)); background-color: rgba(var(--accent), 0.08);' : 'color: rgb(var(--fg));'" onmouseover="if (window.getCurrentTheme() !== 'space-black') this.style.backgroundColor='rgba(var(--card-border), 0.3)'" onmouseout="if (window.getCurrentTheme() !== 'space-black') this.style.backgroundColor='transparent'">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                                        </svg>
                                        {{ __('app.theme_space_black') }}
                                    </button>
                                    <button @click="window.setTheme('space-blue')" class="w-full text-left px-3 py-2 rounded-lg text-sm transition flex items-center gap-2" :class="currentTheme === 'space-blue' ? 'font-bold' : ''" :style="currentTheme === 'space-blue' ? 'color: rgb(var(--fg)); background-color: rgba(var(--accent), 0.08);' : 'color: rgb(var(--fg));'" onmouseover="if (window.getCurrentTheme() !== 'space-blue') this.style.backgroundColor='rgba(var(--card-border), 0.3)'" onmouseout="if (window.getCurrentTheme() !== 'space-blue') this.style.backgroundColor='transparent'">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                                        </svg>
                                        {{ __('app.theme_space_blue') }}
                                    </button>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 dark:border-[#0f1828] my-1"></div>
                            <x-dropdown-link :href="route('login')">
                                <span class="inline-flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515a.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0a12.64 12.64 0 0 0-.617-1.25a.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057a19.9 19.9 0 0 0 5.993 3.03a.078.078 0 0 0 .084-.028a14.09 14.09 0 0 0 1.226-1.994a.076.076 0 0 0-.041-.106a13.107 13.107 0 0 1-1.872-.892a.077.077 0 0 1-.008-.128a10.2 10.2 0 0 0 .372-.292a.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127a12.299 12.299 0 0 1-1.873.892a.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028a19.839 19.839 0 0 0 6.002-3.03a.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419c0-1.333.956-2.419 2.157-2.419c1.21 0 2.176 1.096 2.157 2.42c0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419c0-1.333.955-2.419 2.157-2.419c1.21 0 2.176 1.096 2.157 2.42c0 1.333-.946 2.418-2.157 2.418z"/>
                                    </svg>
                                    {{ __('app.login_discord') }}
                                </span>
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>
                @endif
            </div>

        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" :aria-hidden="!open" :inert="!open" class="hidden lg:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                {{ __('app.kill_feed') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('how-it-works')" :active="request()->routeIs('how-it-works')">
                {{ __('app.how_it_works') }}
            </x-responsive-nav-link>
            @if(Auth::user())
                @if(Auth::user()->rsi_verified)
                    <x-responsive-nav-link :href="route('service.upload-log')" :active="request()->routeIs('service.upload-log')">
                        {{ __('app.upload_log') }}
                    </x-responsive-nav-link>
                @else
                    <x-responsive-nav-link :href="route('service.verify')" :active="request()->routeIs('service.verify')">
                        {{ __('app.rsi_verification') }}
                    </x-responsive-nav-link>
                @endif
            @endif
            <x-responsive-nav-link href="https://robertsspaceindustries.com" target="_blank" rel="noopener noreferrer">
                <span class="inline-flex items-center gap-1">
                    {{ __('app.rsi_official_website') }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                </span>
            </x-responsive-nav-link>
        </div>

        <!-- Mobile Search -->
        <div class="px-3 pb-3 border-t border-gray-200 dark:border-[#0f1828] pt-3">
            <livewire:global-search />
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-[#0f1828]">
            @if(Auth::user())
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800 dark:text-slate-200">{{ Auth::user()->getTagAttribute() }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('app.profile') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('api-documentation')">
                        {{ __('app.api_docs') }}
                    </x-responsive-nav-link>

                    <!-- Language Switcher -->
                    <div class="border-t border-gray-200 dark:border-gray-600 my-1"></div>
                    <div class="px-3 py-2">
                        <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-slate-400 mb-2">{{ __('app.language') }}</div>
                        <div class="space-y-1">
                            <a href="{{ route('language.switch', 'en') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition {{ app()->getLocale() == 'en' ? 'font-bold' : '' }}" style="color: rgb(var(--fg)); {{ app()->getLocale() == 'en' ? 'background-color: rgba(var(--accent), 0.08);' : '' }}" onmouseover="if ('{{ app()->getLocale() }}' !== 'en') this.style.backgroundColor='rgba(var(--card-border), 0.3)'" onmouseout="if ('{{ app()->getLocale() }}' !== 'en') this.style.backgroundColor='transparent'">
                                <span class="text-lg">🇺🇸</span> English
                            </a>
                            <a href="{{ route('language.switch', 'ru') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition {{ app()->getLocale() == 'ru' ? 'font-bold' : '' }}" style="color: rgb(var(--fg)); {{ app()->getLocale() == 'ru' ? 'background-color: rgba(var(--accent), 0.08);' : '' }}" onmouseover="if ('{{ app()->getLocale() }}' !== 'ru') this.style.backgroundColor='rgba(var(--card-border), 0.3)'" onmouseout="if ('{{ app()->getLocale() }}' !== 'ru') this.style.backgroundColor='transparent'">
                                <span class="text-lg">🇷🇺</span> Русский
                            </a>
                            <a href="{{ route('language.switch', 'uk') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition {{ app()->getLocale() == 'uk' ? 'font-bold' : '' }}" style="color: rgb(var(--fg)); {{ app()->getLocale() == 'uk' ? 'background-color: rgba(var(--accent), 0.08);' : '' }}" onmouseover="if ('{{ app()->getLocale() }}' !== 'uk') this.style.backgroundColor='rgba(var(--card-border), 0.3)'" onmouseout="if ('{{ app()->getLocale() }}' !== 'uk') this.style.backgroundColor='transparent'">
                                <span class="text-lg">🇺🇦</span> Українська
                            </a>
                            <a href="{{ route('language.switch', 'es') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition {{ app()->getLocale() == 'es' ? 'font-bold' : '' }}" style="color: rgb(var(--fg)); {{ app()->getLocale() == 'es' ? 'background-color: rgba(var(--accent), 0.08);' : '' }}" onmouseover="if ('{{ app()->getLocale() }}' !== 'es') this.style.backgroundColor='rgba(var(--card-border), 0.3)'" onmouseout="if ('{{ app()->getLocale() }}' !== 'es') this.style.backgroundColor='transparent'">
                                <span class="text-lg">🇪🇸</span> Español
                            </a>
                            <a href="{{ route('language.switch', 'fr') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition {{ app()->getLocale() == 'fr' ? 'font-bold' : '' }}" style="color: rgb(var(--fg)); {{ app()->getLocale() == 'fr' ? 'background-color: rgba(var(--accent), 0.08);' : '' }}" onmouseover="if ('{{ app()->getLocale() }}' !== 'fr') this.style.backgroundColor='rgba(var(--card-border), 0.3)'" onmouseout="if ('{{ app()->getLocale() }}' !== 'fr') this.style.backgroundColor='transparent'">
                                <span class="text-lg">🇫🇷</span> Français
                            </a>
                            <a href="{{ route('language.switch', 'de') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition {{ app()->getLocale() == 'de' ? 'font-bold' : '' }}" style="color: rgb(var(--fg)); {{ app()->getLocale() == 'de' ? 'background-color: rgba(var(--accent), 0.08);' : '' }}" onmouseover="if ('{{ app()->getLocale() }}' !== 'de') this.style.backgroundColor='rgba(var(--card-border), 0.3)'" onmouseout="if ('{{ app()->getLocale() }}' !== 'de') this.style.backgroundColor='transparent'">
                                <span class="text-lg">🇩🇪</span> Deutsch
                            </a>
                            <a href="{{ route('language.switch', 'ko') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition {{ app()->getLocale() == 'ko' ? 'font-bold' : '' }}" style="color: rgb(var(--fg)); {{ app()->getLocale() == 'ko' ? 'background-color: rgba(var(--accent), 0.08);' : '' }}" onmouseover="if ('{{ app()->getLocale() }}' !== 'ko') this.style.backgroundColor='rgba(var(--card-border), 0.3)'" onmouseout="if ('{{ app()->getLocale() }}' !== 'ko') this.style.backgroundColor='transparent'">
                                <span class="text-lg">🇰🇷</span> 한국어
                            </a>
                            <a href="{{ route('language.switch', 'zh-TW') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition {{ app()->getLocale() == 'zh-TW' ? 'font-bold' : '' }}" style="color: rgb(var(--fg)); {{ app()->getLocale() == 'zh-TW' ? 'background-color: rgba(var(--accent), 0.08);' : '' }}" onmouseover="if ('{{ app()->getLocale() }}' !== 'zh-TW') this.style.backgroundColor='rgba(var(--card-border), 0.3)'" onmouseout="if ('{{ app()->getLocale() }}' !== 'zh-TW') this.style.backgroundColor='transparent'">
                                <span class="text-lg">🇹🇼</span> 繁體中文
                            </a>
                            <a href="{{ route('language.switch', 'zh-CN') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition {{ app()->getLocale() == 'zh-CN' ? 'font-bold' : '' }}" style="color: rgb(var(--fg)); {{ app()->getLocale() == 'zh-CN' ? 'background-color: rgba(var(--accent), 0.08);' : '' }}" onmouseover="if ('{{ app()->getLocale() }}' !== 'zh-CN') this.style.backgroundColor='rgba(var(--card-border), 0.3)'" onmouseout="if ('{{ app()->getLocale() }}' !== 'zh-CN') this.style.backgroundColor='transparent'">
                                <span class="text-lg">🇨🇳</span> 简体中文
                            </a>
                        </div>
                    </div>

                    <!-- Theme Selector -->
                    <div class="border-t border-gray-200 dark:border-[#0f1828] my-1"></div>
                    <div class="px-3 py-2">
                        <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-slate-400 mb-2">{{ __('app.theme') }}</div>
                        <div class="space-y-1" x-data="{ currentTheme: window.getCurrentTheme() }" @theme-changed.window="currentTheme = window.getCurrentTheme()">
                            <button @click="window.setTheme('light')" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition w-full text-left" :class="currentTheme === 'light' ? 'font-bold' : ''" :style="currentTheme === 'light' ? 'color: rgb(var(--fg)); background-color: rgba(var(--accent), 0.08);' : 'color: rgb(var(--fg));'" onmouseover="if (window.getCurrentTheme() !== 'light') this.style.backgroundColor='rgba(var(--card-border), 0.3)'" onmouseout="if (window.getCurrentTheme() !== 'light') this.style.backgroundColor='transparent'">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ __('app.theme_light') }}
                            </button>
                            <button @click="window.setTheme('space-black')" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition w-full text-left" :class="currentTheme === 'space-black' ? 'font-bold' : ''" :style="currentTheme === 'space-black' ? 'color: rgb(var(--fg)); background-color: rgba(var(--accent), 0.08);' : 'color: rgb(var(--fg));'" onmouseover="if (window.getCurrentTheme() !== 'space-black') this.style.backgroundColor='rgba(var(--card-border), 0.3)'" onmouseout="if (window.getCurrentTheme() !== 'space-black') this.style.backgroundColor='transparent'">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                                </svg>
                                {{ __('app.theme_space_black') }}
                            </button>
                            <button @click="window.setTheme('space-blue')" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition w-full text-left" :class="currentTheme === 'space-blue' ? 'font-bold' : ''" :style="currentTheme === 'space-blue' ? 'color: rgb(var(--fg)); background-color: rgba(var(--accent), 0.08);' : 'color: rgb(var(--fg));'" onmouseover="if (window.getCurrentTheme() !== 'space-blue') this.style.backgroundColor='rgba(var(--card-border), 0.3)'" onmouseout="if (window.getCurrentTheme() !== 'space-blue') this.style.backgroundColor='transparent'">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                                </svg>
                                {{ __('app.theme_space_blue') }}
                            </button>
                        </div>
                    </div>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-responsive-nav-link :href="route('logout')"
                                               onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('app.log_out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('api-documentation')">
                        {{ __('app.api_docs') }}
                    </x-responsive-nav-link>

                    <!-- Language Switcher -->
                    <div class="border-t border-gray-200 dark:border-gray-600 my-1"></div>
                    <div class="px-3 py-2">
                        <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-slate-400 mb-2">{{ __('app.language') }}</div>
                        <div class="space-y-1">
                            <a href="{{ route('language.switch', 'en') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition {{ app()->getLocale() == 'en' ? 'font-bold' : '' }}" style="color: rgb(var(--fg)); {{ app()->getLocale() == 'en' ? 'background-color: rgba(var(--accent), 0.08);' : '' }}" onmouseover="if ('{{ app()->getLocale() }}' !== 'en') this.style.backgroundColor='rgba(var(--card-border), 0.3)'" onmouseout="if ('{{ app()->getLocale() }}' !== 'en') this.style.backgroundColor='transparent'">
                                <span class="text-lg">🇺🇸</span> English
                            </a>
                            <a href="{{ route('language.switch', 'ru') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition {{ app()->getLocale() == 'ru' ? 'font-bold' : '' }}" style="color: rgb(var(--fg)); {{ app()->getLocale() == 'ru' ? 'background-color: rgba(var(--accent), 0.08);' : '' }}" onmouseover="if ('{{ app()->getLocale() }}' !== 'ru') this.style.backgroundColor='rgba(var(--card-border), 0.3)'" onmouseout="if ('{{ app()->getLocale() }}' !== 'ru') this.style.backgroundColor='transparent'">
                                <span class="text-lg">🇷🇺</span> Русский
                            </a>
                            <a href="{{ route('language.switch', 'uk') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition {{ app()->getLocale() == 'uk' ? 'font-bold' : '' }}" style="color: rgb(var(--fg)); {{ app()->getLocale() == 'uk' ? 'background-color: rgba(var(--accent), 0.08);' : '' }}" onmouseover="if ('{{ app()->getLocale() }}' !== 'uk') this.style.backgroundColor='rgba(var(--card-border), 0.3)'" onmouseout="if ('{{ app()->getLocale() }}' !== 'uk') this.style.backgroundColor='transparent'">
                                <span class="text-lg">🇺🇦</span> Українська
                            </a>
                            <a href="{{ route('language.switch', 'es') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition {{ app()->getLocale() == 'es' ? 'font-bold' : '' }}" style="color: rgb(var(--fg)); {{ app()->getLocale() == 'es' ? 'background-color: rgba(var(--accent), 0.08);' : '' }}" onmouseover="if ('{{ app()->getLocale() }}' !== 'es') this.style.backgroundColor='rgba(var(--card-border), 0.3)'" onmouseout="if ('{{ app()->getLocale() }}' !== 'es') this.style.backgroundColor='transparent'">
                                <span class="text-lg">🇪🇸</span> Español
                            </a>
                            <a href="{{ route('language.switch', 'fr') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition {{ app()->getLocale() == 'fr' ? 'font-bold' : '' }}" style="color: rgb(var(--fg)); {{ app()->getLocale() == 'fr' ? 'background-color: rgba(var(--accent), 0.08);' : '' }}" onmouseover="if ('{{ app()->getLocale() }}' !== 'fr') this.style.backgroundColor='rgba(var(--card-border), 0.3)'" onmouseout="if ('{{ app()->getLocale() }}' !== 'fr') this.style.backgroundColor='transparent'">
                                <span class="text-lg">🇫🇷</span> Français
                            </a>
                            <a href="{{ route('language.switch', 'de') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition {{ app()->getLocale() == 'de' ? 'font-bold' : '' }}" style="color: rgb(var(--fg)); {{ app()->getLocale() == 'de' ? 'background-color: rgba(var(--accent), 0.08);' : '' }}" onmouseover="if ('{{ app()->getLocale() }}' !== 'de') this.style.backgroundColor='rgba(var(--card-border), 0.3)'" onmouseout="if ('{{ app()->getLocale() }}' !== 'de') this.style.backgroundColor='transparent'">
                                <span class="text-lg">🇩🇪</span> Deutsch
                            </a>
                            <a href="{{ route('language.switch', 'ko') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition {{ app()->getLocale() == 'ko' ? 'font-bold' : '' }}" style="color: rgb(var(--fg)); {{ app()->getLocale() == 'ko' ? 'background-color: rgba(var(--accent), 0.08);' : '' }}" onmouseover="if ('{{ app()->getLocale() }}' !== 'ko') this.style.backgroundColor='rgba(var(--card-border), 0.3)'" onmouseout="if ('{{ app()->getLocale() }}' !== 'ko') this.style.backgroundColor='transparent'">
                                <span class="text-lg">🇰🇷</span> 한국어
                            </a>
                            <a href="{{ route('language.switch', 'zh-TW') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition {{ app()->getLocale() == 'zh-TW' ? 'font-bold' : '' }}" style="color: rgb(var(--fg)); {{ app()->getLocale() == 'zh-TW' ? 'background-color: rgba(var(--accent), 0.08);' : '' }}" onmouseover="if ('{{ app()->getLocale() }}' !== 'zh-TW') this.style.backgroundColor='rgba(var(--card-border), 0.3)'" onmouseout="if ('{{ app()->getLocale() }}' !== 'zh-TW') this.style.backgroundColor='transparent'">
                                <span class="text-lg">🇹🇼</span> 繁體中文
                            </a>
                            <a href="{{ route('language.switch', 'zh-CN') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition {{ app()->getLocale() == 'zh-CN' ? 'font-bold' : '' }}" style="color: rgb(var(--fg)); {{ app()->getLocale() == 'zh-CN' ? 'background-color: rgba(var(--accent), 0.08);' : '' }}" onmouseover="if ('{{ app()->getLocale() }}' !== 'zh-CN') this.style.backgroundColor='rgba(var(--card-border), 0.3)'" onmouseout="if ('{{ app()->getLocale() }}' !== 'zh-CN') this.style.backgroundColor='transparent'">
                                <span class="text-lg">🇨🇳</span> 简体中文
                            </a>
                        </div>
                    </div>

                    <!-- Theme Selector -->
                    <div class="border-t border-gray-200 dark:border-[#0f1828] my-1"></div>
                    <div class="px-3 py-2">
                        <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-slate-400 mb-2">{{ __('app.theme') }}</div>
                        <div class="space-y-1" x-data="{ currentTheme: window.getCurrentTheme() }" @theme-changed.window="currentTheme = window.getCurrentTheme()">
                            <button @click="window.setTheme('light')" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition w-full text-left" :class="currentTheme === 'light' ? 'font-bold' : ''" :style="currentTheme === 'light' ? 'color: rgb(var(--fg)); background-color: rgba(var(--accent), 0.08);' : 'color: rgb(var(--fg));'" onmouseover="if (window.getCurrentTheme() !== 'light') this.style.backgroundColor='rgba(var(--card-border), 0.3)'" onmouseout="if (window.getCurrentTheme() !== 'light') this.style.backgroundColor='transparent'">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ __('app.theme_light') }}
                            </button>
                            <button @click="window.setTheme('space-black')" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition w-full text-left" :class="currentTheme === 'space-black' ? 'font-bold' : ''" :style="currentTheme === 'space-black' ? 'color: rgb(var(--fg)); background-color: rgba(var(--accent), 0.08);' : 'color: rgb(var(--fg));'" onmouseover="if (window.getCurrentTheme() !== 'space-black') this.style.backgroundColor='rgba(var(--card-border), 0.3)'" onmouseout="if (window.getCurrentTheme() !== 'space-black') this.style.backgroundColor='transparent'">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                                </svg>
                                {{ __('app.theme_space_black') }}
                            </button>
                            <button @click="window.setTheme('space-blue')" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition w-full text-left" :class="currentTheme === 'space-blue' ? 'font-bold' : ''" :style="currentTheme === 'space-blue' ? 'color: rgb(var(--fg)); background-color: rgba(var(--accent), 0.08);' : 'color: rgb(var(--fg));'" onmouseover="if (window.getCurrentTheme() !== 'space-blue') this.style.backgroundColor='rgba(var(--card-border), 0.3)'" onmouseout="if (window.getCurrentTheme() !== 'space-blue') this.style.backgroundColor='transparent'">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                                </svg>
                                {{ __('app.theme_space_blue') }}
                            </button>
                        </div>
                    </div>

                    <x-responsive-nav-link :href="route('login')">
                        <span class="inline-flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515a.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0a12.64 12.64 0 0 0-.617-1.25a.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057a19.9 19.9 0 0 0 5.993 3.03a.078.078 0 0 0 .084-.028a14.09 14.09 0 0 0 1.226-1.994a.076.076 0 0 0-.041-.106a13.107 13.107 0 0 1-1.872-.892a.077.077 0 0 1-.008-.128a10.2 10.2 0 0 0 .372-.292a.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127a12.299 12.299 0 0 1-1.873.892a.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028a19.839 19.839 0 0 0 6.002-3.03a.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419c0-1.333.956-2.419 2.157-2.419c1.21 0 2.176 1.096 2.157 2.42c0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419c0-1.333.955-2.419 2.157-2.419c1.21 0 2.176 1.096 2.157 2.42c0 1.333-.946 2.418-2.157 2.418z"/>
                            </svg>
                            {{ __('app.login_discord') }}
                        </span>
                    </x-responsive-nav-link>
                </div>
            @endif
        </div>
    </div>
</nav>
