@php
    $user = Auth::user();
    $isAdmin = $user->isAdmin();
    $userInitials = collect(explode(' ', trim($user->name)))
        ->filter()
        ->map(fn (string $part) => strtoupper(substr($part, 0, 1)))
        ->take(2)
        ->implode('');

    $sectionLinks = $isAdmin
        ? [
            [
                'label' => 'Reservation Monitor',
                'href' => route('admin.dashboard', ['panel' => 'monitor']),
                'active' => request()->routeIs('admin.dashboard') && request()->query('panel', 'monitor') === 'monitor',
            ],
            [
                'label' => 'Booking',
                'href' => route('admin.dashboard', ['panel' => 'booking']),
                'active' => request()->routeIs('admin.dashboard') && request()->query('panel') === 'booking',
            ],
            [
                'label' => 'Income Report',
                'href' => route('admin.dashboard', ['panel' => 'income']),
                'active' => request()->routeIs('admin.dashboard') && request()->query('panel') === 'income',
            ],
            [
                'label' => 'Rates, Rentals & Courts',
                'href' => route('admin.dashboard', ['panel' => 'rates']),
                'active' => request()->routeIs('admin.dashboard') && request()->query('panel') === 'rates',
            ],
        ]
        : [
            [
                'label' => 'My Dashboard',
                'href' => route('dashboard'),
                'active' => request()->routeIs('dashboard'),
            ],
            [
                'label' => 'Book a Court',
                'href' => route('reservations.index'),
                'active' => request()->routeIs('reservations.*'),
            ],
        ];
@endphp

<nav class="relative z-30">
    <div class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
        <div class="rounded-[1.75rem] border border-white/60 bg-white/80 px-4 shadow-xl shadow-slate-900/5 backdrop-blur-xl">
            <div class="flex min-h-[78px] items-center justify-between gap-4">
                <div class="flex min-w-0 items-center gap-3">
                    <x-dropdown align="left" width="w-72">
                        <x-slot name="trigger">
                            <button class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 shadow-sm transition duration-200 hover:border-slate-300 hover:text-slate-950 focus:outline-none">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="border-b border-slate-200/80 px-4 py-3">
                                <p class="text-[0.68rem] font-semibold uppercase tracking-[0.3em] text-slate-400">Sections</p>
                                <p class="mt-1 text-sm text-slate-600">{{ $isAdmin ? 'Open the admin sections from here.' : 'Quick access to your main pages.' }}</p>
                            </div>

                            <div class="space-y-1 p-2">
                                @foreach($sectionLinks as $link)
                                    <a
                                        href="{{ $link['href'] }}"
                                        class="block rounded-2xl px-4 py-3 text-sm font-medium transition {{ $link['active'] ? 'bg-slate-900 text-white shadow-lg shadow-slate-900/10' : 'text-slate-700 hover:bg-slate-100/80 hover:text-slate-950' }}"
                                    >
                                        {{ $link['label'] }}
                                    </a>
                                @endforeach
                            </div>

                            <div class="border-t border-slate-200/80 p-2">
                                <a
                                    href="{{ config('services.facebook.page_url') }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="flex items-center gap-3 rounded-2xl bg-[#1877F2] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#1668d1]"
                                >
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M13.5 22v-8.2h2.8l.4-3.2h-3.2V8.55c0-.93.27-1.55 1.6-1.55h1.7V4.1c-.3-.04-1.33-.1-2.52-.1-2.5 0-4.2 1.5-4.2 4.3v2.3H8v3.2h2.6V22h2.9Z"/>
                                    </svg>
                                    Facebook Page
                                </a>
                            </div>
                        </x-slot>
                    </x-dropdown>

                    <a href="{{ route('reservations.index') }}" class="flex min-w-0 items-center gap-3">
                        <x-application-logo class="block h-12 w-12 shrink-0" />
                        <div class="hidden min-w-0 sm:block">
                            <div class="text-sm font-semibold tracking-tight text-slate-950">Pickle BALLan Ni Juan</div>
                            <div class="text-[0.68rem] uppercase tracking-[0.34em] text-slate-500">Court Booking Hub</div>
                        </div>
                    </a>
                </div>

                <x-dropdown align="right" width="w-72">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-3 rounded-full border border-slate-200 bg-white px-2.5 py-2 text-left text-sm font-semibold text-slate-700 shadow-sm transition duration-200 hover:border-slate-300 hover:text-slate-950 focus:outline-none">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-slate-900 text-sm font-bold text-white">
                                {{ $userInitials !== '' ? $userInitials : 'U' }}
                            </span>
                            <span class="hidden sm:block">
                                <span class="block text-sm font-semibold text-slate-900">{{ $user->name }}</span>
                                <span class="block text-[0.68rem] uppercase tracking-[0.28em] text-slate-500">{{ $isAdmin ? 'Admin' : 'Member' }}</span>
                            </span>
                            <svg class="h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="border-b border-slate-200/80 px-4 py-3">
                            <p class="text-sm font-semibold text-slate-900">{{ $user->name }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $user->email }}</p>
                        </div>

                        <div class="space-y-1 p-2">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>
</nav>
