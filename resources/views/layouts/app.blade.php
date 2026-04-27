<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @php
        $isAdminShell = auth()->check() && auth()->user()->isAdmin() && request()->routeIs('admin.dashboard');
        $shellWidthClass = $isAdminShell ? 'max-w-[1920px] 2xl:max-w-[2080px]' : 'max-w-7xl';
    @endphp
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Pickle BALLan Ni Juan') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="app-shell font-sans antialiased text-slate-900">
        <div class="relative min-h-screen overflow-hidden">
            <div class="pointer-events-none absolute inset-x-0 top-0 h-[32rem] bg-[radial-gradient(circle_at_top,rgba(255,255,255,0.16),transparent_58%)]"></div>
            <div class="pointer-events-none absolute inset-x-0 top-0 h-[26rem] bg-[linear-gradient(90deg,rgba(56,189,248,0.18),transparent_32%,transparent_68%,rgba(251,191,36,0.18))] opacity-70"></div>
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="relative z-10">
                    <div class="mx-auto {{ $shellWidthClass }} px-4 pb-2 pt-8 sm:px-6 lg:px-8">
                        <div class="dark-panel relative overflow-hidden px-6 py-7 sm:px-8">
                            <div class="pointer-events-none absolute -left-16 top-0 h-44 w-44 rounded-full bg-sky-400/20 blur-3xl"></div>
                            <div class="pointer-events-none absolute -right-12 bottom-0 h-40 w-40 rounded-full bg-amber-300/20 blur-3xl"></div>
                            <div class="relative">
                                {{ $header }}
                            </div>
                        </div>
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="relative z-10 pb-16">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
