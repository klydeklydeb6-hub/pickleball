<x-app-layout>
    @php
        $heroTitle = $isAdmin ? 'Admin Dashboard' : 'My Dashboard';
        $heroLead = $isAdmin
            ? 'Tan-awa ang live dashboard, reservations, income, courts, ug rates from one control center.'
            : 'Check your bookings, receipts, and rain reschedule updates in one clean view.';
        $heroKicker = $isAdmin ? 'Operations Hub' : 'Player Portal';
        $heroMetaLabel = $isAdmin ? 'Current report range' : 'Reschedule window';
        $heroMetaValue = $isAdmin ? $reportRangeLabel : $customerBookingWindowDays . ' days';
        $heroFocusLabel = $isAdmin ? 'Booking focus date' : 'Account status';
        $heroFocusValue = $isAdmin
            ? \Illuminate\Support\Carbon::parse($selectedDate)->format('M d, Y')
            : 'Active member';
        $adminPanel = $isAdmin ? request()->query('panel', 'analytics') : null;
        if ($adminPanel === 'dashboard') {
            $adminPanel = 'analytics';
        }
        if ($adminPanel === 'courts') {
            $adminPanel = 'rates';
        }
        if ($isAdmin && ! in_array($adminPanel, ['income', 'analytics', 'monitor', 'rates', 'booking'], true)) {
            $adminPanel = 'analytics';
        }
        $dashboardContainerClass = $isAdmin ? 'max-w-[1920px] 2xl:max-w-[2080px]' : 'max-w-7xl';
        $showOverviewCards = ! $isAdmin || $adminPanel === 'income';
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
        <div class="mx-auto {{ $dashboardContainerClass }} space-y-6 px-4 sm:px-6 lg:px-8">
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
                <div class="flex flex-col gap-5">
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

                    @unless($isAdmin)
                        <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                            <a
                                href="{{ route('reservations.index') }}"
                                class="accent-link w-full border-slate-900 bg-slate-900 text-white hover:border-sky-600 hover:bg-sky-600 sm:w-auto"
                            >
                                Book Now
                            </a>
                            <a
                                href="{{ route('profile.edit') }}"
                                class="accent-link w-full border-slate-200 bg-white text-slate-700 hover:border-sky-200 hover:text-sky-700 sm:w-auto"
                            >
                                Manage Profile
                            </a>
                        </div>
                    @endunless
                </div>
            </div>

            @if($showOverviewCards)
                <div class="grid gap-4 {{ $isAdmin ? 'md:grid-cols-2 xl:grid-cols-5' : 'md:grid-cols-2 xl:grid-cols-4' }}">
                    @foreach($overviewCards as $card)
                        <div class="stat-card">
                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-500">{{ $card['label'] }}</p>
                            <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">{{ $card['value'] }}</p>
                        </div>
                    @endforeach
                </div>
            @endif

            @if(! $isAdmin)
                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-900 dark:border-amber-800 dark:bg-amber-950/30 dark:text-amber-200">
                    <p class="font-semibold">No cancellation once a booking is confirmed.</p>
                    <p class="mt-2">
                        If rain or uncovered court conditions affect play, only the admin can unlock your reservation for reschedule.
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
                                                Current schedule: {{ $reservation->booking_date->format('M d, Y') }} at {{ $reservation->timeRangeLabel() }}, {{ $resolveCourtLabel($reservation->court_number, $reservation->court_name) }} ({{ $reservation->durationLabel() }})
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
                                            <label for="reschedule_time_{{ $reservation->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200">New start time</label>
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
                        @elseif($adminPanel === 'analytics')
                            @php
                                $maxDailyBookings = max(1, (int) ($analyticsSummary['max_daily_bookings'] ?? 0));
                                $hasAnalyticsBookings = ($analyticsSummary['max_daily_bookings'] ?? 0) > 0;
                                $analyticsTotalBookings = max(1, (int) ($stats['range_bookings'] ?? 0));
                                $paidBookingPercentage = (int) round((($analyticsSummary['paid_bookings'] ?? 0) / $analyticsTotalBookings) * 100);
                                $walkInPercentage = (int) round((($analyticsSummary['walk_in_bookings'] ?? 0) / $analyticsTotalBookings) * 100);
                                $memberBookingPercentage = (int) round((($analyticsSummary['member_bookings'] ?? 0) / $analyticsTotalBookings) * 100);
                                $pendingBookingPercentage = (int) round((($analyticsSummary['pending_or_unpaid_bookings'] ?? 0) / $analyticsTotalBookings) * 100);
                                $topCourtMaxBookings = max(1, (int) $analyticsTopCourts->max('booking_count'));
                                $topIncomeDays = $analyticsSeries
                                    ->sortByDesc('paid_income')
                                    ->take(5)
                                    ->values();
                                $dashboardSummaryCards = [
                                    [
                                        'label' => 'Visitors',
                                        'value' => number_format($stats['range_unique_visitors']),
                                        'note' => 'Unique customers in the selected range.',
                                        'subnote' => number_format($stats['range_repeat_customers']) . ' returning customer' . ($stats['range_repeat_customers'] === 1 ? '' : 's'),
                                        'panel' => 'bg-[linear-gradient(135deg,#0f172a_0%,#1d4ed8_55%,#38bdf8_100%)] text-white',
                                        'accent' => 'text-sky-100/90',
                                        'badge' => 'Live traffic',
                                    ],
                                    [
                                        'label' => 'Users',
                                        'value' => number_format($stats['registered_users']),
                                        'note' => 'Registered member accounts in the system.',
                                        'subnote' => number_format($analyticsSummary['member_bookings']) . ' member bookings inside this range',
                                        'panel' => 'bg-[linear-gradient(135deg,#111827_0%,#334155_55%,#64748b_100%)] text-white',
                                        'accent' => 'text-slate-200/90',
                                        'badge' => 'Members',
                                    ],
                                    [
                                        'label' => 'Booking',
                                        'value' => number_format($stats['range_bookings']),
                                        'note' => 'Total reservations recorded for this dashboard view.',
                                        'subnote' => number_format($analyticsSummary['average_bookings_per_day'], 1) . ' average bookings per day',
                                        'panel' => 'bg-[linear-gradient(135deg,#0f766e_0%,#10b981_55%,#6ee7b7_100%)] text-white',
                                        'accent' => 'text-emerald-100/90',
                                        'badge' => 'Reservations',
                                    ],
                                    [
                                        'label' => 'Repeat Booking',
                                        'value' => number_format($stats['range_repeat_bookings']),
                                        'note' => 'Extra bookings created by returning customers.',
                                        'subnote' => number_format($stats['range_walk_in_reservations']) . ' walk-in bookings in this range',
                                        'panel' => 'bg-[linear-gradient(135deg,#7c2d12_0%,#ea580c_52%,#fdba74_100%)] text-white',
                                        'accent' => 'text-orange-100/90',
                                        'badge' => 'Retention',
                                    ],
                                ];
                                $dashboardProgressRows = [
                                    [
                                        'label' => 'Paid bookings',
                                        'value' => $analyticsSummary['paid_bookings'],
                                        'total' => $stats['range_bookings'],
                                        'width' => $paidBookingPercentage,
                                        'bar' => 'bg-blue-500',
                                    ],
                                    [
                                        'label' => 'Member bookings',
                                        'value' => $analyticsSummary['member_bookings'],
                                        'total' => $stats['range_bookings'],
                                        'width' => $memberBookingPercentage,
                                        'bar' => 'bg-slate-800',
                                    ],
                                    [
                                        'label' => 'Walk-in bookings',
                                        'value' => $analyticsSummary['walk_in_bookings'],
                                        'total' => $stats['range_bookings'],
                                        'width' => $walkInPercentage,
                                        'bar' => 'bg-emerald-500',
                                    ],
                                    [
                                        'label' => 'Pending / unpaid',
                                        'value' => $analyticsSummary['pending_or_unpaid_bookings'],
                                        'total' => $stats['range_bookings'],
                                        'width' => $pendingBookingPercentage,
                                        'bar' => 'bg-amber-400',
                                    ],
                                ];
                                $dashboardSignalCards = [
                                    [
                                        'label' => 'Average / day',
                                        'value' => number_format($analyticsSummary['average_bookings_per_day'], 1),
                                        'note' => 'Booking count across the selected range.',
                                        'panel' => 'bg-amber-400 text-slate-950',
                                        'accent' => 'text-amber-950/70',
                                    ],
                                    [
                                        'label' => 'Peak booking day',
                                        'value' => data_get($analyticsPeakBookingDay, 'date') ? $analyticsPeakBookingDay['date']->format('M d, Y') : 'No data yet',
                                        'note' => number_format((int) data_get($analyticsPeakBookingDay, 'booking_count', 0)) . ' bookings on the busiest day.',
                                        'panel' => 'bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-700 text-white',
                                        'accent' => 'text-blue-100',
                                    ],
                                    [
                                        'label' => 'Best income day',
                                        'value' => data_get($analyticsPeakIncomeDay, 'date') ? $analyticsPeakIncomeDay['date']->format('M d, Y') : 'No income yet',
                                        'note' => 'PHP ' . number_format((float) data_get($analyticsPeakIncomeDay, 'paid_income', 0), 2) . ' paid income on the strongest day.',
                                        'panel' => 'bg-gradient-to-br from-emerald-500 via-teal-600 to-emerald-700 text-white',
                                        'accent' => 'text-emerald-100',
                                    ],
                                    [
                                        'label' => 'Days analyzed',
                                        'value' => number_format($analyticsSummary['range_days']),
                                        'note' => 'Custom dashboard reporting window.',
                                        'panel' => 'bg-gradient-to-br from-slate-800 via-slate-700 to-slate-600 text-white',
                                        'accent' => 'text-slate-200',
                                    ],
                                ];
                                $recentDashboardBookings = $reservations
                                    ->take(8)
                                    ->map(fn ($reservation) => [
                                        'name' => $reservation->customer_name,
                                        'date' => $reservation->booking_date,
                                        'schedule' => $reservation->timeRangeLabel(),
                                        'court' => $resolveCourtLabel($reservation->court_number, $reservation->court_name),
                                        'payment_status' => $reservation->payment_status,
                                        'receipt_no' => $reservation->receipt_no,
                                    ])
                                    ->take(8)
                                    ->values();
                                $chartPointCount = max(1, $analyticsSeries->count());
                                $chartWidth = max(980, $chartPointCount * 122);
                                $chartHeight = 290;
                                $chartBottom = 242;
                                $chartPlotHeight = 178;
                                $maxPaidIncome = max(1, (float) $analyticsSeries->max('paid_income'));
                                $bookingPolylinePoints = $analyticsSeries
                                    ->values()
                                    ->map(function (array $day, int $index) use ($chartPointCount, $chartWidth, $chartBottom, $chartPlotHeight, $maxDailyBookings) {
                                        $x = $chartPointCount > 1
                                            ? 32 + (($chartWidth - 64) / ($chartPointCount - 1)) * $index
                                            : $chartWidth / 2;
                                        $y = $chartBottom - (($day['booking_count'] / $maxDailyBookings) * $chartPlotHeight);

                                        return number_format($x, 2, '.', '') . ',' . number_format($y, 2, '.', '');
                                    })
                                    ->implode(' ');
                                $incomePolylinePoints = $analyticsSeries
                                    ->values()
                                    ->map(function (array $day, int $index) use ($chartPointCount, $chartWidth, $chartBottom, $chartPlotHeight, $maxPaidIncome) {
                                        $x = $chartPointCount > 1
                                            ? 32 + (($chartWidth - 64) / ($chartPointCount - 1)) * $index
                                            : $chartWidth / 2;
                                        $y = $chartBottom - (($day['paid_income'] / $maxPaidIncome) * $chartPlotHeight);

                                        return number_format($x, 2, '.', '') . ',' . number_format($y, 2, '.', '');
                                    })
                                    ->implode(' ');
                                $chartGridLines = collect([0, 25, 50, 75, 100]);
                                $courtSharePalette = ['#2563eb', '#0f766e', '#f59e0b', '#0f172a', '#7c3aed'];
                                $courtShareLegend = [];
                                $currentAngle = 0;

                                foreach ($analyticsTopCourts as $index => $court) {
                                    $percentage = $stats['range_bookings'] > 0
                                        ? round(($court['booking_count'] / $stats['range_bookings']) * 100, 1)
                                        : 0;
                                    $angle = ($percentage / 100) * 360;

                                    if ($angle <= 0) {
                                        continue;
                                    }

                                    $color = $courtSharePalette[$index % count($courtSharePalette)];
                                    $courtShareLegend[] = [
                                        'label' => $court['label'],
                                        'percentage' => $percentage,
                                        'booking_count' => $court['booking_count'],
                                        'color' => $color,
                                    ];
                                    $currentAngle += $angle;
                                }

                                if ($stats['range_bookings'] > 0 && $currentAngle < 360) {
                                    $othersPercentage = max(0, round((($stats['range_bookings'] - $analyticsTopCourts->sum('booking_count')) / $stats['range_bookings']) * 100, 1));
                                    if ($othersPercentage > 0) {
                                        $courtShareLegend[] = [
                                            'label' => 'Others',
                                            'percentage' => $othersPercentage,
                                            'booking_count' => max(0, $stats['range_bookings'] - $analyticsTopCourts->sum('booking_count')),
                                            'color' => '#cbd5e1',
                                        ];
                                    }
                                }

                                $donutRadius = 78;
                                $donutCircumference = 2 * pi() * $donutRadius;
                                $donutGapLength = 8;
                                $donutOffset = 0.0;
                                $donutSegments = collect($courtShareLegend)
                                    ->map(function (array $courtShare) use (&$donutOffset, $donutCircumference, $donutGapLength) {
                                        $rawLength = ($courtShare['percentage'] / 100) * $donutCircumference;
                                        $segment = [
                                            'label' => $courtShare['label'],
                                            'color' => $courtShare['color'],
                                            'percentage' => $courtShare['percentage'],
                                            'booking_count' => $courtShare['booking_count'],
                                            'length' => max(0, $rawLength - $donutGapLength),
                                            'offset' => $donutOffset,
                                        ];

                                        $donutOffset += $rawLength;

                                        return $segment;
                                    })
                                    ->filter(fn (array $segment) => $segment['length'] > 0)
                                    ->values();
                                $topCourtShare = $donutSegments->first();
                            @endphp

                            <div class="space-y-6">
                                <div class="glass-panel relative overflow-hidden p-6 sm:p-8">
                                    <div class="pointer-events-none absolute -left-10 top-0 h-40 w-40 rounded-full bg-sky-300/20 blur-3xl"></div>
                                    <div class="pointer-events-none absolute right-0 top-8 h-40 w-40 rounded-full bg-amber-200/25 blur-3xl"></div>

                                    <div class="relative flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
                                        <div class="max-w-2xl">
                                            <p class="text-xs font-semibold uppercase tracking-[0.34em] text-sky-600">Booking Analytics</p>
                                            <h3 class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">Dashboard</h3>
                                            <p class="mt-3 text-sm leading-7 text-slate-600">
                                                Top summary cards show the key counts first, then the deeper booking analytics stay visible below for daily monitoring.
                                            </p>
                                            <p class="mt-3 inline-flex rounded-full border border-sky-100 bg-sky-50/85 px-4 py-2 text-sm font-medium text-sky-700">
                                                Dashboard range: {{ $reportRangeLabel }}
                                            </p>
                                        </div>

                                        <div class="flex flex-wrap gap-2">
                                            @foreach($quickRanges as $range)
                                                <a
                                                    href="{{ route('admin.dashboard', ['panel' => 'analytics', 'date' => $range['date'], 'start_date' => $range['start_date'], 'end_date' => $range['end_date']]) }}"
                                                    class="inline-flex items-center rounded-full border border-sky-100 bg-white/90 px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700"
                                                >
                                                    {{ $range['label'] }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>

                                    <form method="GET" action="{{ $dashboardRoute }}" class="relative mt-8 grid gap-4 rounded-[2rem] border border-sky-100 bg-slate-50/90 p-4 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto] lg:items-end">
                                        <input type="hidden" name="panel" value="analytics">
                                        <input type="hidden" name="date" value="{{ $selectedDate }}">

                                        <div>
                                            <label for="analytics_start_date" class="block text-sm font-medium text-slate-700">Start date</label>
                                            <input
                                                id="analytics_start_date"
                                                name="start_date"
                                                type="date"
                                                value="{{ $reportStartDate }}"
                                                class="mt-2 w-full rounded-2xl border border-sky-100 bg-white shadow-sm focus:border-sky-400 focus:ring-sky-400"
                                            >
                                        </div>

                                        <div>
                                            <label for="analytics_end_date" class="block text-sm font-medium text-slate-700">End date</label>
                                            <input
                                                id="analytics_end_date"
                                                name="end_date"
                                                type="date"
                                                value="{{ $reportEndDate }}"
                                                class="mt-2 w-full rounded-2xl border border-sky-100 bg-white shadow-sm focus:border-sky-400 focus:ring-sky-400"
                                            >
                                        </div>

                                        <div class="flex items-end">
                                            <x-primary-button>Refresh Dashboard</x-primary-button>
                                        </div>
                                    </form>
                                </div>

                                <div class="grid gap-5 md:grid-cols-2 2xl:grid-cols-4">
                                    @foreach($dashboardSummaryCards as $summaryCard)
                                        <div class="{{ $summaryCard['panel'] }} relative overflow-hidden rounded-[2rem] px-6 py-6 shadow-[0_22px_60px_rgba(15,23,42,0.16)]">
                                            <div class="pointer-events-none absolute -right-8 top-0 h-28 w-28 rounded-full bg-white/10 blur-2xl"></div>
                                            <div class="pointer-events-none absolute bottom-0 left-0 h-24 w-24 rounded-full bg-black/10 blur-2xl"></div>

                                            <div class="relative flex h-full flex-col">
                                                <div class="flex items-start justify-between gap-4">
                                                    <div>
                                                        <p class="text-[0.68rem] font-semibold uppercase tracking-[0.3em] {{ $summaryCard['accent'] }}">{{ $summaryCard['badge'] }}</p>
                                                        <p class="mt-3 text-sm font-medium {{ $summaryCard['accent'] }}">{{ $summaryCard['label'] }}</p>
                                                    </div>
                                                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-white/10">
                                                        @if($summaryCard['label'] === 'Visitors')
                                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                                <path d="M17 21V19C17 17.3431 15.6569 16 14 16H8C6.34315 16 5 17.3431 5 19V21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                                <circle cx="11" cy="8" r="4" stroke="currentColor" stroke-width="1.8"/>
                                                            </svg>
                                                        @elseif($summaryCard['label'] === 'Users')
                                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                                <path d="M16 21V19C16 17.3431 14.6569 16 13 16H7C5.34315 16 4 17.3431 4 19V21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                                <circle cx="10" cy="8" r="3.5" stroke="currentColor" stroke-width="1.8"/>
                                                                <path d="M20 21V19.5C20 18.1193 18.8807 17 17.5 17H17" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                            </svg>
                                                        @elseif($summaryCard['label'] === 'Booking')
                                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                                <path d="M7 3V6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                                <path d="M17 3V6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                                <rect x="4" y="5" width="16" height="15" rx="2.5" stroke="currentColor" stroke-width="1.8"/>
                                                                <path d="M4 10H20" stroke="currentColor" stroke-width="1.8"/>
                                                            </svg>
                                                        @else
                                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                                <path d="M6 15L10 11L13 14L19 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                                <path d="M16 8H19V11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                            </svg>
                                                        @endif
                                                    </span>
                                                </div>

                                                <p class="mt-8 text-5xl font-semibold tracking-tight">{{ $summaryCard['value'] }}</p>
                                                <p class="mt-4 text-sm leading-7 {{ $summaryCard['accent'] }}">{{ $summaryCard['note'] }}</p>
                                                <div class="mt-6 rounded-[1.35rem] border border-white/10 bg-white/10 px-4 py-3 text-xs uppercase tracking-[0.22em] {{ $summaryCard['accent'] }}">
                                                    {{ $summaryCard['subnote'] }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="grid gap-6 lg:grid-cols-[minmax(0,1.62fr)_minmax(360px,0.96fr)] 2xl:grid-cols-[minmax(0,1.62fr)_minmax(420px,0.9fr)]">
                                    <div class="glass-panel p-6">
                                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Daily Booking Trend</h3>
                                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                    Monthly recap report for bookings and paid income across the selected range.
                                                </p>
                                            </div>
                                            <div class="rounded-2xl bg-slate-100 px-4 py-3 text-sm text-slate-700 dark:bg-gray-900 dark:text-gray-200">
                                                Peak day:
                                                <span class="font-semibold">
                                                    {{ data_get($analyticsPeakBookingDay, 'date') ? $analyticsPeakBookingDay['date']->format('M d, Y') : 'N/A' }}
                                                </span>
                                                | {{ number_format((int) data_get($analyticsPeakBookingDay, 'booking_count', 0)) }} bookings
                                            </div>
                                        </div>

                                        @if($hasAnalyticsBookings)
                                            <div class="mt-6 overflow-x-auto">
                                                <div class="min-w-max rounded-3xl border border-sky-100 bg-white/95 p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900/30" style="min-width: {{ $chartWidth }}px;">
                                                    <div class="flex items-center gap-5 px-3 pb-4 text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">
                                                        <span class="inline-flex items-center gap-2">
                                                            <span class="h-2.5 w-2.5 rounded-full bg-blue-500"></span>
                                                            Bookings
                                                        </span>
                                                        <span class="inline-flex items-center gap-2">
                                                            <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                                                            Paid income
                                                        </span>
                                                    </div>

                                                    <svg viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}" class="h-[220px] w-full sm:h-[250px] lg:h-[280px]">
                                                        @foreach($chartGridLines as $gridLine)
                                                            @php
                                                                $y = $chartBottom - (($gridLine / 100) * $chartPlotHeight);
                                                            @endphp
                                                            <line x1="24" y1="{{ $y }}" x2="{{ $chartWidth - 24 }}" y2="{{ $y }}" stroke="#e2e8f0" stroke-width="1" />
                                                        @endforeach

                                                        <polyline
                                                            fill="none"
                                                            stroke="#10b981"
                                                            stroke-width="4"
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            points="{{ $incomePolylinePoints }}"
                                                        />

                                                        <polyline
                                                            fill="none"
                                                            stroke="#2563eb"
                                                            stroke-width="4"
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            points="{{ $bookingPolylinePoints }}"
                                                        />

                                                        @foreach($analyticsSeries->values() as $index => $day)
                                                            @php
                                                                $x = $chartPointCount > 1
                                                                    ? 32 + (($chartWidth - 64) / ($chartPointCount - 1)) * $index
                                                                    : $chartWidth / 2;
                                                                $bookingY = $chartBottom - (($day['booking_count'] / $maxDailyBookings) * $chartPlotHeight);
                                                                $incomeY = $chartBottom - (($day['paid_income'] / $maxPaidIncome) * $chartPlotHeight);
                                                            @endphp
                                                            <circle cx="{{ $x }}" cy="{{ $bookingY }}" r="4.5" fill="#2563eb" />
                                                            <circle cx="{{ $x }}" cy="{{ $incomeY }}" r="4.5" fill="#10b981" />
                                                        @endforeach
                                                    </svg>

                                                    <div class="mt-4 grid grid-cols-2 gap-3 md:grid-cols-4">
                                                        @foreach($analyticsSeries as $day)
                                                            <div class="rounded-2xl bg-slate-50 px-3 py-3 text-center dark:bg-gray-950/40">
                                                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $day['date']->format('D') }}</p>
                                                                <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">{{ $day['date']->format('M d') }}</p>
                                                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $day['booking_count'] }} bookings</p>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="mt-6 rounded-3xl border border-dashed border-gray-300 bg-gray-50 px-6 py-10 text-center text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-900/30 dark:text-gray-400">
                                                No bookings found in this range yet. Try another date range to generate the graph.
                                            </div>
                                        @endif

                                        <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                                            <div class="rounded-3xl border border-sky-100 bg-sky-50/75 px-5 py-4 dark:border-gray-700 dark:bg-gray-900/40">
                                                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Total revenue</p>
                                                <p class="mt-3 text-2xl font-semibold text-slate-950 dark:text-white">PHP {{ number_format($stats['range_paid_income'], 2) }}</p>
                                                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Collected within the active reporting range.</p>
                                            </div>
                                            <div class="rounded-3xl border border-emerald-100 bg-emerald-50/80 px-5 py-4 dark:border-gray-700 dark:bg-gray-900/40">
                                                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Member mix</p>
                                                <p class="mt-3 text-2xl font-semibold text-slate-950 dark:text-white">{{ $memberBookingPercentage }}%</p>
                                                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $analyticsSummary['member_bookings'] }} member bookings in this dashboard range.</p>
                                            </div>
                                            <div class="rounded-3xl border border-amber-100 bg-amber-50/80 px-5 py-4 dark:border-gray-700 dark:bg-gray-900/40">
                                                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Walk-in mix</p>
                                                <p class="mt-3 text-2xl font-semibold text-slate-950 dark:text-white">{{ $walkInPercentage }}%</p>
                                                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $analyticsSummary['walk_in_bookings'] }} walk-in bookings inside the same period.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-6">
                                        <div class="glass-panel p-6">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Court Share Donut Chart</h3>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                Large donut view of which courts are absorbing the booking demand inside this dashboard range.
                                            </p>

                                            <div class="mt-6 grid gap-6 xl:grid-cols-[250px_minmax(0,1fr)] xl:items-center">
                                                <div class="flex items-center justify-center">
                                                    <div class="relative h-64 w-64">
                                                        <svg viewBox="0 0 220 220" class="h-full w-full -rotate-90 drop-shadow-[0_12px_30px_rgba(37,99,235,0.14)]">
                                                            <circle cx="110" cy="110" r="{{ $donutRadius }}" fill="none" stroke="#e2e8f0" stroke-width="26" />
                                                            @foreach($donutSegments as $segment)
                                                                <circle
                                                                    cx="110"
                                                                    cy="110"
                                                                    r="{{ $donutRadius }}"
                                                                    fill="none"
                                                                    stroke="{{ $segment['color'] }}"
                                                                    stroke-width="26"
                                                                    stroke-linecap="round"
                                                                    stroke-dasharray="{{ number_format($segment['length'], 2, '.', '') }} {{ number_format($donutCircumference, 2, '.', '') }}"
                                                                    stroke-dashoffset="-{{ number_format($segment['offset'], 2, '.', '') }}"
                                                                />
                                                            @endforeach
                                                        </svg>

                                                        <div class="absolute inset-0 flex items-center justify-center">
                                                            <div class="text-center">
                                                                <p class="text-[0.68rem] font-semibold uppercase tracking-[0.28em] text-slate-400">Top Court</p>
                                                                <p class="mt-2 text-lg font-semibold text-slate-950 dark:text-white">{{ data_get($topCourtShare, 'label', 'No data yet') }}</p>
                                                                <p class="mt-2 text-3xl font-semibold text-slate-950 dark:text-white">{{ data_get($topCourtShare, 'percentage', 0) }}%</p>
                                                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ number_format($stats['range_bookings']) }} bookings total</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="space-y-3">
                                                    @forelse($donutSegments as $segment)
                                                        <div class="rounded-2xl border border-sky-100 bg-sky-50/75 px-4 py-4 dark:border-gray-700 dark:bg-gray-900/40">
                                                            <div class="flex items-center justify-between gap-3">
                                                                <div class="flex items-center gap-3">
                                                                    <span class="h-3 w-3 rounded-full" style="background-color: {{ $segment['color'] }}"></span>
                                                                    <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $segment['label'] }}</p>
                                                                </div>
                                                                <span class="text-sm font-semibold text-slate-600 dark:text-slate-300">{{ $segment['percentage'] }}%</span>
                                                            </div>
                                                            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">{{ $segment['booking_count'] }} bookings in range</p>
                                                        </div>
                                                    @empty
                                                        <div class="rounded-3xl border border-dashed border-sky-200 px-4 py-8 text-center text-sm text-slate-500 dark:border-gray-700 dark:text-slate-400">
                                                            No court-share data yet for this range.
                                                        </div>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>

                                        <div class="glass-panel p-6">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Demand Snapshot</h3>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                Goal-completion style view of booking mix and demand progress for the current range.
                                            </p>

                                            <div class="mt-5 space-y-4">
                                                @foreach($dashboardProgressRows as $progressRow)
                                                    <div>
                                                        <div class="flex items-center justify-between gap-3">
                                                            <p class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $progressRow['label'] }}</p>
                                                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $progressRow['value'] }} / {{ $progressRow['total'] }}</span>
                                                        </div>
                                                        <div class="mt-2 h-3 rounded-full bg-gray-100 dark:bg-gray-800">
                                                            <div class="h-3 rounded-full {{ $progressRow['bar'] }}" style="width: {{ min(100, max(0, $progressRow['width'])) }}%;"></div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <div class="mt-6 space-y-3">
                                                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-gray-400">Top Courts</p>
                                                @forelse($analyticsTopCourts as $court)
                                                    @php
                                                        $courtWidth = max(14, (int) round(($court['booking_count'] / $topCourtMaxBookings) * 100));
                                                    @endphp
                                                    <div class="rounded-2xl border border-sky-100 bg-slate-50 px-4 py-4 dark:border-gray-700 dark:bg-gray-900/40">
                                                        <div class="flex items-center justify-between gap-3">
                                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $court['label'] }}</p>
                                                            <span class="text-xs font-medium uppercase tracking-[0.2em] text-gray-400">{{ $court['booking_count'] }} bookings</span>
                                                        </div>
                                                        <div class="mt-3 h-2 rounded-full bg-gray-200 dark:bg-gray-800">
                                                            <div class="h-2 rounded-full bg-indigo-500" style="width: {{ $courtWidth }}%;"></div>
                                                        </div>
                                                        <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                                                            Paid income: PHP {{ number_format($court['paid_income'], 2) }}
                                                        </p>
                                                    </div>
                                                @empty
                                                    <div class="rounded-2xl border border-dashed border-sky-200 px-4 py-5 text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                                        No court demand data yet for this range.
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>

                                        <div class="glass-panel p-6">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Dashboard Signals</h3>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                Fast reads for the team before jumping into bookings, monitor, or rates.
                                            </p>

                                            <div class="mt-5 space-y-3">
                                                @foreach($dashboardSignalCards as $signalCard)
                                                    <div class="{{ $signalCard['panel'] }} rounded-3xl px-5 py-4 shadow-sm">
                                                        <p class="text-xs font-semibold uppercase tracking-[0.24em] {{ $signalCard['accent'] }}">{{ $signalCard['label'] }}</p>
                                                        <p class="mt-3 text-lg font-semibold">{{ $signalCard['value'] }}</p>
                                                        <p class="mt-2 text-sm {{ $signalCard['accent'] }}">{{ $signalCard['note'] }}</p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid gap-6 lg:grid-cols-[minmax(0,1.18fr)_minmax(0,0.82fr)]">
                                    <div class="glass-panel p-6">
                                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Recent Booking Names</h3>
                                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                    Quick customer view for {{ \Illuminate\Support\Carbon::parse($selectedDate)->format('M d, Y') }} so the front desk can spot today's bookings fast.
                                                </p>
                                            </div>
                                            <div class="rounded-xl bg-slate-100 px-4 py-3 text-sm text-slate-700 dark:bg-gray-900 dark:text-gray-200">
                                                {{ $recentDashboardBookings->count() }} customer highlight{{ $recentDashboardBookings->count() === 1 ? '' : 's' }}
                                            </div>
                                        </div>

                                        <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-2 2xl:grid-cols-4">
                                            @forelse($recentDashboardBookings as $bookingHighlight)
                                                <div class="rounded-3xl border border-sky-100 bg-white/95 px-4 py-4 shadow-sm dark:border-gray-700 dark:bg-gray-900/40">
                                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-900 text-sm font-semibold text-white">
                                                        {{ \Illuminate\Support\Str::of($bookingHighlight['name'])->explode(' ')->map(fn ($part) => \Illuminate\Support\Str::substr($part, 0, 1))->take(2)->implode('') }}
                                                    </div>
                                                    <p class="mt-4 text-sm font-semibold text-slate-950 dark:text-white">{{ $bookingHighlight['name'] }}</p>
                                                    <p class="mt-1 text-xs uppercase tracking-[0.2em] text-slate-400">{{ $bookingHighlight['date']->format('M d, Y') }}</p>
                                                    <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">
                                                        {{ $bookingHighlight['schedule'] }}<br>
                                                        {{ $bookingHighlight['court'] }}
                                                        <br>
                                                        {{ $bookingHighlight['payment_status'] }} | {{ $bookingHighlight['receipt_no'] }}
                                                    </p>
                                                </div>
                                            @empty
                                                <div class="sm:col-span-2 lg:col-span-2 2xl:col-span-4 rounded-3xl border border-dashed border-sky-200 px-4 py-8 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                                    No customer names are available in this dashboard range yet.
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>

                                    <div class="glass-panel p-6">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Revenue Pulse</h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            Highest paid-income days within the selected date range.
                                        </p>

                                        <div class="mt-5 overflow-hidden rounded-3xl border border-sky-100 dark:border-gray-700">
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                    <thead class="bg-sky-50/85 dark:bg-gray-900/60">
                                                        <tr>
                                                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Date</th>
                                                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Revenue</th>
                                                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Bookings</th>
                                                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Mix</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                                                        @forelse($topIncomeDays as $day)
                                                            <tr>
                                                                <td class="px-4 py-4 text-sm font-medium text-slate-900 dark:text-white">{{ $day['date']->format('M d, Y') }}</td>
                                                                <td class="px-4 py-4 text-sm text-emerald-600 dark:text-emerald-300">PHP {{ number_format($day['paid_income'], 2) }}</td>
                                                                <td class="px-4 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $day['booking_count'] }}</td>
                                                                <td class="px-4 py-4 text-sm text-slate-500 dark:text-slate-400">
                                                                    {{ $day['member_booking_count'] }} member | {{ $day['walk_in_count'] }} walk-in
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-500 dark:text-slate-400">
                                                                    No paid income data yet for this range.
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
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
                                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Schedule</th>
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
                                                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $reservation->timeRangeLabel() }}<br><span class="text-xs text-gray-500 dark:text-gray-400">{{ $reservation->durationLabel() }}</span></td>
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
                                                <label for="walkin_time_slot" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Start time</label>
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
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Rates & Rentals</h3>
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
                                @php
                                    $showPublicCustomerNamesChecked = old('show_public_customer_names') === null
                                        ? $showPublicCustomerNames
                                        : old('show_public_customer_names') === '1';
                                @endphp

                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Public Reservation List</h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            Choose if customer names only will appear in the public <span class="font-medium">Reservations for</span> list. Contact numbers and emails stay visible to admin only.
                                        </p>
                                    </div>
                                    <div class="rounded-xl bg-sky-50 px-4 py-3 text-sm text-sky-700 dark:bg-sky-950/40 dark:text-sky-300">
                                        {{ $showPublicCustomerNames ? 'Customer names only are visible publicly' : 'Customer names are hidden publicly' }}
                                    </div>
                                </div>

                                @if(! $publicCustomerNamesSettingReady)
                                    <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:border-amber-800 dark:bg-amber-950/30 dark:text-amber-200">
                                        Public reservation visibility is not ready yet. Run <code>php artisan migrate</code> first before saving changes.
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('admin.public-reservations.visibility.update') }}" class="mt-6 space-y-4">
                                    @csrf
                                    <input type="hidden" name="show_public_customer_names" value="0">

                                    <label class="flex cursor-pointer items-start gap-4 rounded-2xl border border-gray-200 bg-gray-50 px-4 py-4 dark:border-gray-700 dark:bg-gray-900/40">
                                        <input
                                            type="checkbox"
                                            name="show_public_customer_names"
                                            value="1"
                                            class="mt-1 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                            @checked($showPublicCustomerNamesChecked)
                                        >
                                        <span class="space-y-1">
                                            <span class="block text-sm font-medium text-gray-900 dark:text-gray-100">Show customer names only on the public reservation list</span>
                                            <span class="block text-sm text-gray-500 dark:text-gray-400">
                                                When this is off, visitors and other players only see <span class="font-medium">Reserved slot</span>. When this is on, name only is shown publicly. Admin still keeps the full contact details inside the dashboard.
                                            </span>
                                        </span>
                                    </label>

                                    <x-primary-button>Save Visibility</x-primary-button>
                                </form>
                            </div>

                            <div class="glass-panel p-6">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Court Setup</h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            Admin can change pila ka courts ang available plus set a custom name and two editable time-based rates for every court.
                                        </p>
                                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                            Court booking rate is managed per court and time window below.
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
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Schedule</th>
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
                                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $reservation->timeRangeLabel() }}<br><span class="text-xs text-gray-500 dark:text-gray-400">{{ $reservation->durationLabel() }}</span></td>
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
