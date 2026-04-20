<x-app-layout>
    @php
        $heroTitle = $isAdmin ? 'Admin Dashboard' : 'My Dashboard';
        $heroLead = $isAdmin
            ? 'Tan-awa ang reservations, income, courts, ug rates from one control center.'
            : 'Check your bookings, receipts, and rain reschedule updates in one clean view.';
        $heroKicker = $isAdmin ? 'Operations Hub' : 'Player Portal';
        $heroMetaLabel = $isAdmin ? 'Current report range' : 'Reschedule window';
        $heroMetaValue = $isAdmin ? $reportRangeLabel : $customerBookingWindowDays . ' days';
        $heroFocusLabel = $isAdmin ? 'Booking focus date' : 'Account status';
        $heroFocusValue = $isAdmin
            ? \Illuminate\Support\Carbon::parse($selectedDate)->format('M d, Y')
            : 'Active member';
        $overviewCards = $isAdmin
            ? [
                ['label' => 'Registered Users', 'value' => number_format($stats['registered_users'])],
                ['label' => 'Active Courts', 'value' => number_format($stats['active_courts'])],
                ['label' => 'Range Bookings', 'value' => number_format($stats['range_bookings'])],
                ['label' => 'Walk-Ins', 'value' => number_format($stats['range_walk_in_reservations'])],
                ['label' => 'Paid Income', 'value' => 'PHP ' . number_format($stats['range_paid_income'], 2)],
            ]
            : [
                ['label' => 'My Reservations', 'value' => number_format($stats['my_reservations'])],
                ['label' => 'Upcoming', 'value' => number_format($stats['upcoming_reservations'])],
                ['label' => 'Paid', 'value' => number_format($stats['paid_reservations'])],
                ['label' => 'Pending Payment', 'value' => number_format($stats['pending_payments'])],
            ];
        $courtLookup = collect($courts ?? [])->keyBy('number');
        $resolveCourtName = function (int $courtNumber, ?string $courtName = null) use ($courtLookup) {
            $resolvedName = trim((string) ($courtName ?: data_get($courtLookup->get($courtNumber), 'name')));

            return $resolvedName !== '' ? $resolvedName : "Court {$courtNumber}";
        };
        $resolveCourtLabel = function (int $courtNumber, ?string $courtName = null) use ($resolveCourtName) {
            $resolvedName = $resolveCourtName($courtNumber, $courtName);
            $defaultName = "Court {$courtNumber}";

            return $resolvedName === $defaultName ? $defaultName : "{$resolvedName} ({$defaultName})";
        };
        $baseCourtRate = $reservationRate ?? 0;
        $courtSetupCount = max($courtCount, min(50, (int) old('court_count', $courtCount)));
        $courtSetupRows = collect(range(1, $courtSetupCount))->map(function (int $courtNumber) use ($courtLookup, $baseCourtRate) {
            $court = $courtLookup->get($courtNumber);

            return [
                'number' => $courtNumber,
                'name' => data_get($court, 'name', "Court {$courtNumber}"),
                'rate' => data_get($court, 'rate', $baseCourtRate),
                'day_rate' => data_get($court, 'day_rate', data_get($court, 'rate', $baseCourtRate)),
                'day_starts_at' => data_get($court, 'day_starts_at', '5:00 AM'),
                'day_ends_at' => data_get($court, 'day_ends_at', '5:00 PM'),
                'night_rate' => data_get($court, 'night_rate', data_get($court, 'day_rate', data_get($court, 'rate', $baseCourtRate))),
                'night_starts_at' => data_get($court, 'night_starts_at', '5:00 PM'),
                'night_ends_at' => data_get($court, 'night_ends_at', '12:00 AM'),
                'label' => data_get($court, 'label', "Court {$courtNumber}"),
                'rate_summary' => data_get(
                    $court,
                    'rate_summary',
                    data_get($court, 'day_starts_at', '5:00 AM') . ' - ' . data_get($court, 'day_ends_at', '5:00 PM') . ': PHP '
                    . number_format(data_get($court, 'day_rate', data_get($court, 'rate', $baseCourtRate)), 2)
                    . ' | ' . data_get($court, 'night_starts_at', '5:00 PM') . ' - ' . data_get($court, 'night_ends_at', '12:00 AM') . ': PHP '
                    . number_format(data_get($court, 'night_rate', data_get($court, 'day_rate', data_get($court, 'rate', $baseCourtRate))), 2)
                ),
            ];
        });
    @endphp

    <x-slot name="header">
        <div class="relative overflow-hidden">
            <div class="pointer-events-none absolute -left-12 top-0 h-32 w-32 rounded-full bg-sky-400/20 blur-3xl"></div>
            <div class="pointer-events-none absolute right-0 top-4 h-28 w-28 rounded-full bg-amber-300/20 blur-3xl"></div>

            <div class="relative flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between">
                <div class="max-w-2xl">
                    <span class="inline-flex items-center rounded-full border border-white/10 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.32em] text-sky-200">
                        {{ $heroKicker }}
                    </span>
                    <h2 class="mt-4 text-3xl font-semibold tracking-tight text-white sm:text-4xl">
                        {{ $heroTitle }}
                    </h2>
                    <p class="mt-3 max-w-xl text-sm leading-7 text-slate-300">
                        {{ $heroLead }}
                    </p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 xl:min-w-[28rem]">
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-4 backdrop-blur">
                        <p class="text-[0.68rem] uppercase tracking-[0.28em] text-slate-400">{{ $heroMetaLabel }}</p>
                        <p class="mt-2 text-lg font-semibold text-white">{{ $heroMetaValue }}</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-4 backdrop-blur">
                        <p class="text-[0.68rem] uppercase tracking-[0.28em] text-slate-400">{{ $heroFocusLabel }}</p>
                        <p class="mt-2 text-lg font-semibold text-white">{{ $heroFocusValue }}</p>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-800 dark:bg-rose-950/40 dark:text-rose-300">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-800 dark:bg-rose-950/40 dark:text-rose-300">
                    <ul class="list-disc space-y-1 ps-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="glass-panel px-6 py-7 sm:px-8">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div class="max-w-3xl">
                        <p class="text-xs font-semibold uppercase tracking-[0.34em] text-sky-600">
                            {{ $isAdmin ? 'Control Center' : 'Matchday Flow' }}
                        </p>
                        <h3 class="mt-3 text-2xl font-semibold tracking-tight text-slate-950">
                            {{ $isAdmin ? 'Review reservations, revenue, and court settings in one place.' : 'Everything you need before your next game is right here.' }}
                        </h3>
                        <p class="mt-3 text-sm leading-7 text-slate-600">
                            {{ $isAdmin ? 'Switch panels to monitor bookings, update rates, and keep the day moving smoothly.' : 'Check receipts, watch your payment status, and use rain unlocks as soon as the admin enables them.' }}
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        @if($isAdmin)
                            <a
                                href="{{ route('admin.dashboard', ['panel' => 'monitor', 'date' => $selectedDate, 'start_date' => $reportStartDate, 'end_date' => $reportEndDate]) }}"
                                class="accent-link border-slate-900 bg-slate-900 text-white hover:border-sky-600 hover:bg-sky-600"
                            >
                                Open Monitor
                            </a>
                            <a
                                href="{{ route('admin.dashboard', ['panel' => 'income', 'date' => $selectedDate, 'start_date' => $reportStartDate, 'end_date' => $reportEndDate]) }}"
                                class="accent-link border-slate-200 bg-white text-slate-700 hover:border-sky-200 hover:text-sky-700"
                            >
                                View Income
                            </a>
                        @else
                            <a
                                href="{{ route('reservations.index') }}"
                                class="accent-link border-slate-900 bg-slate-900 text-white hover:border-sky-600 hover:bg-sky-600"
                            >
                                Book Another Slot
                            </a>
                            <a
                                href="{{ route('profile.edit') }}"
                                class="accent-link border-slate-200 bg-white text-slate-700 hover:border-sky-200 hover:text-sky-700"
                            >
                                Manage Profile
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid gap-4 {{ $isAdmin ? 'md:grid-cols-2 xl:grid-cols-5' : 'md:grid-cols-2 xl:grid-cols-4' }}">
                @foreach($overviewCards as $card)
                    <div class="stat-card">
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-500">{{ $card['label'] }}</p>
                        <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">{{ $card['value'] }}</p>
                    </div>
                @endforeach
            </div>

            @if(! $isAdmin)
                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-900 dark:border-amber-800 dark:bg-amber-950/30 dark:text-amber-200">
                    <p class="font-semibold">No cancellation once paid or reserved.</p>
                    <p class="mt-2">
                        If mag-ulan and dili magamit ang court because walay atop, admin ra ang maka-unlock sa imong reschedule.
                        Once unlocked, you can choose a new date, court, and time within {{ $customerBookingWindowDays }} days from the original booking date.
                    </p>
                    @if(! $rescheduleFeatureReady)
                        <p class="mt-2 text-xs">
                            Rain reschedule setup is not ready yet. Admin needs to run the latest migration first.
                        </p>
                    @endif
                </div>

                @if($reschedulableReservations->isNotEmpty())
                    <div class="glass-panel p-6">
                        <div class="flex flex-col gap-2">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Rain Reschedule Unlocks</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Admin has unlocked these reservations for reschedule because of rain. Pili ug new schedule before the deadline.
                            </p>
                        </div>

                        <div class="mt-6 space-y-5">
                            @foreach($reschedulableReservations as $reservation)
                                @php
                                    $suggestedDate = $reservation->booking_date->copy()->addDay();
                                    if ($reservation->reschedule_deadline && $suggestedDate->gt($reservation->reschedule_deadline)) {
                                        $suggestedDate = $reservation->reschedule_deadline->copy();
                                    }
                                    if ($suggestedDate->lt(now()->startOfDay())) {
                                        $suggestedDate = now()->startOfDay();
                                    }
                                @endphp

                                <div class="rounded-2xl border border-indigo-200 bg-indigo-50/60 p-5 dark:border-indigo-800 dark:bg-indigo-950/20">
                                    <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
                                        <div>
                                            <p class="text-sm font-semibold text-indigo-700 dark:text-indigo-300">{{ $reservation->receipt_no }}</p>
                                            <p class="mt-1 text-sm text-gray-700 dark:text-gray-200">
                                                Current schedule: {{ $reservation->booking_date->format('M d, Y') }} at {{ $reservation->time_slot }}, {{ $resolveCourtLabel($reservation->court_number, $reservation->court_name) }}
                                            </p>
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                Unlock reason: {{ $reservation->reschedule_reason ?? 'Rain / uncovered court' }}
                                            </p>
                                        </div>
                                        <div class="rounded-xl bg-white px-4 py-3 text-sm text-gray-700 shadow-sm ring-1 ring-indigo-100 dark:bg-gray-900 dark:text-gray-200 dark:ring-indigo-900/40">
                                            Choose a new slot until
                                            <span class="font-semibold">{{ $reservation->reschedule_deadline?->format('M d, Y') }}</span>
                                        </div>
                                    </div>

                                    <form method="POST" action="{{ route('reservations.reschedule', $reservation) }}" class="mt-5 grid gap-4 md:grid-cols-3">
                                        @csrf

                                        <div>
                                            <label for="reschedule_date_{{ $reservation->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200">New date</label>
                                            <input
                                                id="reschedule_date_{{ $reservation->id }}"
                                                name="booking_date"
                                                type="date"
                                                min="{{ now()->toDateString() }}"
                                                max="{{ $reservation->reschedule_deadline?->toDateString() }}"
                                                value="{{ old('booking_date', $suggestedDate->toDateString()) }}"
                                                class="mt-2 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                                required
                                            >
                                        </div>

                                        <div>
                                            <label for="reschedule_court_{{ $reservation->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200">New court</label>
                                            <select
                                                id="reschedule_court_{{ $reservation->id }}"
                                                name="court_number"
                                                class="mt-2 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                                required
                                            >
                                                @foreach($courts as $court)
                                                    <option value="{{ $court['number'] }}" @selected((string) old('court_number', $reservation->court_number) === (string) $court['number'])>
                                                        {{ $court['label'] }} | {{ $court['rate_summary'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div>
                                            <label for="reschedule_time_{{ $reservation->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200">New time slot</label>
                                            <select
                                                id="reschedule_time_{{ $reservation->id }}"
                                                name="time_slot"
                                                class="mt-2 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                                required
                                            >
                                                @foreach($timeSlots as $slot)
                                                    <option value="{{ $slot }}" @selected(old('time_slot', $reservation->time_slot) === $slot)>{{ $slot }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="md:col-span-3">
                                            <x-primary-button>Submit Reschedule</x-primary-button>
                                        </div>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif

            @if($isAdmin)
                @php
                    $adminPanel = request()->query('panel', 'monitor');
                    if ($adminPanel === 'courts') {
                        $adminPanel = 'rates';
                    }

                    if (! in_array($adminPanel, ['income', 'monitor', 'rates', 'booking'], true)) {
                        $adminPanel = 'monitor';
                        }
                    @endphp

                <div class="space-y-6">
                        @if($adminPanel === 'income')
                            <div class="glass-panel p-6">
                                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Income Report</h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            Tan-awa ang income, number of bookings, ug customer names for last week or any custom range.
                                        </p>
                                        <p class="mt-2 text-sm font-medium text-indigo-600 dark:text-indigo-300">
                                            Current report: {{ $reportRangeLabel }}
                                        </p>
                                    </div>

                                    <div class="flex flex-wrap gap-2">
                                        @foreach($quickRanges as $range)
                                            <a
                                                href="{{ route('admin.dashboard', ['panel' => 'income', 'date' => $range['date'], 'start_date' => $range['start_date'], 'end_date' => $range['end_date']]) }}"
                                                class="inline-flex items-center rounded-full bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-200 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-700"
                                            >
                                                {{ $range['label'] }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>

                                <form method="GET" action="{{ $dashboardRoute }}" class="mt-6 grid gap-4 md:grid-cols-4">
                                    <input type="hidden" name="panel" value="income">
                                    <input type="hidden" name="date" value="{{ $selectedDate }}">

                                    <div>
                                        <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Start date</label>
                                        <input
                                            id="start_date"
                                            name="start_date"
                                            type="date"
                                            value="{{ $reportStartDate }}"
                                            class="mt-2 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                        >
                                    </div>

                                    <div>
                                        <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-200">End date</label>
                                        <input
                                            id="end_date"
                                            name="end_date"
                                            type="date"
                                            value="{{ $reportEndDate }}"
                                            class="mt-2 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                        >
                                    </div>

                                    <div class="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-700 dark:bg-gray-900/40">
                                        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Days with bookings</p>
                                        <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['report_days'] }}</p>
                                    </div>

                                    <div class="flex items-end">
                                        <x-primary-button>Generate Report</x-primary-button>
                                    </div>
                                </form>

                                <div class="mt-6 overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-700">
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead class="bg-gray-50 dark:bg-gray-900/40">
                                                <tr>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Date</th>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Bookings</th>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Paid Income</th>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Walk-Ins</th>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Customer Names</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                                                @forelse($dailyReports as $report)
                                                    <tr>
                                                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $report['date']->format('M d, Y') }}</td>
                                                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $report['booking_count'] }}</td>
                                                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">PHP {{ number_format($report['paid_income'], 2) }}</td>
                                                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $report['walk_in_count'] }}</td>
                                                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                                            <div class="flex flex-wrap gap-2">
                                                                @foreach($report['customer_names'] as $customerName)
                                                                    <span class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300">
                                                                        {{ $customerName }}
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                                            No bookings found in this date range.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @elseif($adminPanel === 'monitor')
                            <div class="glass-panel p-6">
                                <div class="flex flex-col gap-2">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Reservation Monitor</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Search by customer name, filter by booking date, and control rain reschedule access here.
                                    </p>
                                </div>

                                <form method="GET" action="{{ $dashboardRoute }}" class="mt-6 grid gap-4 md:grid-cols-[minmax(0,180px)_minmax(0,1fr)_auto]">
                                    <input type="hidden" name="panel" value="monitor">
                                    <input type="hidden" name="start_date" value="{{ $reportStartDate }}">
                                    <input type="hidden" name="end_date" value="{{ $reportEndDate }}">

                                    <div>
                                        <label for="monitor_date" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Booking date</label>
                                        <input
                                            id="monitor_date"
                                            name="date"
                                            type="date"
                                            value="{{ $selectedDate }}"
                                            class="mt-2 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                        >
                                    </div>

                                    <div>
                                        <label for="customer" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Search customer</label>
                                        <input
                                            id="customer"
                                            name="customer"
                                            type="text"
                                            value="{{ $customerSearch }}"
                                            placeholder="Type customer name"
                                            class="mt-2 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                        >
                                    </div>

                                    <div class="flex items-end gap-3">
                                        <x-primary-button>Search</x-primary-button>
                                        @if($customerSearch !== '')
                                            <a
                                                href="{{ route('admin.dashboard', ['panel' => 'monitor', 'date' => $selectedDate, 'start_date' => $reportStartDate, 'end_date' => $reportEndDate]) }}"
                                                class="inline-flex items-center rounded-xl bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-200 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-700"
                                            >
                                                Clear
                                            </a>
                                        @endif
                                    </div>
                                </form>

                                <div class="mt-6 overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-700">
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead class="bg-gray-50 dark:bg-gray-900/40">
                                                <tr>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Customer</th>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Contact</th>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Date</th>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Time</th>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Court</th>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Players</th>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Payment</th>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Receipt</th>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                                                @forelse($reservations as $reservation)
                                                    <tr>
                                                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">
                                                            {{ $reservation->customer_name }}
                                                        </td>
                                                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                                            <div class="flex flex-col gap-1">
                                                                <span>{{ $reservation->contact_number ?: 'No contact number' }}</span>
                                                                <span class="text-xs">
                                                                    {{ $reservation->user?->email ?? 'Walk-in / No account' }}
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $reservation->booking_date->format('M d, Y') }}</td>
                                                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $reservation->time_slot }}</td>
                                                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $resolveCourtLabel($reservation->court_number, $reservation->court_name) }}</td>
                                                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $reservation->players }}</td>
                                                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                                            {{ strtoupper($reservation->payment_method) }} / {{ $reservation->payment_status }}
                                                        </td>
                                                        <td class="px-4 py-3 text-sm">
                                                            <a href="{{ route('reservations.receipt', $reservation) }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                                {{ $reservation->receipt_no }}
                                                            </a>
                                                        </td>
                                                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                                            @if($reservation->user_id === null)
                                                                <span class="text-xs text-gray-500 dark:text-gray-400">Walk-in only</span>
                                                            @elseif(! $rescheduleFeatureReady)
                                                                <span class="text-xs text-amber-600 dark:text-amber-300">Run migrate first</span>
                                                            @elseif($reservation->isRescheduleUnlocked())
                                                                <div class="flex flex-col items-start gap-2">
                                                                    <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700 dark:bg-amber-950/30 dark:text-amber-300">
                                                                        Unlocked until {{ $reservation->reschedule_deadline?->format('M d, Y') }}
                                                                    </span>
                                                                    <form method="POST" action="{{ route('admin.reservations.lock-reschedule', $reservation) }}">
                                                                        @csrf
                                                                        <button
                                                                            type="submit"
                                                                            class="inline-flex items-center rounded-xl bg-gray-100 px-3 py-2 text-xs font-semibold text-gray-700 transition hover:bg-gray-200 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-700"
                                                                        >
                                                                            Lock Reschedule
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            @else
                                                                <form method="POST" action="{{ route('admin.reservations.unlock-reschedule', $reservation) }}">
                                                                    @csrf
                                                                    <button
                                                                        type="submit"
                                                                        class="inline-flex items-center rounded-xl bg-amber-100 px-3 py-2 text-xs font-semibold text-amber-700 transition hover:bg-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:hover:bg-amber-950/60"
                                                                    >
                                                                        Unlock Rain Reschedule
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="9" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                                            No reservations found for this date and customer search.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @elseif($adminPanel === 'booking')
                            <div class="grid gap-6 xl:grid-cols-[minmax(0,1.3fr)_minmax(320px,0.9fr)]">
                                <div class="glass-panel p-6">
                                    <div class="flex flex-col gap-2">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Admin Booking</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Create a walk-in or admin-assisted booking here. This form accepts both GCash and Cash payments.
                                        </p>
                                    </div>

                                    <form method="POST" action="{{ route('admin.walkins.store') }}" class="mt-6 space-y-6">
                                        @csrf

                                        <div class="grid gap-4 md:grid-cols-2">
                                            <div>
                                                <label for="walkin_customer_name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Customer name</label>
                                                <input
                                                    id="walkin_customer_name"
                                                    name="customer_name"
                                                    type="text"
                                                    value="{{ old('customer_name') }}"
                                                    class="mt-2 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                                    placeholder="Juan Dela Cruz"
                                                    required
                                                >
                                            </div>

                                            <div>
                                                <label for="walkin_contact_number" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Contact number</label>
                                                <input
                                                    id="walkin_contact_number"
                                                    name="contact_number"
                                                    type="text"
                                                    value="{{ old('contact_number') }}"
                                                    class="mt-2 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                                    placeholder="09XXXXXXXXX"
                                                    required
                                                >
                                            </div>
                                        </div>

                                        <div class="grid gap-4 md:grid-cols-3">
                                            <div>
                                                <label for="walkin_booking_date" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Booking date</label>
                                                <input
                                                    id="walkin_booking_date"
                                                    name="booking_date"
                                                    type="date"
                                                    value="{{ old('booking_date', $selectedDate) }}"
                                                    class="mt-2 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                                    required
                                                >
                                            </div>

                                            <div>
                                                <label for="walkin_court_number" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Court</label>
                                                <select
                                                    id="walkin_court_number"
                                                    name="court_number"
                                                    class="mt-2 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                                    required
                                                >
                                                    @foreach($courts as $court)
                                                        <option value="{{ $court['number'] }}" @selected((string) old('court_number', $courts[0]['number'] ?? 1) === (string) $court['number'])>
                                                            {{ $court['label'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                                    Court rate windows are listed on the right side for quick reference.
                                                </p>
                                            </div>

                                            <div>
                                                <label for="walkin_time_slot" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Time slot</label>
                                                <select
                                                    id="walkin_time_slot"
                                                    name="time_slot"
                                                    class="mt-2 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                                    required
                                                >
                                                    @foreach($timeSlots as $slot)
                                                        <option value="{{ $slot }}" @selected(old('time_slot') === $slot)>{{ $slot }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="grid gap-4 md:grid-cols-4">
                                            <div>
                                                <label for="walkin_players" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Players</label>
                                                <input
                                                    id="walkin_players"
                                                    name="players"
                                                    type="number"
                                                    min="1"
                                                    max="8"
                                                    value="{{ old('players', 4) }}"
                                                    class="mt-2 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                                    required
                                                >
                                            </div>

                                            <div>
                                                <label for="walkin_new_paddle_quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-200">New paddle qty</label>
                                                <input
                                                    id="walkin_new_paddle_quantity"
                                                    name="new_paddle_rent_quantity"
                                                    type="number"
                                                    min="0"
                                                    max="20"
                                                    value="{{ old('new_paddle_rent_quantity', 0) }}"
                                                    class="mt-2 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                                >
                                            </div>

                                            <div>
                                                <label for="walkin_old_paddle_quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Old paddle qty</label>
                                                <input
                                                    id="walkin_old_paddle_quantity"
                                                    name="paddle_rent_quantity"
                                                    type="number"
                                                    min="0"
                                                    max="20"
                                                    value="{{ old('paddle_rent_quantity', 0) }}"
                                                    class="mt-2 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                                >
                                            </div>

                                            <div>
                                                <label for="walkin_ball_quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Ball qty</label>
                                                <input
                                                    id="walkin_ball_quantity"
                                                    name="ball_quantity"
                                                    type="number"
                                                    min="0"
                                                    max="20"
                                                    value="{{ old('ball_quantity', 0) }}"
                                                    class="mt-2 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                                >
                                            </div>
                                        </div>

                                        <div class="grid gap-4 md:grid-cols-3">
                                            <div>
                                                <label for="walkin_payment_method" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Payment method</label>
                                                <select
                                                    id="walkin_payment_method"
                                                    name="payment_method"
                                                    class="mt-2 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                                    required
                                                >
                                                    <option value="cash" @selected(old('payment_method', 'cash') === 'cash')>Cash</option>
                                                    <option value="gcash" @selected(old('payment_method') === 'gcash')>GCash</option>
                                                </select>
                                            </div>

                                            <div>
                                                <label for="walkin_payment_status" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Payment status</label>
                                                <select
                                                    id="walkin_payment_status"
                                                    name="payment_status"
                                                    class="mt-2 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                                    required
                                                >
                                                    <option value="Paid" @selected(old('payment_status', 'Paid') === 'Paid')>Paid</option>
                                                    <option value="Unpaid" @selected(old('payment_status') === 'Unpaid')>Unpaid</option>
                                                </select>
                                            </div>

                                            <div>
                                                <label for="walkin_payment_reference" class="block text-sm font-medium text-gray-700 dark:text-gray-200">GCash reference</label>
                                                <input
                                                    id="walkin_payment_reference"
                                                    name="payment_reference"
                                                    type="text"
                                                    value="{{ old('payment_reference') }}"
                                                    class="mt-2 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                                    placeholder="Required for GCash only"
                                                >
                                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                                    Leave blank for cash bookings. If GCash is selected, this reference is required.
                                                </p>
                                            </div>
                                        </div>

                                        <div class="flex flex-wrap items-center gap-3">
                                            <x-primary-button>Create Booking</x-primary-button>
                                            <a
                                                href="{{ route('admin.dashboard', ['panel' => 'monitor', 'date' => old('booking_date', $selectedDate), 'start_date' => $reportStartDate, 'end_date' => $reportEndDate]) }}"
                                                class="inline-flex items-center rounded-xl bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-200 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-700"
                                            >
                                                Open Monitor
                                            </a>
                                        </div>
                                    </form>
                                </div>

                                <div class="space-y-6">
                                    <div class="glass-panel p-6">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Rental Quick Guide</h3>
                                        <div class="mt-5 grid gap-3 sm:grid-cols-3 xl:grid-cols-1">
                                            <div class="rounded-2xl border border-indigo-100 bg-indigo-50/70 px-4 py-4 text-sm text-indigo-900 dark:border-indigo-900/40 dark:bg-indigo-950/30 dark:text-indigo-100">
                                                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-indigo-500 dark:text-indigo-300">New Paddle</p>
                                                <p class="mt-2 text-2xl font-semibold">PHP {{ number_format($newPaddleRentRate, 2) }}</p>
                                            </div>
                                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm text-slate-900 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-100">
                                                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Old Paddle</p>
                                                <p class="mt-2 text-2xl font-semibold">PHP {{ number_format($oldPaddleRentRate, 2) }}</p>
                                            </div>
                                            <div class="rounded-2xl border border-amber-100 bg-amber-50/80 px-4 py-4 text-sm text-amber-900 dark:border-amber-900/40 dark:bg-amber-950/30 dark:text-amber-100">
                                                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-amber-500 dark:text-amber-300">Ball</p>
                                                <p class="mt-2 text-2xl font-semibold">PHP {{ number_format($ballRate, 2) }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="glass-panel p-6">
                                        <div class="flex flex-col gap-2">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Court Rate Guide</h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                The final booking amount follows the selected court and time slot, then adds the chosen rentals.
                                            </p>
                                        </div>

                                        <div class="mt-5 grid gap-3">
                                            @foreach($courts as $court)
                                                <div class="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-4 dark:border-gray-700 dark:bg-gray-900/40">
                                                    <div class="flex items-center justify-between gap-3">
                                                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $court['label'] }}</p>
                                                        <span class="text-xs uppercase tracking-[0.24em] text-gray-400">Court {{ $court['number'] }}</span>
                                                    </div>
                                                    <p class="mt-2 text-xs leading-6 text-gray-500 dark:text-gray-400">{{ $court['rate_summary'] }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($adminPanel === 'rates')
                            <div class="glass-panel p-6">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Rental Rates</h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            Set the paddle and ball rental prices shown on the booking page.
                                        </p>
                                    </div>
                                    <div class="rounded-xl bg-indigo-50 px-4 py-3 text-sm text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300">
                                        Old paddle: <span class="font-semibold">PHP {{ number_format($oldPaddleRentRate, 2) }}</span>
                                        | New paddle: <span class="font-semibold">PHP {{ number_format($newPaddleRentRate, 2) }}</span>
                                    </div>
                                </div>

                                @if(! $rateSettingsReady)
                                    <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:border-amber-800 dark:bg-amber-950/30 dark:text-amber-200">
                                        Rate settings columns are not ready yet. Run <code>php artisan migrate</code> first before saving changes.
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('admin.rates.update') }}" class="mt-6 grid gap-4 md:grid-cols-3">
                                    @csrf

                                    <div>
                                        <label for="paddle_rent_rate" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Old paddle rate</label>
                                        <input
                                            id="paddle_rent_rate"
                                            name="paddle_rent_rate"
                                            type="number"
                                            min="0"
                                            value="{{ old('paddle_rent_rate', $oldPaddleRentRate) }}"
                                            class="mt-2 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                            required
                                        >
                                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                            Daan nga paddle rate. Example: PHP 50.
                                        </p>
                                    </div>

                                    <div>
                                        <label for="new_paddle_rent_rate" class="block text-sm font-medium text-gray-700 dark:text-gray-200">New paddle rate</label>
                                        <input
                                            id="new_paddle_rent_rate"
                                            name="new_paddle_rent_rate"
                                            type="number"
                                            min="0"
                                            value="{{ old('new_paddle_rent_rate', $newPaddleRentRate) }}"
                                            class="mt-2 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                            required
                                        >
                                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                            Bag-o nga paddle rate. Example: PHP 60.
                                        </p>
                                    </div>

                                    <div>
                                        <label for="ball_rate" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Ball rate</label>
                                        <input
                                            id="ball_rate"
                                            name="ball_rate"
                                            type="number"
                                            min="0"
                                            value="{{ old('ball_rate', $ballRate) }}"
                                            class="mt-2 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                            required
                                        >
                                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                            These rental prices are shown to customers and admin for quick reference.
                                        </p>
                                    </div>

                                    <div class="md:col-span-3">
                                        <x-primary-button>Update Rates</x-primary-button>
                                    </div>
                                </form>
                            </div>

                            <div class="glass-panel p-6">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Court Setup</h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            Admin can change pila ka courts ang available plus set a custom name and two editable time-based rates for every court.
                                        </p>
                                    </div>
                                    <div class="rounded-xl bg-indigo-50 px-4 py-3 text-sm text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300">
                                        Current courts: <span class="font-semibold">{{ $courtCount }}</span>
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('admin.courts.update') }}" class="mt-6 space-y-4">
                                    @csrf
                                    <div class="grid gap-4 lg:grid-cols-[220px_minmax(0,1fr)]">
                                        <div>
                                            <label for="court_count" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Number of courts</label>
                                            <input
                                                id="court_count"
                                                name="court_count"
                                                type="number"
                                                min="{{ $minCourtCountAllowed }}"
                                                max="50"
                                                value="{{ old('court_count', $courtCount) }}"
                                                class="mt-2 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                            >
                                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                                Minimum allowed right now: {{ $minCourtCountAllowed }} court(s), based on active reservations.
                                            </p>
                                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                                Default fallback rate for new courts: PHP {{ number_format($reservationRate, 2) }}
                                            </p>
                                        </div>

                                        <div class="grid gap-4 md:grid-cols-2">
                                            @foreach($courtSetupRows as $court)
                                                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/40">
                                                    <div class="flex items-center justify-between gap-3">
                                                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $court['label'] }}</p>
                                                        <span class="text-xs uppercase tracking-[0.24em] text-gray-400">Court {{ $court['number'] }}</span>
                                                    </div>
                                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ $court['rate_summary'] }}</p>

                                                    <div class="mt-4">
                                                        <label for="court_name_{{ $court['number'] }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Court name</label>
                                                        <input
                                                            id="court_name_{{ $court['number'] }}"
                                                            name="court_names[{{ $court['number'] }}]"
                                                            type="text"
                                                            value="{{ old('court_names.' . $court['number'], $court['name']) }}"
                                                            class="mt-2 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                                            placeholder="Court {{ $court['number'] }}"
                                                        >
                                                    </div>

                                                    <div class="mt-4 grid gap-4">
                                                        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-950/40">
                                                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-gray-400">Rate Window 1</p>
                                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Example: 5:00 AM to 5:00 PM = PHP 120</p>

                                                            <div class="mt-3 grid gap-3 sm:grid-cols-3">
                                                                <div>
                                                                    <label for="court_day_start_{{ $court['number'] }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200">From</label>
                                                                    <select
                                                                        id="court_day_start_{{ $court['number'] }}"
                                                                        name="court_day_starts[{{ $court['number'] }}]"
                                                                        class="mt-2 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                                                    >
                                                                        @foreach($rateBoundaryOptions as $boundary)
                                                                            <option value="{{ $boundary }}" @selected(old('court_day_starts.' . $court['number'], $court['day_starts_at']) === $boundary)>
                                                                                {{ $boundary }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div>
                                                                    <label for="court_day_end_{{ $court['number'] }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200">To</label>
                                                                    <select
                                                                        id="court_day_end_{{ $court['number'] }}"
                                                                        name="court_day_ends[{{ $court['number'] }}]"
                                                                        class="mt-2 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                                                    >
                                                                        @foreach($rateBoundaryOptions as $boundary)
                                                                            <option value="{{ $boundary }}" @selected(old('court_day_ends.' . $court['number'], $court['day_ends_at']) === $boundary)>
                                                                                {{ $boundary }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div>
                                                                    <label for="court_day_rate_{{ $court['number'] }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Price</label>
                                                                    <input
                                                                        id="court_day_rate_{{ $court['number'] }}"
                                                                        name="court_day_rates[{{ $court['number'] }}]"
                                                                        type="number"
                                                                        min="0"
                                                                        value="{{ old('court_day_rates.' . $court['number'], $court['day_rate']) }}"
                                                                        class="mt-2 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                                                        placeholder="PHP {{ number_format($reservationRate, 2) }}"
                                                                    >
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-950/40">
                                                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-gray-400">Rate Window 2</p>
                                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Example: 5:00 PM to 12:00 AM = PHP 150</p>

                                                            <div class="mt-3 grid gap-3 sm:grid-cols-3">
                                                                <div>
                                                                    <label for="court_night_start_{{ $court['number'] }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200">From</label>
                                                                    <select
                                                                        id="court_night_start_{{ $court['number'] }}"
                                                                        name="court_night_starts[{{ $court['number'] }}]"
                                                                        class="mt-2 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                                                    >
                                                                        @foreach($rateBoundaryOptions as $boundary)
                                                                            <option value="{{ $boundary }}" @selected(old('court_night_starts.' . $court['number'], $court['night_starts_at']) === $boundary)>
                                                                                {{ $boundary }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div>
                                                                    <label for="court_night_end_{{ $court['number'] }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200">To</label>
                                                                    <select
                                                                        id="court_night_end_{{ $court['number'] }}"
                                                                        name="court_night_ends[{{ $court['number'] }}]"
                                                                        class="mt-2 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                                                    >
                                                                        @foreach($rateBoundaryOptions as $boundary)
                                                                            <option value="{{ $boundary }}" @selected(old('court_night_ends.' . $court['number'], $court['night_ends_at']) === $boundary)>
                                                                                {{ $boundary }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div>
                                                                    <label for="court_night_rate_{{ $court['number'] }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Price</label>
                                                                    <input
                                                                        id="court_night_rate_{{ $court['number'] }}"
                                                                        name="court_night_rates[{{ $court['number'] }}]"
                                                                        type="number"
                                                                        min="0"
                                                                        value="{{ old('court_night_rates.' . $court['number'], $court['night_rate']) }}"
                                                                        class="mt-2 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                                                        placeholder="PHP {{ number_format($reservationRate, 2) }}"
                                                                    >
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <x-primary-button>Update Court Setup</x-primary-button>
                                </form>
                            </div>
                        @endif
                </div>
            @endif

            @if(! $isAdmin)
                <div class="glass-panel p-6">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                Your Reservation History
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Use this page to check your bookings and open the receipt anytime.
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <a
                                href="{{ route('reservations.index') }}"
                                class="inline-flex items-center rounded-xl bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-700 dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-gray-300"
                            >
                                Book Another Slot
                            </a>
                        </div>
                    </div>

                    <div class="mt-6 overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-700">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900/40">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Date</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Time</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Court</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Players</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Payment</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Receipt</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                                    @forelse($reservations as $reservation)
                                        <tr>
                                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $reservation->booking_date->format('M d, Y') }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $reservation->time_slot }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $resolveCourtLabel($reservation->court_number, $reservation->court_name) }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $reservation->players }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                                {{ strtoupper($reservation->payment_method) }} / {{ $reservation->payment_status }}
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                <a href="{{ route('reservations.receipt', $reservation) }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                    {{ $reservation->receipt_no }}
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                                You do not have any reservations yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
