<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="color-scheme" content="light dark">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-container">
        <div class="min-h-screen flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <a href="/" class="transition-transform hover:scale-105 inline-block">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-700 dark:text-gray-300" />
                </a>
            </div>

            <div class="w-full sm:max-w-md glass sm:rounded-2xl p-8">
                {{ $slot }}
            </div>
        </div>
        <livewire:cookie-consent />
    </body>
</html>
