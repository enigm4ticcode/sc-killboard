<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light dark">
    <meta name="description" content="Track kills, deaths, and player efficiency for Star Citizen. View global leaderboards and stats for yourself or your organization on the premier Star Citizen killboard.">
    <meta name="keywords" content="Star Citizen killboard, Star Citizen stats, SC kill death tracker, Star Citizen player efficiency, SC leaderboards, Star Citizen combat stats, organization stats, Star Citizen PvP">
    <title>
        @hasSection('title')
            @yield('title') - {{ config('app.name', 'Star Citizen Killboard') }} | Kills, Deaths, and Leaderboards
        @else
            {{ config('app.name', 'Star Citizen Killboard') }} | Kills, Deaths, and Leaderboards
        @endif
    </title>

    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ Vite::asset('resources/images/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ Vite::asset('resources/images/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ Vite::asset('resources/images/favicon/favicon-32x32.png') }}">
    <link rel="manifest" href="{{ Vite::asset('resources/images/favicon/site.webmanifest') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-container">
<div class="min-h-screen flex flex-col">
    @include('layouts.navigation')

    <!-- Page Heading -->
    @isset($header)
        <header class="surface sticky top-0 z-40 backdrop-blur-sm">
            <div class="container-prose py-4">
                {{ $header }}
            </div>
        </header>
    @endisset

    <main class="flex-1">
        <div class="container-prose py-4 sm:py-8 lg:py-12">
            {{ $slot }}
        </div>
    </main>

    @include('layouts.footer')
</div>
<x-toaster-hub />
</body>
</html>
