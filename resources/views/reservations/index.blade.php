<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pickle BALLan ni Juan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            color-scheme: light;
            --bg: #f4f7fb;
            --surface: #ffffff;
            --surface-alt: #eef5ff;
            --text: #0f172a;
            --muted: #64748b;
            --line: #dbe4f0;
            --primary: #0f172a;
            --primary-soft: #dbeafe;
            --success-bg: #dcfce7;
            --success-text: #166534;
            --danger-bg: #fee2e2;
            --danger-text: #991b1b;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Segoe UI", Arial, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(59, 130, 246, 0.12), transparent 24%),
                radial-gradient(circle at bottom right, rgba(16, 185, 129, 0.12), transparent 26%),
                var(--bg);
            color: var(--text);
            padding: clamp(16px, 2vw, 28px);
        }

        .container {
            width: min(100%, 1820px);
            max-width: none;
            margin: 0 auto;
        }

        .card {
            background: var(--surface);
            border: 1px solid rgba(15, 23, 42, 0.06);
            border-radius: 22px;
            padding: 22px;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
            margin-bottom: 24px;
        }

        .hero {
            display: grid;
            grid-template-columns: minmax(0, 1.15fr) minmax(250px, 0.78fr) minmax(250px, 0.78fr);
            gap: 20px;
            align-items: center;
        }

        .brand-lockup {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            min-width: 0;
        }

        .brand-copy {
            min-width: 0;
        }

        .brand-title {
            margin: 0;
            font-size: clamp(2rem, 3vw, 2.6rem);
            line-height: 1.02;
            letter-spacing: -0.04em;
        }

        .brand-copy .muted {
            max-width: 460px;
        }

        .brand-mark {
            width: 92px;
            height: 92px;
            flex-shrink: 0;
        }

        .hero-spotlight {
            padding: 20px 18px;
            border: 1px solid var(--line);
            border-radius: 20px;
            background: linear-gradient(180deg, #f8fbff 0%, #eef5ff 100%);
            text-align: center;
        }

        .hero-kicker {
            margin: 0;
            color: #2563eb;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }

        .hero-spotlight-title {
            margin: 10px 0 0;
            font-size: 1.35rem;
            line-height: 1.2;
        }

        .hero-book-wrap {
            width: 100%;
        }

        .hero-book-button {
            min-width: 190px;
            margin: 14px 0 0;
        }

        .hero-cta-note {
            margin: 12px auto 0;
            max-width: 300px;
            line-height: 1.65;
        }

        .hero-book-panel {
            margin-top: 14px;
            padding: 16px;
            border: 1px solid var(--line);
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.94);
            box-shadow: 0 16px 34px rgba(15, 23, 42, 0.08);
        }

        .hero-book-title {
            margin: 0 0 8px;
            font-size: 1rem;
        }

        .hero-book-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin-top: 14px;
        }

        .hero-book-actions .button,
        .hero-book-actions .button-secondary {
            margin: 0;
            min-width: 140px;
        }

        .hero-side {
            display: flex;
            flex-direction: column;
            gap: 12px;
            align-items: stretch;
            min-width: 0;
        }

        .hero-utility-row {
            display: flex;
            gap: 12px;
            align-items: center;
            justify-content: flex-end;
        }

        .hero-menu {
            position: relative;
            display: block;
            margin: 0;
        }

        .hero-menu[open] .hero-menu-summary {
            border-color: #bfdbfe;
            box-shadow: 0 12px 30px rgba(59, 130, 246, 0.16);
        }

        .hero-menu-summary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 52px;
            height: 52px;
            border: 1px solid var(--line);
            border-radius: 18px;
            background: #ffffff;
            cursor: pointer;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
            list-style: none;
        }

        .hero-menu-summary::-webkit-details-marker {
            display: none;
        }

        .hero-menu-avatar {
            width: 22px;
            height: 22px;
            color: var(--text);
        }

        .hero-menu-panel {
            position: absolute;
            right: 0;
            top: calc(100% + 10px);
            z-index: 40;
            width: min(240px, calc(100vw - 48px));
            padding: 10px;
            border: 1px solid var(--line);
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.14);
            backdrop-filter: blur(14px);
        }

        .hero-menu-link {
            display: flex;
            align-items: center;
            min-height: 46px;
            padding: 0 14px;
            border-radius: 14px;
            color: var(--text);
            text-decoration: none;
            font-weight: 600;
        }

        .hero-menu-link:hover {
            background: var(--surface-alt);
        }

        .hero-date-filter {
            width: 100%;
            min-width: 0;
            max-width: none;
            padding: 16px;
            border: 1px solid var(--line);
            border-radius: 18px;
            background: linear-gradient(180deg, #f8fbff 0%, #eef5ff 100%);
        }

        .hero-date-filter label {
            display: block;
            margin-bottom: 6px;
        }

        .hero-date-filter .input {
            margin: 0;
        }

        .grid {
            display: grid;
            grid-template-columns: minmax(0, 1.14fr) minmax(360px, 0.86fr);
            gap: 24px;
            align-items: start;
        }

        .grid > div {
            min-width: 0;
        }

        .input, .select, .button, .button-secondary {
            width: 100%;
            padding: 12px 14px;
            border-radius: 14px;
            border: 1px solid var(--line);
            margin-top: 6px;
            margin-bottom: 14px;
            font: inherit;
        }

        .input, .select {
            background: #fff;
        }

        .button, .button-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: auto;
            min-width: 150px;
            text-decoration: none;
            cursor: pointer;
            transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
        }

        .button:hover, .button-secondary:hover {
            transform: translateY(-1px);
        }

        .button {
            border: none;
            background: var(--primary);
            color: #fff;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.14);
        }

        .button-secondary {
            background: var(--surface-alt);
            color: var(--text);
        }

        .button-facebook {
            background: #1877f2;
            color: #fff;
            border: none;
            box-shadow: 0 10px 24px rgba(24, 119, 242, 0.22);
        }

        .hero-facebook-button {
            margin: 0;
        }

        .slot, .booking-item {
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 16px;
            margin-bottom: 12px;
            background: #fff;
        }

        .court-panel {
            border: 1px solid var(--line);
            border-radius: 18px;
            margin-bottom: 12px;
            overflow: hidden;
            background: #fff;
        }

        .court-panel summary {
            list-style: none;
            cursor: pointer;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .court-panel summary::-webkit-details-marker {
            display: none;
        }

        .court-panel[open] summary {
            background: #f8fbff;
            border-bottom: 1px solid var(--line);
        }

        .court-summary {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .court-summary-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: flex-end;
        }

        .court-arrow {
            font-size: 14px;
            color: var(--muted);
            transition: transform .2s ease;
        }

        .court-panel[open] .court-arrow {
            transform: rotate(180deg);
        }

        .slots-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(145px, 1fr));
            gap: 10px;
            padding: 18px;
        }

        .time-chip {
            border-radius: 14px;
            padding: 12px;
            border: 1px solid var(--line);
            display: grid;
            gap: 4px;
        }

        .time-chip.available {
            background: #f0fdf4;
            border-color: #bbf7d0;
        }

        .time-chip.booked {
            background: #fff7ed;
            border-color: #fed7aa;
        }

        .time-chip strong {
            font-size: 15px;
        }

        .booking-item {
            display: grid;
            gap: 6px;
        }

        .muted {
            color: var(--muted);
            font-size: 14px;
        }

        .success {
            background: var(--success-bg);
            color: var(--success-text);
            padding: 14px 16px;
            border-radius: 16px;
            margin-bottom: 16px;
        }

        .error {
            background: var(--danger-bg);
            color: var(--danger-text);
            padding: 14px 16px;
            border-radius: 16px;
            margin-bottom: 16px;
        }

        .badge-ok, .badge-full, .badge-mine {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
        }

        .badge-ok {
            background: var(--success-bg);
            color: var(--success-text);
        }

        .badge-full {
            background: var(--danger-bg);
            color: var(--danger-text);
        }

        .badge-mine {
            background: var(--primary-soft);
            color: #1d4ed8;
        }

        .panel-title {
            margin: 0 0 8px;
            font-size: 1.4rem;
        }

        .account-box {
            border: 1px solid var(--line);
            background: var(--surface-alt);
            border-radius: 18px;
            padding: 16px;
            margin-bottom: 16px;
        }

        .booking-card {
            scroll-margin-top: 28px;
        }

        .notice-box {
            border: 1px solid #fed7aa;
            background: #fff7ed;
            border-radius: 18px;
            padding: 16px;
            margin-bottom: 16px;
            color: #9a3412;
        }

        .date-filter {
            min-width: 220px;
        }

        ul.error-list {
            margin: 0;
            padding-left: 18px;
        }

        @media (max-width: 980px) {
            body {
                padding: 16px;
            }

            .hero {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .grid {
                grid-template-columns: 1fr;
            }

            .brand-lockup {
                align-items: flex-start;
            }

            .brand-mark {
                width: 72px;
                height: 72px;
            }

            .hero-side {
                width: 100%;
                align-items: stretch;
            }

            .hero-utility-row {
                justify-content: flex-end;
            }

            .hero-facebook-button {
                display: none;
            }

            .hero-book-actions {
                flex-direction: column;
            }

            .hero-book-actions .button,
            .hero-book-actions .button-secondary {
                width: 100%;
            }

            .date-filter {
                width: 100%;
                min-width: 0;
            }
        }
    </style>
</head>
@php
    $reservationManager = app(\App\Support\ReservationManager::class);
    $courtLookup = collect($courts)->keyBy('number');
    $resolveCourtLabel = function (int $courtNumber, ?string $courtName = null) use ($courtLookup) {
        $resolvedName = trim((string) ($courtName ?: data_get($courtLookup->get($courtNumber), 'name')));
        $defaultName = "Court {$courtNumber}";

        if ($resolvedName === '' || $resolvedName === $defaultName) {
            return $defaultName;
        }

        return "{$resolvedName} ({$defaultName})";
    };
    $selectedCourtNumber = (int) old('court_number', $courts[0]['number'] ?? 0);
    $selectedCourtLabel = data_get($courtLookup->get($selectedCourtNumber), 'label', 'Choose a court first');
    $selectedTimeSlot = (string) old('time_slot', '');
    $selectedDurationHours = max(1, (int) old('duration_hours', 1));
    $selectedDurationOptions = array_map('intval', data_get($durationOptionsBySlot ?? [], $selectedTimeSlot, [1]));
    if (! in_array($selectedDurationHours, $selectedDurationOptions, true)) {
        $selectedDurationHours = (int) ($selectedDurationOptions[0] ?? 1);
    }
    $selectedCourtDayRate = (int) data_get($courtLookup->get($selectedCourtNumber), 'day_rate', 0);
    $selectedCourtAvailability = collect($courtAvailability)->firstWhere('court_number', $selectedCourtNumber);
    $selectedCourtSlots = collect(data_get($selectedCourtAvailability, 'slots', []))->keyBy('slot');
    $selectedDurationSlots = $selectedTimeSlot !== ''
        ? $reservationManager->timeSlotsForDuration($selectedTimeSlot, $selectedDurationHours)
        : [];
    $selectedOldPaddleRentQuantity = max(0, (int) old('paddle_rent_quantity', 0));
    $selectedNewPaddleRentQuantity = max(0, (int) old('new_paddle_rent_quantity', 0));
    $selectedBallQuantity = max(0, (int) old('ball_quantity', 0));
    $selectedPaymentMethod = (string) old('payment_method', ($payMongoCheckoutReady ?? false) ? 'paymongo' : 'gcash');
    if (! ($payMongoCheckoutReady ?? false) && $selectedPaymentMethod === 'paymongo') {
        $selectedPaymentMethod = 'gcash';
    }
    $hasSelectedSchedule = $selectedDurationSlots !== [];
    $selectedScheduleLabel = $hasSelectedSchedule
        ? $reservationManager->formatReservationTimeRange($selectedTimeSlot, $selectedDurationHours) . ' (' . $reservationManager->formatDurationLabel($selectedDurationHours) . ')'
        : 'Choose time slot first';
    $selectedCourtRate = $hasSelectedSchedule
        ? collect($selectedDurationSlots)->sum(fn (string $slot) => (int) data_get($selectedCourtSlots->get($slot), 'rate', $selectedCourtDayRate))
        : 0;
    $selectedRentalTotal = ($selectedOldPaddleRentQuantity * $oldPaddleRentRate) + ($selectedNewPaddleRentQuantity * $newPaddleRentRate) + ($selectedBallQuantity * $ballRate);
    $selectedBookingTotal = $hasSelectedSchedule
        ? $selectedCourtRate + $selectedRentalTotal
        : 0;
    $heroDashboardUrl = auth()->check()
        ? (auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard'))
        : null;
    $heroDashboardLabel = auth()->check()
        ? (auth()->user()->isAdmin() ? 'Open Admin Dashboard' : 'Open My Dashboard')
        : null;
    $bookingAuthRedirect = 'booking';
    $heroLoginUrl = route('login', ['redirect_to' => $bookingAuthRedirect]);
    $heroRegisterUrl = route('register', ['redirect_to' => $bookingAuthRedirect]);
    $heroBookNowUrl = auth()->check()
        ? (auth()->user()->isAdmin()
            ? route('admin.dashboard', ['panel' => 'booking'])
            : '#booking-section')
        : null;
    $heroBookNowNote = ! auth()->check()
        ? 'Sign in or create an account first, then continue to the booking form.'
        : (auth()->user()->isAdmin()
            ? 'Open the admin booking panel for walk-in and assisted reservations.'
            : 'Jump straight to the booking section below and reserve your court.');
    $selectedDateHeadline = \Illuminate\Support\Carbon::parse($selectedDate)->format('F d, Y');
    $totalCourtSlots = collect($courtAvailability)->sum(fn (array $court) => count($court['slots']));
    $totalAvailableSlots = collect($courtAvailability)->sum(fn (array $court) => (int) $court['available_slots']);
    $totalBookedSlots = collect($courtAvailability)->sum(fn (array $court) => (int) $court['booked_slots']);
    $publicReservationsCount = $bookings->count();
    $pageSupportNote = auth()->check() && auth()->user()->isAdmin()
        ? 'Admin view shows the live public schedule and member reservations for the selected day.'
        : 'Browse live availability, compare rates, and lock in consecutive hours from one screen.';
@endphp
<body>
    <div class="public-topbar">
        <div class="public-topbar-inner">
            <a href="{{ route('reservations.index') }}" class="public-topbar-brand" aria-label="Open public booking dashboard">
                <span class="public-topbar-brand-mark">PB</span>
                <span class="public-topbar-brand-copy">
                    <strong>Pickle BALLan ni Juan</strong>
                    <span>Public Booking Dashboard</span>
                </span>
            </a>

            <div class="public-topbar-actions">
                <a
                    href="{{ config('services.facebook.page_url') }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="public-topbar-link"
                >
                    Facebook
                </a>

                @auth
                    @if(! auth()->user()->isAdmin())
                        <a href="#booking-section" class="button public-topbar-button">Book Now</a>
                    @endif
                    <a href="{{ $heroDashboardUrl }}" class="button-secondary public-topbar-button">{{ $heroDashboardLabel }}</a>
                @else
                    <a href="{{ $heroLoginUrl }}" class="button public-topbar-button">Book Now</a>
                    <a href="{{ $heroLoginUrl }}" class="button-secondary public-topbar-button">Login</a>
                    <a href="{{ $heroRegisterUrl }}" class="button-secondary public-topbar-button">Sign Up</a>
                @endauth
            </div>
        </div>
    </div>

    <style>
        .agreement-overlay {
            position: fixed;
            inset: 0;
            z-index: 2000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background:
                linear-gradient(180deg, rgba(15, 23, 42, 0.84), rgba(15, 23, 42, 0.9)),
                radial-gradient(circle at top, rgba(59, 130, 246, 0.18), transparent 42%);
            backdrop-filter: blur(12px);
        }

        .agreement-overlay[hidden] {
            display: none;
        }

        .agreement-card {
            width: min(760px, 100%);
            max-height: min(88vh, 920px);
            overflow: auto;
            padding: 28px;
            border: 1px solid rgba(191, 219, 254, 0.42);
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 30px 80px rgba(15, 23, 42, 0.32);
        }

        .agreement-eyebrow {
            margin: 0 0 10px;
            color: #2563eb;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.22em;
            text-transform: uppercase;
        }

        .agreement-title {
            margin: 0;
            color: #0f172a;
            font-size: clamp(1.8rem, 3vw, 2.6rem);
            line-height: 1.05;
        }

        .agreement-lead {
            margin: 14px 0 0;
            color: #475569;
            font-size: 0.98rem;
            line-height: 1.7;
        }

        .agreement-grid {
            display: grid;
            gap: 14px;
            margin-top: 22px;
        }

        .agreement-panel {
            padding: 16px 18px;
            border: 1px solid #dbeafe;
            border-radius: 18px;
            background: linear-gradient(180deg, #f8fbff 0%, #eef6ff 100%);
        }

        .agreement-panel strong {
            display: block;
            margin-bottom: 6px;
            color: #0f172a;
            font-size: 0.98rem;
        }

        .agreement-panel p {
            margin: 0;
            color: #475569;
            font-size: 0.93rem;
            line-height: 1.65;
        }

        .agreement-warning {
            margin-top: 18px;
            padding: 16px 18px;
            border: 1px solid #fed7aa;
            border-radius: 18px;
            background: #fff7ed;
            color: #9a3412;
        }

        .agreement-warning strong {
            display: block;
            margin-bottom: 6px;
            font-size: 0.96rem;
        }

        .agreement-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            justify-content: space-between;
            margin-top: 22px;
        }

        .agreement-check {
            display: inline-flex;
            gap: 12px;
            align-items: flex-start;
            color: #334155;
            font-size: 0.92rem;
            line-height: 1.55;
        }

        .agreement-check input {
            margin-top: 4px;
        }

        .agreement-button {
            min-width: 220px;
        }

        .agreement-button[disabled] {
            cursor: not-allowed;
            opacity: 0.55;
        }

        @media (max-width: 640px) {
            .agreement-card {
                padding: 22px 18px;
                border-radius: 22px;
            }

            .agreement-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .agreement-button {
                width: 100%;
            }
        }

        @media (min-width: 1400px) {
            .hero {
                grid-template-columns: minmax(0, 1.38fr) minmax(320px, 0.8fr) minmax(280px, 0.72fr);
            }

            .grid {
                grid-template-columns: minmax(0, 1.18fr) minmax(420px, 0.82fr);
                gap: 28px;
            }

            .card {
                padding: 24px;
            }
        }
    </style>

    <style>
        :root {
            --bg: #eef4fb;
            --surface: rgba(255, 255, 255, 0.95);
            --surface-alt: #edf5ff;
            --surface-strong: #0f172a;
            --line: rgba(148, 163, 184, 0.22);
            --line-strong: rgba(37, 99, 235, 0.18);
            --primary: #0f172a;
            --primary-soft: #dbeafe;
            --accent: #2563eb;
            --accent-soft: rgba(37, 99, 235, 0.1);
            --muted: #516076;
        }

        body {
            font-family: "Manrope", "Segoe UI", Arial, sans-serif;
            line-height: 1.6;
            background:
                radial-gradient(circle at 8% 10%, rgba(37, 99, 235, 0.18), transparent 20%),
                radial-gradient(circle at 92% 12%, rgba(16, 185, 129, 0.14), transparent 18%),
                radial-gradient(circle at 50% 100%, rgba(14, 165, 233, 0.12), transparent 24%),
                linear-gradient(180deg, #f8fbff 0%, #eef4fb 42%, #f7fafc 100%);
            padding: clamp(12px, 1.4vw, 24px);
        }

        .page-shell {
            display: grid;
            gap: 24px;
            width: min(100%, 1920px);
        }

        .public-topbar {
            position: relative;
            z-index: 50;
            width: min(100%, 1920px);
            margin: 0 auto 18px;
        }

        .public-topbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            min-height: 72px;
            padding: 12px 14px;
            border: 1px solid rgba(191, 219, 254, 0.5);
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 18px 42px rgba(15, 23, 42, 0.08);
            backdrop-filter: blur(16px);
        }

        .public-topbar-brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
            color: #0f172a;
            text-decoration: none;
        }

        .public-topbar-brand-mark {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            flex: 0 0 auto;
            border-radius: 16px;
            background: linear-gradient(135deg, #0f172a 0%, #2563eb 100%);
            color: #ffffff;
            font-weight: 800;
            letter-spacing: 0;
        }

        .public-topbar-brand-copy {
            display: grid;
            gap: 1px;
            min-width: 0;
        }

        .public-topbar-brand-copy strong,
        .public-topbar-brand-copy span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .public-topbar-brand-copy strong {
            font-family: "Space Grotesk", "Segoe UI", Arial, sans-serif;
            font-size: 1rem;
            line-height: 1.15;
        }

        .public-topbar-brand-copy span {
            color: #516076;
            font-size: 0.78rem;
            font-weight: 700;
        }

        .public-topbar-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: 10px;
        }

        .public-topbar-link,
        .public-topbar-button {
            min-height: 44px;
            margin: 0;
            white-space: nowrap;
        }

        .public-topbar-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 14px;
            border: 1px solid rgba(148, 163, 184, 0.2);
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.82);
            color: #0f172a;
            text-decoration: none;
            font-weight: 800;
        }

        .public-topbar-link:hover {
            background: #edf5ff;
        }

        .hero-card,
        .section-card,
        .stat-card {
            backdrop-filter: blur(16px);
        }

        .hero-card {
            position: relative;
            overflow: hidden;
            padding: clamp(24px, 2vw, 34px);
            border-radius: 30px;
            border-color: rgba(191, 219, 254, 0.48);
            background:
                linear-gradient(135deg, rgba(255, 255, 255, 0.97) 0%, rgba(240, 247, 255, 0.94) 54%, rgba(255, 255, 255, 0.98) 100%);
            box-shadow: 0 28px 75px rgba(15, 23, 42, 0.09);
        }

        .hero-card::before,
        .hero-card::after {
            content: "";
            position: absolute;
            border-radius: 999px;
            pointer-events: none;
        }

        .hero-card::before {
            width: 320px;
            height: 320px;
            top: -120px;
            right: -80px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.18), rgba(37, 99, 235, 0));
        }

        .hero-card::after {
            width: 260px;
            height: 260px;
            bottom: -110px;
            left: -90px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.14), rgba(16, 185, 129, 0));
        }

        .hero {
            position: relative;
            z-index: 1;
            grid-template-columns: minmax(0, 1.3fr) minmax(290px, 0.88fr) minmax(290px, 0.82fr);
            gap: 22px;
        }

        .brand-title,
        .panel-title,
        .section-title,
        .stat-value,
        .snapshot-value {
            font-family: "Space Grotesk", "Segoe UI", Arial, sans-serif;
        }

        .brand-title {
            font-size: clamp(2.3rem, 3.3vw, 3.5rem);
        }

        .brand-copy .muted {
            max-width: 54ch;
            color: #41516a;
            font-size: 0.98rem;
        }

        .hero-spotlight,
        .hero-date-filter {
            border: 1px solid rgba(148, 163, 184, 0.2);
            border-radius: 24px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.94) 0%, rgba(235, 245, 255, 0.92) 100%);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
        }

        .hero-spotlight {
            padding: 24px 22px;
            text-align: left;
        }

        .hero-kicker {
            color: var(--accent);
            letter-spacing: 0.24em;
        }

        .hero-spotlight-title {
            margin-top: 12px;
            font-size: clamp(1.55rem, 2vw, 2.15rem);
        }

        .hero-book-button {
            min-width: 210px;
            min-height: 56px;
            border-radius: 18px;
        }

        .hero-cta-note {
            margin-left: 0;
            max-width: 34ch;
        }

        .hero-book-panel {
            margin-top: 16px;
            border-radius: 22px;
            border-color: rgba(191, 219, 254, 0.5);
            background: rgba(255, 255, 255, 0.92);
        }

        .hero-side {
            gap: 14px;
        }

        .hero-utility-row {
            justify-content: space-between;
        }

        .hero-menu-summary,
        .hero-date-filter,
        .hero-facebook-button {
            box-shadow: 0 16px 32px rgba(15, 23, 42, 0.08);
        }

        .hero-menu-summary {
            border-radius: 18px;
        }

        .hero-menu-panel {
            border-radius: 22px;
            border-color: rgba(148, 163, 184, 0.2);
        }

        .hero-date-filter {
            padding: 18px;
        }

        .hero-date-filter label,
        .booking-card label {
            font-weight: 700;
            color: #162033;
            font-size: 0.95rem;
        }

        .input,
        .select,
        .button,
        .button-secondary {
            border-radius: 16px;
            padding: 13px 15px;
        }

        .input,
        .select {
            border-color: rgba(148, 163, 184, 0.26);
            background: rgba(255, 255, 255, 0.96);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.85);
        }

        .input:focus,
        .select:focus {
            outline: 0;
            border-color: rgba(37, 99, 235, 0.45);
            box-shadow:
                0 0 0 4px rgba(37, 99, 235, 0.08),
                inset 0 1px 0 rgba(255, 255, 255, 0.92);
        }

        .button,
        .button-secondary {
            font-weight: 800;
        }

        .button {
            background: linear-gradient(135deg, #0f172a 0%, #1d4ed8 100%);
            box-shadow: 0 16px 30px rgba(37, 99, 235, 0.22);
        }

        .button-secondary {
            border-color: rgba(148, 163, 184, 0.2);
            background: rgba(255, 255, 255, 0.86);
        }

        .button-facebook {
            border-radius: 18px;
        }

        .overview-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
        }

        .stat-card {
            display: grid;
            gap: 10px;
            padding: 22px;
            border: 1px solid rgba(191, 219, 254, 0.45);
            border-radius: 24px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.95) 0%, rgba(239, 246, 255, 0.92) 100%);
            box-shadow: 0 22px 44px rgba(15, 23, 42, 0.06);
        }

        .stat-card-primary {
            background:
                linear-gradient(135deg, rgba(15, 23, 42, 0.96) 0%, rgba(29, 78, 216, 0.94) 100%);
            color: #eff6ff;
        }

        .stat-card-primary .stat-label,
        .stat-card-primary .stat-detail {
            color: rgba(239, 246, 255, 0.78);
        }

        .stat-label,
        .section-kicker {
            color: var(--accent);
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }

        .stat-value {
            font-size: clamp(1.4rem, 2vw, 2.2rem);
            line-height: 1.05;
            letter-spacing: -0.04em;
        }

        .stat-detail {
            color: #516076;
            font-size: 0.92rem;
        }

        .public-grid {
            grid-template-columns: minmax(0, 1.18fr) minmax(380px, 0.82fr);
            gap: 24px;
        }

        .admin-shortcut-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 20px;
            padding: 18px 20px;
            border: 1px solid rgba(191, 219, 254, 0.44);
            border-radius: 24px;
            background:
                linear-gradient(135deg, rgba(15, 23, 42, 0.96) 0%, rgba(29, 78, 216, 0.92) 58%, rgba(56, 189, 248, 0.84) 100%);
            box-shadow: 0 22px 52px rgba(15, 23, 42, 0.14);
            color: #eff6ff;
        }

        .admin-shortcut-copy {
            display: grid;
            gap: 6px;
        }

        .admin-shortcut-copy strong {
            font-family: "Space Grotesk", "Segoe UI", Arial, sans-serif;
            font-size: 1.25rem;
            line-height: 1.05;
            letter-spacing: -0.03em;
        }

        .admin-shortcut-copy .stat-label,
        .admin-shortcut-copy .stat-detail {
            color: rgba(239, 246, 255, 0.82);
        }

        .admin-shortcut-button {
            margin: 0;
            min-width: 220px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            background: rgba(255, 255, 255, 0.14);
            color: #ffffff;
            box-shadow: none;
        }

        .admin-shortcut-button:hover {
            background: rgba(255, 255, 255, 0.22);
        }

        .main-column,
        .side-column {
            display: grid;
            gap: 24px;
            align-content: start;
        }

        .section-card {
            border-radius: 28px;
            padding: clamp(22px, 1.7vw, 30px);
            border-color: rgba(191, 219, 254, 0.4);
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.96) 0%, rgba(247, 250, 252, 0.96) 100%);
        }

        .section-heading {
            display: grid;
            gap: 6px;
            margin-bottom: 20px;
        }

        .section-title {
            margin: 0;
            font-size: clamp(1.5rem, 2vw, 2rem);
            line-height: 1.08;
            letter-spacing: -0.04em;
        }

        .section-lead {
            margin: 0;
            max-width: 70ch;
            color: #516076;
        }

        .rates-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 14px;
        }

        .rate-card {
            display: grid;
            gap: 10px;
            min-height: 170px;
            padding: 18px;
            border: 1px solid rgba(191, 219, 254, 0.44);
            border-radius: 22px;
            background:
                linear-gradient(180deg, rgba(239, 246, 255, 0.9) 0%, rgba(255, 255, 255, 0.98) 100%);
        }

        .rate-card strong {
            font-family: "Space Grotesk", "Segoe UI", Arial, sans-serif;
            font-size: 1.6rem;
            line-height: 1;
            color: #0f172a;
        }

        .rate-card small {
            color: #516076;
            font-size: 0.88rem;
        }

        .rates-summary-box {
            margin-top: 16px;
            margin-bottom: 0;
        }

        .booking-preflight {
            display: grid;
            gap: 14px;
            margin-bottom: 18px;
        }

        .booking-form {
            display: block;
        }

        .booking-form .button[type="submit"] {
            width: 100%;
            min-height: 58px;
            margin-top: 8px;
            border-radius: 18px;
        }

        .account-box,
        .notice-box,
        .slot,
        .booking-item,
        .court-panel,
        .snapshot-metric {
            border-radius: 22px;
            border-color: rgba(191, 219, 254, 0.4);
            box-shadow: 0 14px 28px rgba(15, 23, 42, 0.04);
        }

        .account-box {
            background:
                linear-gradient(180deg, rgba(239, 246, 255, 0.88) 0%, rgba(255, 255, 255, 0.92) 100%);
        }

        .notice-box {
            background:
                linear-gradient(180deg, rgba(255, 247, 237, 0.96) 0%, rgba(255, 251, 235, 0.94) 100%);
        }

        .booking-total-box strong {
            display: inline-block;
            margin-top: 6px;
            font-family: "Space Grotesk", "Segoe UI", Arial, sans-serif;
            font-size: clamp(1.8rem, 2vw, 2.3rem);
            line-height: 1.05;
            color: #0f172a;
        }

        .rental-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }

        .availability-card .court-panel {
            overflow: hidden;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(246, 250, 255, 0.96) 100%);
        }

        .availability-card .court-panel summary {
            padding: 20px 22px;
        }

        .availability-card .slots-grid {
            padding: 18px 20px 20px;
        }

        .time-chip {
            gap: 8px;
            min-height: 118px;
            padding: 14px;
            background: rgba(255, 255, 255, 0.88);
        }

        .time-chip.available {
            background: linear-gradient(180deg, rgba(240, 253, 244, 0.94) 0%, rgba(255, 255, 255, 0.98) 100%);
        }

        .time-chip.booked {
            background: linear-gradient(180deg, rgba(255, 247, 237, 0.96) 0%, rgba(255, 255, 255, 0.98) 100%);
        }

        .snapshot-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .snapshot-metric {
            padding: 18px;
            border: 1px solid rgba(191, 219, 254, 0.4);
            background:
                linear-gradient(180deg, rgba(239, 246, 255, 0.82) 0%, rgba(255, 255, 255, 0.96) 100%);
        }

        .snapshot-value {
            display: block;
            margin-top: 6px;
            font-size: 1.85rem;
            line-height: 1;
            letter-spacing: -0.04em;
        }

        .snapshot-metric span:last-child {
            display: block;
            margin-top: 8px;
            color: #516076;
            font-size: 0.9rem;
        }

        .reservation-feed {
            display: grid;
            gap: 14px;
        }

        .reservations-card .booking-item {
            gap: 8px;
            padding: 18px;
            border: 1px solid rgba(191, 219, 254, 0.42);
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(245, 249, 255, 0.98) 100%);
        }

        .reservations-card .booking-item strong {
            font-size: 1.05rem;
            color: #0f172a;
        }

        .badge-ok,
        .badge-full,
        .badge-mine {
            font-weight: 800;
        }

        @media (min-width: 1550px) {
            .page-shell {
                width: min(100%, 2000px);
            }

            .hero {
                grid-template-columns: minmax(0, 1.42fr) minmax(320px, 0.85fr) minmax(320px, 0.8fr);
            }

            .public-grid {
                grid-template-columns: minmax(0, 1.24fr) minmax(420px, 0.76fr);
            }
        }

        @media (max-width: 1180px) {
            .overview-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .public-grid {
                grid-template-columns: 1fr;
            }

            .admin-shortcut-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .admin-shortcut-button {
                width: 100%;
            }
        }

        @media (max-width: 980px) {
            .hero {
                grid-template-columns: 1fr;
            }

            .hero-utility-row {
                justify-content: space-between;
            }

            .hero-spotlight {
                text-align: left;
            }
        }

        @media (max-width: 720px) {
            .public-topbar-inner {
                align-items: stretch;
                flex-direction: column;
            }

            .public-topbar-actions {
                width: 100%;
                justify-content: stretch;
            }

            .public-topbar-link,
            .public-topbar-button {
                flex: 1 1 120px;
            }

            .overview-grid,
            .snapshot-grid {
                grid-template-columns: 1fr;
            }

            .hero-card,
            .section-card,
            .stat-card {
                border-radius: 24px;
            }

            .brand-title {
                font-size: 2.25rem;
            }
        }
    </style>

    <div id="userAgreementOverlay" class="agreement-overlay" role="dialog" aria-modal="true" aria-labelledby="agreementTitle">
        <div class="agreement-card">
            <p class="agreement-eyebrow">User Agreement</p>
            <h2 id="agreementTitle" class="agreement-title">Please review and accept before entering the website.</h2>
            <p class="agreement-lead">
                This booking platform follows a strict reservation policy similar to major online booking services. By continuing, you confirm that you understand and agree to the terms below.
            </p>

            <div class="agreement-grid">
                <div class="agreement-panel">
                    <strong>1. Final Booking Policy</strong>
                    <p>All reservations are treated as final once a booking is submitted and confirmed. No cancellation, refund, reversal, or transfer will be allowed after a slot has been booked.</p>
                </div>

                <div class="agreement-panel">
                    <strong>2. Rain and Court Availability</strong>
                    <p>If weather or uncovered court conditions affect play, rescheduling is not automatic. An administrator must first unlock the reservation, and any replacement slot will still depend on availability and policy approval.</p>
                </div>

                <div class="agreement-panel">
                    <strong>3. Payment and Reservation Accuracy</strong>
                    <p>You are responsible for choosing the correct date, time slot, court, rental items, and payment details before confirming the reservation. Online booking uses GCash payment only.</p>
                </div>

                <div class="agreement-panel">
                    <strong>4. Reservation Information</strong>
                    <p>Your reservation details may be displayed according to the facility settings managed by the administrator. Public reservation visibility will only show the customer name when enabled by admin, never the email address or contact number.</p>
                </div>
            </div>

            <div class="agreement-warning">
                <strong>Important notice</strong>
                Once you accept this agreement and continue, you are entering a booking system where confirmed reservations are binding and cannot be cancelled after booking.
            </div>

            <div class="agreement-actions">
                <label class="agreement-check" for="agreementAccepted">
                    <input id="agreementAccepted" type="checkbox">
                    <span>I have read and agree to the reservation policy, including the rule that bookings cannot be cancelled once booked.</span>
                </label>

                <button id="agreementAcceptButton" class="button agreement-button" type="button" disabled>I Agree and Enter the Website</button>
            </div>
        </div>
    </div>

    @auth
        @if(auth()->user()->isAdmin())
            <div class="admin-shortcut-bar">
                <div class="admin-shortcut-copy">
                    <span class="stat-label">Admin Shortcut</span>
                    <strong>Need to go back to the admin side?</strong>
                    <span class="stat-detail">Open the full admin dashboard anytime while reviewing the public booking page.</span>
                </div>

                <a href="{{ route('admin.dashboard') }}" class="button admin-shortcut-button">Open Admin Dashboard</a>
            </div>
        @endif
    @endauth

    <div class="overview-grid">
        <div class="stat-card stat-card-primary">
            <span class="stat-label">Viewing Date</span>
            <strong class="stat-value">{{ $selectedDateHeadline }}</strong>
            <span class="stat-detail">{{ $pageSupportNote }}</span>
        </div>

        <div class="stat-card">
            <span class="stat-label">Open Slots</span>
            <strong class="stat-value">{{ $totalAvailableSlots }}</strong>
            <span class="stat-detail">
                Across {{ count($courtAvailability) }} courts and {{ $totalCourtSlots }} total hourly slots.
            </span>
        </div>

        <div class="stat-card">
            <span class="stat-label">Booked Slots</span>
            <strong class="stat-value">{{ $totalBookedSlots }}</strong>
            <span class="stat-detail">
                {{ $publicReservationsCount }} reservation{{ $publicReservationsCount === 1 ? '' : 's' }} visible for this date.
            </span>
        </div>

        <div class="stat-card">
            <span class="stat-label">Booking Window</span>
            <strong class="stat-value">{{ $customerBookingWindowDays }} days</strong>
            <span class="stat-detail">
                Customers can book until {{ \Illuminate\Support\Carbon::parse($maxBookingDate)->format('F d, Y') }}.
            </span>
        </div>
    </div>

    <div class="grid public-grid">
        <div class="main-column">
            @if(! auth()->check() || ! auth()->user()->isAdmin())
            <div class="card section-card rates-card">
                <div class="section-heading">
                    <span class="section-kicker">Pricing</span>
                    <h2 class="panel-title section-title">Current Rates</h2>
                    <p class="muted section-lead">
                        Published court and rental rates for the booking page. Final court pricing still follows the selected court and time slot.
                    </p>
                </div>

                <div class="rates-grid">
                    <div class="rate-card">
                        <span class="muted">Court Rate</span>
                        <strong>PHP {{ number_format($reservationFee, 2) }}</strong>
                        <small>Base court fee shown on the booking page before time-based pricing is applied.</small>
                    </div>

                    <div class="rate-card">
                        <span class="muted">Old Paddle Rent</span>
                        <strong>PHP {{ number_format($oldPaddleRentRate, 2) }}</strong>
                        <small>Good for standard paddle rentals when you only need the essentials.</small>
                    </div>

                    <div class="rate-card">
                        <span class="muted">New Paddle Rent</span>
                        <strong>PHP {{ number_format($newPaddleRentRate, 2) }}</strong>
                        <small>Updated paddle option for players who prefer the newer rental set.</small>
                    </div>

                    <div class="rate-card">
                        <span class="muted">Ball Rate</span>
                        <strong>PHP {{ number_format($ballRate, 2) }}</strong>
                        <small>Add balls to your booking anytime while completing the reservation form.</small>
                    </div>
                </div>

                <div class="account-box rates-summary-box">
                    <strong>Base Pricing Summary</strong><br><br>
                    <span class="muted">Court Rate: PHP {{ number_format($reservationFee, 2) }}</span><br>
                    <span class="muted">Paddle Rent: PHP {{ number_format($oldPaddleRentRate, 2) }}</span><br>
                    <span class="muted">New Paddle Rent: PHP {{ number_format($newPaddleRentRate, 2) }}</span><br>
                    <span class="muted">Ball Rate: PHP {{ number_format($ballRate, 2) }}</span>
                </div>
            </div>
            @endif

            @auth
            @if(! auth()->user()->isAdmin())
            <div id="booking-section" class="card booking-card section-card">
                <div class="section-heading">
                    <span class="section-kicker">Booking Form</span>
                    <h2 class="panel-title section-title">Reserve a Slot</h2>
                    <p class="muted section-lead">
                        Choose your court, start time, and booking duration below. The page updates your running total as you adjust the reservation.
                    </p>
                </div>

                    <div class="booking-preflight">
                    <div class="account-box">
                        <strong>{{ auth()->user()->name }}</strong><br>
                        <span class="muted">{{ auth()->user()->email }}</span><br><br>
                        <span class="muted">Your reservation will be saved under your account name automatically. Add a contact number so admin can call if there is a reschedule or schedule problem.</span>
                    </div>

                    @if(! $contactNumberReady)
                        <div class="notice-box">
                            <strong>Contact number setup is not ready yet.</strong><br><br>
                            <span class="muted" style="color: #9a3412;">
                                Admin needs to run the latest migration first before new bookings can be saved with contact numbers.
                            </span>
                        </div>
                    @endif

                    @if(! ($durationReady ?? true))
                        <div class="notice-box">
                            <strong>Booking duration setup is not ready yet.</strong><br><br>
                            <span class="muted" style="color: #9a3412;">
                                Admin needs to run the latest migration first before customers can choose multiple booking hours.
                            </span>
                        </div>
                    @endif

                    <div class="notice-box">
                        <strong>No cancellation once a booking is confirmed.</strong><br><br>
                        <span class="muted" style="color: #9a3412;">
                            If rain or uncovered court conditions affect play, only the admin can unlock your reservation for reschedule.
                            Once unlocked, you may choose a new court, date, and time within {{ $customerBookingWindowDays }} days from the original booking date.
                        </span>
                    </div>

                    <div class="notice-box">
                        <strong>Online payment options are ready for client checkout.</strong><br><br>
                        <span class="muted" style="color: #9a3412;">
                            You can open the PayMongo payment link for client payment, or enter a manual GCash reference.
                            If you want to pay walk-in or cash, please go directly to the office. Walk-in payments are handled by admin only.
                        </span>
                    </div>
                    </div>

                    <form method="POST" action="{{ route('reservations.store') }}" class="booking-form">
                        @csrf

                        <label for="booking_date">Booking Date</label>
                        <input id="booking_date" class="input" type="date" name="booking_date" value="{{ old('booking_date', $selectedDate) }}" min="{{ $minBookingDate }}" max="{{ $maxBookingDate }}" required>
                        <p class="muted" style="margin-top: -8px; margin-bottom: 14px;">
                            Customers can book from today until {{ \Illuminate\Support\Carbon::parse($maxBookingDate)->format('F d, Y') }} only.
                        </p>

                        <label for="court_number">Select Court</label>
                        <select id="court_number" class="select" name="court_number" required>
                            <option value="" data-label="Choose a court first" data-rate="0" disabled>Select court</option>
                            @foreach($courts as $court)
                                <option
                                    value="{{ $court['number'] }}"
                                    data-label="{{ $court['label'] }}"
                                    data-day-rate="{{ $court['day_rate'] }}"
                                    data-day-start="{{ $court['day_starts_at'] }}"
                                    data-day-end="{{ $court['day_ends_at'] }}"
                                    data-night-rate="{{ $court['night_rate'] }}"
                                    data-night-start="{{ $court['night_starts_at'] }}"
                                    data-night-end="{{ $court['night_ends_at'] }}"
                                    @selected((string) old('court_number', $courts[0]['number'] ?? '') === (string) $court['number'])
                                >
                                    {{ $court['label'] }}
                                </option>
                            @endforeach
                        </select>
                        <p class="muted" style="margin-top: -8px; margin-bottom: 14px;">
                            Selected court: <strong id="selectedCourtLabel">{{ $selectedCourtLabel }}</strong> | Court subtotal:
                            <strong id="selectedCourtRate">{{ $hasSelectedSchedule ? 'PHP ' . number_format($selectedCourtRate, 2) : 'Choose time slot first' }}</strong>
                        </p>

                        <label for="time_slot">Start Time</label>
                        <select id="time_slot" class="select" name="time_slot" required>
                            <option value="">Select time slot</option>
                            @foreach($timeSlots as $slot)
                                <option value="{{ $slot }}" @selected(old('time_slot') == $slot)>
                                    {{ $slot }}
                                </option>
                            @endforeach
                        </select>

                        <label for="duration_hours">How Many Hours</label>
                        <select id="duration_hours" class="select" name="duration_hours" required>
                            @foreach($selectedDurationOptions as $durationOption)
                                <option value="{{ $durationOption }}" @selected($selectedDurationHours === (int) $durationOption)>
                                    {{ $durationOption }} hour{{ (int) $durationOption === 1 ? '' : 's' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="muted" style="margin-top: -8px; margin-bottom: 14px;">
                            Selected schedule: <strong id="selectedScheduleLabel">{{ $selectedScheduleLabel }}</strong><br>
                            Longer bookings need sunod-sunod nga hourly slots. Some start times only allow 1 hour.
                        </p>

                        <label for="players">Players</label>
                        <select id="players" class="select" name="players" required>
                            <option value="2" @selected(old('players') == '2')>2 Players</option>
                            <option value="4" @selected(old('players', '4') == '4')>4 Players</option>
                            <option value="6" @selected(old('players') == '6')>6 Players</option>
                            <option value="8" @selected(old('players') == '8')>8 Players</option>
                        </select>

                        <div class="rental-grid">
                            <div>
                                <label for="new_paddle_rent_quantity">New Paddle Quantity</label>
                                <input
                                    id="new_paddle_rent_quantity"
                                    class="input"
                                    type="number"
                                    name="new_paddle_rent_quantity"
                                    min="0"
                                    max="20"
                                    value="{{ old('new_paddle_rent_quantity', 0) }}"
                                >
                                <p class="muted" style="margin-top: -8px; margin-bottom: 0;">
                                    Pila ka bag-o nga paddle ang i-rent. Rate per paddle: PHP {{ number_format($newPaddleRentRate, 2) }}
                                </p>
                            </div>

                            <div>
                                <label for="paddle_rent_quantity">Old Paddle Quantity</label>
                                <input
                                    id="paddle_rent_quantity"
                                    class="input"
                                    type="number"
                                    name="paddle_rent_quantity"
                                    min="0"
                                    max="20"
                                    value="{{ old('paddle_rent_quantity', 0) }}"
                                >
                                <p class="muted" style="margin-top: -8px; margin-bottom: 0;">
                                    Pila ka daan nga paddle ang i-rent. Rate per paddle: PHP {{ number_format($oldPaddleRentRate, 2) }}
                                </p>
                            </div>

                            <div>
                                <label for="ball_quantity">Ball Quantity</label>
                                <input
                                    id="ball_quantity"
                                    class="input"
                                    type="number"
                                    name="ball_quantity"
                                    min="0"
                                    max="20"
                                    value="{{ old('ball_quantity', 0) }}"
                                >
                                <p class="muted" style="margin-top: -8px; margin-bottom: 0;">
                                    Pila ka bola ang i-rent. Rate per ball: PHP {{ number_format($ballRate, 2) }}
                                </p>
                            </div>
                        </div>

                        <div class="account-box booking-total-box">
                            <span class="muted">Estimated total</span><br>
                            <strong id="selectedBookingTotal">{{ $hasSelectedSchedule ? 'PHP ' . number_format($selectedBookingTotal, 2) : 'Choose time slot first' }}</strong>
                        </div>

                        <label for="contact_number">Contact Number</label>
                        <input
                            id="contact_number"
                            class="input"
                            type="text"
                            name="contact_number"
                            value="{{ old('contact_number') }}"
                            placeholder="Enter your active mobile number"
                            required
                        >
                        <p class="muted" style="margin-top: -8px; margin-bottom: 14px;">
                            This will be used if admin needs to contact you for rain reschedule or booking concerns.
                        </p>

                        <label for="payment_method">Payment Method</label>
                        <select id="payment_method" class="select" name="payment_method" required>
                            @if($payMongoCheckoutReady ?? false)
                                <option value="paymongo" @selected($selectedPaymentMethod === 'paymongo')>PayMongo Payment Link</option>
                            @endif
                            <option value="gcash" @selected($selectedPaymentMethod === 'gcash')>Manual GCash Reference</option>
                        </select>

                        <div id="payMongoBox" class="account-box" style="margin-top: 6px; display: {{ $selectedPaymentMethod === 'paymongo' ? 'block' : 'none' }};">
                            <strong>PayMongo Payment Link</strong><br>
                            <span class="muted">
                                Your reservation will be saved first with a pending payment status, then you will be redirected to your PayMongo payment link.
                                After paying, admin can verify the payment and update the booking status.
                            </span>
                            <p class="muted" style="margin-top: 12px; margin-bottom: 0;">
                                Current total to pay: <strong id="paymongoTotalAmount">{{ $hasSelectedSchedule ? 'PHP ' . number_format($selectedBookingTotal, 2) : 'Choose time slot first' }}</strong>
                            </p>
                        </div>

                        <div id="gcashBox" class="account-box" style="margin-top: 6px; display: {{ $selectedPaymentMethod === 'gcash' ? 'block' : 'none' }};">
                            <p class="muted">
                                GCash Number: <strong>0917-123-4567</strong><br>
                                Account Name: <strong>Pickle BALLan ni Juan</strong><br>
                                Court Subtotal: <strong id="gcashCourtRate">{{ $hasSelectedSchedule ? 'PHP ' . number_format($selectedCourtRate, 2) : 'Choose time slot first' }}</strong><br>
                                New Paddle Rent: <strong id="gcashNewPaddleSummary">{{ $selectedNewPaddleRentQuantity > 0 ? $selectedNewPaddleRentQuantity . ' x PHP ' . number_format($newPaddleRentRate, 2) . ' = PHP ' . number_format($selectedNewPaddleRentQuantity * $newPaddleRentRate, 2) : 'None' }}</strong><br>
                                Old Paddle Rent: <strong id="gcashOldPaddleSummary">{{ $selectedOldPaddleRentQuantity > 0 ? $selectedOldPaddleRentQuantity . ' x PHP ' . number_format($oldPaddleRentRate, 2) . ' = PHP ' . number_format($selectedOldPaddleRentQuantity * $oldPaddleRentRate, 2) : 'None' }}</strong><br>
                                Ball Rent: <strong id="gcashBallSummary">{{ $selectedBallQuantity > 0 ? $selectedBallQuantity . ' x PHP ' . number_format($ballRate, 2) . ' = PHP ' . number_format($selectedBallQuantity * $ballRate, 2) : 'None' }}</strong><br>
                                Total Due: <strong id="gcashTotalAmount">{{ $hasSelectedSchedule ? 'PHP ' . number_format($selectedBookingTotal, 2) : 'Choose time slot first' }}</strong>
                            </p>

                            <label for="payment_reference">GCash Reference Number</label>
                            <input id="payment_reference" class="input" type="text" name="payment_reference" value="{{ old('payment_reference') }}" placeholder="Enter GCash reference number">
                        </div>

                        <button id="paymentSubmitButton" class="button" type="submit">
                            {{ $selectedPaymentMethod === 'paymongo' ? 'Save Booking and Open PayMongo Link' : 'Pay and Confirm Reservation' }}
                        </button>
                    </form>
            </div>
            @endif
            @endauth

            <div class="card section-card availability-card">
                <div class="section-heading">
                    <span class="section-kicker">Live Schedule</span>
                    <h2 class="panel-title section-title">Availability for {{ \Illuminate\Support\Carbon::parse($selectedDate)->format('F d, Y') }}</h2>
                    <p class="muted section-lead">
                        Click a court to see which time slots are still available.
                    </p>
                </div>

                @foreach($courtAvailability as $court)
                    <details class="court-panel" @if($loop->first) open @endif>
                        <summary>
                            <div class="court-summary">
                                <strong>{{ $court['court_label'] }}</strong>
                                @if($court['full'])
                                    <span class="badge-full">No more time slots</span>
                                @else
                                    <span class="badge-ok">{{ $court['available_slots'] }} time slots available</span>
                                @endif
                            </div>

                            <div class="court-summary-meta">
                                <span class="muted">{{ $court['rate_summary'] }} | {{ $court['booked_slots'] }} booked | {{ count($court['slots']) }} total slots</span>
                                <span class="court-arrow">v</span>
                            </div>
                        </summary>

                        <div class="slots-grid">
                            @foreach($court['slots'] as $slot)
                                <div class="time-chip {{ $slot['available'] ? 'available' : 'booked' }}">
                                    <strong>{{ $slot['slot'] }}</strong>
                                    <span class="muted">{{ $slot['period'] }} | PHP {{ number_format($slot['rate'], 2) }}</span>
                                    @if($slot['available'])
                                        <span class="badge-ok">Available</span>
                                    @else
                                        <span class="badge-full">Booked</span>
                                        @if(auth()->check() && auth()->user()->isAdmin() && $slot['reservation'])
                                            <span class="muted">{{ $slot['reservation']->customer_name }}</span>
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </details>
                @endforeach
            </div>
        </div>

        <div class="side-column">
            <div class="card section-card snapshot-card">
                <div class="section-heading">
                    <span class="section-kicker">Daily Snapshot</span>
                    <h2 class="panel-title section-title">Quick Overview</h2>
                    <p class="muted section-lead">Live booking numbers for {{ $selectedDateHeadline }}.</p>
                </div>

                <div class="snapshot-grid">
                    <div class="snapshot-metric">
                        <span class="muted">Reservations</span>
                        <strong class="snapshot-value">{{ $publicReservationsCount }}</strong>
                        <span>Visible bookings on this date.</span>
                    </div>

                    <div class="snapshot-metric">
                        <span class="muted">Courts</span>
                        <strong class="snapshot-value">{{ count($courtAvailability) }}</strong>
                        <span>Live court panels with hourly schedules.</span>
                    </div>

                    <div class="snapshot-metric">
                        <span class="muted">Open Slots</span>
                        <strong class="snapshot-value">{{ $totalAvailableSlots }}</strong>
                        <span>Still available across all courts.</span>
                    </div>

                    <div class="snapshot-metric">
                        <span class="muted">Booked Slots</span>
                        <strong class="snapshot-value">{{ $totalBookedSlots }}</strong>
                        <span>Already taken for {{ $selectedDateHeadline }}.</span>
                    </div>
                </div>
            </div>

            <div class="card section-card reservations-card">
                <div class="section-heading">
                    <span class="section-kicker">Reservation Feed</span>
                    <h2 class="panel-title section-title">Reservations for {{ \Illuminate\Support\Carbon::parse($selectedDate)->format('F d, Y') }}</h2>
                    <p class="muted section-lead">This list updates based on the selected date and your account visibility rules.</p>
                </div>

                <div class="reservation-feed">

                @forelse($bookings as $booking)
                    <div class="booking-item">
                        @if(auth()->check() && auth()->user()->isAdmin())
                            <strong>{{ $booking->customer_name }}</strong>
                            <span class="muted">
                                {{ $booking->contact_number ?: 'No contact number' }}
                                @if($booking->user?->email)
                                    | {{ $booking->user->email }}
                                @elseif($booking->user_id === null)
                                    | Walk-in / No account
                                @endif
                            </span>
                        @elseif(auth()->check() && $booking->user_id === auth()->id())
                            <strong>Your reservation</strong>
                            <span class="badge-mine">This booking is yours</span>
                        @elseif($showPublicCustomerNames ?? false)
                            <strong>{{ $booking->customer_name }}</strong>
                        @else
                            <strong>Reserved slot</strong>
                        @endif

                        @php
                            $paddleRentQuantity = max(0, (int) ($booking->paddle_rent_quantity ?? 0));
                            $newPaddleRentQuantity = max(0, (int) ($booking->new_paddle_rent_quantity ?? 0));
                            $ballQuantity = max(0, (int) ($booking->ball_quantity ?? 0));
                        @endphp
                        <small class="muted">{{ $resolveCourtLabel($booking->court_number, $booking->court_name) }}</small>
                        <span class="muted">{{ $booking->timeRangeLabel() }} | {{ $booking->durationLabel() }}</span>
                        <span>{{ $booking->players }} players</span>
                        @if($paddleRentQuantity > 0 || $newPaddleRentQuantity > 0 || $ballQuantity > 0)
                            <span class="muted">
                                Rentals:
                                @if($paddleRentQuantity > 0)
                                    {{ $paddleRentQuantity }} old paddle{{ $paddleRentQuantity === 1 ? '' : 's' }}
                                @endif
                                @if($paddleRentQuantity > 0 && ($newPaddleRentQuantity > 0 || $ballQuantity > 0))
                                    |
                                @endif
                                @if($newPaddleRentQuantity > 0)
                                    {{ $newPaddleRentQuantity }} new paddle{{ $newPaddleRentQuantity === 1 ? '' : 's' }}
                                @endif
                                @if($newPaddleRentQuantity > 0 && $ballQuantity > 0)
                                    |
                                @endif
                                @if($ballQuantity > 0)
                                    {{ $ballQuantity }} ball{{ $ballQuantity === 1 ? '' : 's' }}
                                @endif
                            </span>
                        @endif
                        <span class="muted">Amount: PHP {{ number_format($booking->amount, 2) }}</span>
                        <span class="muted">
                            {{ strtoupper($booking->payment_method) }}
                            @if($booking->payment_reference && auth()->check() && auth()->user()->isAdmin())
                                | Ref: {{ $booking->payment_reference }}
                            @endif
                            | {{ $booking->payment_status }}
                        </span>
                    </div>
                @empty
                    <div class="muted">Wala pay naka-book ani nga adlaw.</div>
                @endforelse
                </div>
            </div>
        </div>
</div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const heroBookNowButton = document.getElementById('heroBookNowButton');
        const heroBookNowPanel = document.getElementById('heroBookNowPanel');

        if (!heroBookNowButton || !heroBookNowPanel) {
            return;
        }

        const closeHeroBookNowPanel = () => {
            heroBookNowPanel.hidden = true;
            heroBookNowButton.setAttribute('aria-expanded', 'false');
        };

        const openHeroBookNowPanel = () => {
            heroBookNowPanel.hidden = false;
            heroBookNowButton.setAttribute('aria-expanded', 'true');
        };

        heroBookNowButton.addEventListener('click', function (event) {
            event.stopPropagation();

            if (heroBookNowPanel.hidden) {
                openHeroBookNowPanel();
            } else {
                closeHeroBookNowPanel();
            }
        });

        heroBookNowPanel.addEventListener('click', function (event) {
            event.stopPropagation();
        });

        document.addEventListener('click', function () {
            if (!heroBookNowPanel.hidden) {
                closeHeroBookNowPanel();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeHeroBookNowPanel();
            }
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const agreementStorageKey = 'pickleball_user_agreement_v1';
        const agreementOverlay = document.getElementById('userAgreementOverlay');
        const agreementAcceptedCheckbox = document.getElementById('agreementAccepted');
        const agreementAcceptButton = document.getElementById('agreementAcceptButton');

        if (!agreementOverlay || !agreementAcceptedCheckbox || !agreementAcceptButton) {
            return;
        }

        const hasAcceptedAgreement = (() => {
            try {
                return window.localStorage.getItem(agreementStorageKey) === 'accepted';
            } catch (error) {
                return false;
            }
        })();

        const closeAgreement = () => {
            agreementOverlay.hidden = true;
            document.body.style.overflow = '';
        };

        const openAgreement = () => {
            agreementOverlay.hidden = false;
            document.body.style.overflow = 'hidden';
        };

        const updateAgreementButton = () => {
            agreementAcceptButton.disabled = !agreementAcceptedCheckbox.checked;
        };

        if (hasAcceptedAgreement) {
            closeAgreement();
        } else {
            openAgreement();
        }

        agreementAcceptedCheckbox.addEventListener('change', updateAgreementButton);
        updateAgreementButton();

        agreementAcceptButton.addEventListener('click', function () {
            if (!agreementAcceptedCheckbox.checked) {
                return;
            }

            try {
                window.localStorage.setItem(agreementStorageKey, 'accepted');
            } catch (error) {
                // Continue opening the page even if local storage is unavailable.
            }

            closeAgreement();
        });
    });
</script>

<script>
    function togglePaymentMethod() {
        const paymentMethodSelect = document.getElementById('payment_method');
        const gcashBox = document.getElementById('gcashBox');
        const payMongoBox = document.getElementById('payMongoBox');
        const paymentReferenceInput = document.getElementById('payment_reference');
        const paymentSubmitButton = document.getElementById('paymentSubmitButton');

        if (!paymentMethodSelect || !gcashBox || !payMongoBox || !paymentSubmitButton) {
            return;
        }

        const selectedMethod = paymentMethodSelect.value;
        const showPayMongo = selectedMethod === 'paymongo';

        payMongoBox.style.display = showPayMongo ? 'block' : 'none';
        gcashBox.style.display = showPayMongo ? 'none' : 'block';

        if (paymentReferenceInput) {
            paymentReferenceInput.required = !showPayMongo;
        }

        paymentSubmitButton.textContent = showPayMongo
            ? 'Save Booking and Open PayMongo Link'
            : 'Pay and Confirm Reservation';
    }

    function updateCourtPricing() {
        const courtSelect = document.getElementById('court_number');
        const timeSlotSelect = document.getElementById('time_slot');
        const durationSelect = document.getElementById('duration_hours');
        const newPaddleRentQuantityInput = document.getElementById('new_paddle_rent_quantity');
        const paddleRentQuantityInput = document.getElementById('paddle_rent_quantity');
        const ballQuantityInput = document.getElementById('ball_quantity');
        const selectedCourtLabel = document.getElementById('selectedCourtLabel');
        const selectedScheduleLabel = document.getElementById('selectedScheduleLabel');
        const selectedCourtRate = document.getElementById('selectedCourtRate');
        const selectedBookingTotal = document.getElementById('selectedBookingTotal');
        const gcashCourtRate = document.getElementById('gcashCourtRate');
        const gcashNewPaddleSummary = document.getElementById('gcashNewPaddleSummary');
        const gcashOldPaddleSummary = document.getElementById('gcashOldPaddleSummary');
        const gcashBallSummary = document.getElementById('gcashBallSummary');
        const gcashTotalAmount = document.getElementById('gcashTotalAmount');
        const paymongoTotalAmount = document.getElementById('paymongoTotalAmount');

        if (!courtSelect || !timeSlotSelect || !durationSelect || !newPaddleRentQuantityInput || !paddleRentQuantityInput || !ballQuantityInput || !selectedCourtLabel || !selectedScheduleLabel || !selectedCourtRate || !selectedBookingTotal || !gcashCourtRate || !gcashNewPaddleSummary || !gcashOldPaddleSummary || !gcashBallSummary || !gcashTotalAmount || !paymongoTotalAmount) {
            return;
        }

        const oldPaddleRentRate = @json($oldPaddleRentRate);
        const newPaddleRentRate = @json($newPaddleRentRate);
        const ballRate = @json($ballRate);
        const timeSlots = @json(array_values($timeSlots));
        const durationOptionsBySlot = @json($durationOptionsBySlot);

        const timeToMinutes = (timeLabel) => {
            const match = String(timeLabel || '').trim().match(/^(\d{1,2}):(\d{2})\s(AM|PM)$/i);

            if (!match) {
                return null;
            }

            let hours = Number(match[1]) % 12;
            const minutes = Number(match[2]);
            const meridiem = match[3].toUpperCase();

            if (meridiem === 'PM') {
                hours += 12;
            }

            return (hours * 60) + minutes;
        };
        const minutesToTimeLabel = (totalMinutes) => {
            const normalizedMinutes = ((Number(totalMinutes) % 1440) + 1440) % 1440;
            let hours = Math.floor(normalizedMinutes / 60);
            const minutes = normalizedMinutes % 60;
            const meridiem = hours >= 12 ? 'PM' : 'AM';
            const twelveHour = (hours % 12) || 12;

            return `${twelveHour}:${String(minutes).padStart(2, '0')} ${meridiem}`;
        };

        const fallsWithinRange = (slot, from, to) => {
            const slotMinutes = timeToMinutes(slot);
            const fromMinutes = timeToMinutes(from);
            const toMinutes = timeToMinutes(to);

            if (slotMinutes === null || fromMinutes === null || toMinutes === null) {
                return false;
            }

            if (fromMinutes === toMinutes) {
                return true;
            }

            if (fromMinutes < toMinutes) {
                return slotMinutes >= fromMinutes && slotMinutes < toMinutes;
            }

            return slotMinutes >= fromMinutes || slotMinutes < toMinutes;
        };

        const formatPhp = (amount) => `PHP ${Number(amount || 0).toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        })}`;
        const formatDurationLabel = (hours) => `${hours} hour${hours === 1 ? '' : 's'}`;
        const normalizeQuantity = (value) => {
            const parsed = Number(value);

            if (!Number.isFinite(parsed) || parsed <= 0) {
                return 0;
            }

            return Math.floor(parsed);
        };
        const rateForSlot = (slot, dayRate, dayStart, dayEnd, nightRate, nightStart, nightEnd) => {
            if (fallsWithinRange(slot, dayStart, dayEnd)) {
                return dayRate;
            }

            if (fallsWithinRange(slot, nightStart, nightEnd)) {
                return nightRate;
            }

            return dayRate;
        };
        const getSelectedDurationSlots = (startSlot, durationHours) => {
            const startIndex = timeSlots.indexOf(startSlot);

            if (startIndex < 0 || durationHours < 1) {
                return [];
            }

            const selectedSlots = [];
            let previousSlotMinutes = null;

            for (let index = startIndex; index < timeSlots.length && selectedSlots.length < durationHours; index += 1) {
                const slot = timeSlots[index];
                const slotMinutes = timeToMinutes(slot);

                if (slotMinutes === null) {
                    return [];
                }

                if (previousSlotMinutes !== null && slotMinutes !== (previousSlotMinutes + 60)) {
                    break;
                }

                selectedSlots.push(slot);
                previousSlotMinutes = slotMinutes;
            }

            return selectedSlots.length === durationHours ? selectedSlots : [];
        };
        const syncDurationOptions = () => {
            const selectedTimeSlotValue = timeSlotSelect.value;
            const optionValues = Array.isArray(durationOptionsBySlot?.[selectedTimeSlotValue]) && durationOptionsBySlot[selectedTimeSlotValue].length > 0
                ? durationOptionsBySlot[selectedTimeSlotValue].map((value) => Number(value))
                : [1];
            const currentValue = normalizeQuantity(durationSelect.value);
            const nextValue = optionValues.includes(currentValue) ? currentValue : optionValues[0];

            durationSelect.innerHTML = '';

            optionValues.forEach((hours) => {
                const option = document.createElement('option');
                option.value = String(hours);
                option.textContent = formatDurationLabel(hours);
                option.selected = hours === nextValue;
                durationSelect.appendChild(option);
            });
        };
        const formatScheduleLabel = (selectedSlots) => {
            if (selectedSlots.length === 0) {
                return 'Choose time slot first';
            }

            const lastSlotMinutes = timeToMinutes(selectedSlots[selectedSlots.length - 1]);

            if (lastSlotMinutes === null) {
                return selectedSlots[0];
            }

            return `${selectedSlots[0]} - ${minutesToTimeLabel(lastSlotMinutes + 60)} (${formatDurationLabel(selectedSlots.length)})`;
        };

        syncDurationOptions();

        const selectedOption = courtSelect.options[courtSelect.selectedIndex];
        const courtLabel = selectedOption?.dataset?.label || 'Select court';
        const dayRate = Number(selectedOption?.dataset?.dayRate || 0);
        const dayStart = selectedOption?.dataset?.dayStart || '{{ $defaultDayStartTime }}';
        const dayEnd = selectedOption?.dataset?.dayEnd || '{{ $defaultDayEndTime }}';
        const nightRate = Number(selectedOption?.dataset?.nightRate || dayRate);
        const nightStart = selectedOption?.dataset?.nightStart || '{{ $defaultNightStartTime }}';
        const nightEnd = selectedOption?.dataset?.nightEnd || '{{ $defaultNightEndTime }}';
        const selectedTimeSlot = timeSlotSelect.value;
        const selectedDurationHours = normalizeQuantity(durationSelect.value) || 1;
        const selectedDurationSlots = getSelectedDurationSlots(selectedTimeSlot, selectedDurationHours);
        const newPaddleRentQuantity = normalizeQuantity(newPaddleRentQuantityInput.value);
        const oldPaddleRentQuantity = normalizeQuantity(paddleRentQuantityInput.value);
        const ballQuantity = normalizeQuantity(ballQuantityInput.value);
        const newPaddleSubtotal = newPaddleRentQuantity * newPaddleRentRate;
        const oldPaddleSubtotal = oldPaddleRentQuantity * oldPaddleRentRate;
        const ballSubtotal = ballQuantity * ballRate;
        const courtSubtotal = selectedDurationSlots.reduce((total, slot) => total + rateForSlot(
            slot,
            dayRate,
            dayStart,
            dayEnd,
            nightRate,
            nightStart,
            nightEnd,
        ), 0);
        const hasSelectedSchedule = selectedDurationSlots.length > 0;
        const formattedRate = hasSelectedSchedule ? formatPhp(courtSubtotal) : 'Choose time slot first';
        const formattedTotal = hasSelectedSchedule
            ? formatPhp(courtSubtotal + newPaddleSubtotal + oldPaddleSubtotal + ballSubtotal)
            : 'Choose time slot first';

        selectedCourtLabel.textContent = courtLabel;
        selectedScheduleLabel.textContent = formatScheduleLabel(selectedDurationSlots);
        selectedCourtRate.textContent = formattedRate;
        selectedBookingTotal.textContent = formattedTotal;
        gcashCourtRate.textContent = formattedRate;
        gcashNewPaddleSummary.textContent = newPaddleRentQuantity > 0
            ? `${newPaddleRentQuantity} x ${formatPhp(newPaddleRentRate)} = ${formatPhp(newPaddleSubtotal)}`
            : 'None';
        gcashOldPaddleSummary.textContent = oldPaddleRentQuantity > 0
            ? `${oldPaddleRentQuantity} x ${formatPhp(oldPaddleRentRate)} = ${formatPhp(oldPaddleSubtotal)}`
            : 'None';
        gcashBallSummary.textContent = ballQuantity > 0
            ? `${ballQuantity} x ${formatPhp(ballRate)} = ${formatPhp(ballSubtotal)}`
            : 'None';
        gcashTotalAmount.textContent = formattedTotal;
        paymongoTotalAmount.textContent = formattedTotal;
    }

    document.addEventListener('DOMContentLoaded', () => {
        togglePaymentMethod();
        updateCourtPricing();

        const courtSelect = document.getElementById('court_number');
        const paymentMethodSelect = document.getElementById('payment_method');

        if (courtSelect) {
            courtSelect.addEventListener('change', updateCourtPricing);
        }

        if (paymentMethodSelect) {
            paymentMethodSelect.addEventListener('change', togglePaymentMethod);
        }

        const timeSlotSelect = document.getElementById('time_slot');
        const durationSelect = document.getElementById('duration_hours');
        const newPaddleRentQuantityInput = document.getElementById('new_paddle_rent_quantity');
        const paddleRentQuantityInput = document.getElementById('paddle_rent_quantity');
        const ballQuantityInput = document.getElementById('ball_quantity');

        if (timeSlotSelect) {
            timeSlotSelect.addEventListener('change', updateCourtPricing);
        }

        if (durationSelect) {
            durationSelect.addEventListener('change', updateCourtPricing);
        }

        if (newPaddleRentQuantityInput) {
            newPaddleRentQuantityInput.addEventListener('input', updateCourtPricing);
        }

        if (paddleRentQuantityInput) {
            paddleRentQuantityInput.addEventListener('input', updateCourtPricing);
        }

        if (ballQuantityInput) {
            ballQuantityInput.addEventListener('input', updateCourtPricing);
        }
    });
</script>

</body>
</html>
