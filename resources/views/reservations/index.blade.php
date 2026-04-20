<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pickle BALLan ni Juan</title>
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
            padding: 24px;
        }

        .container {
            max-width: 1180px;
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
            display: flex;
            justify-content: space-between;
            gap: 20px;
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .brand-lockup {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .brand-mark {
            width: 92px;
            height: 92px;
            flex-shrink: 0;
        }

        .hero-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
        }

        .grid {
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(0, 0.95fr);
            gap: 24px;
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

        @media (max-width: 920px) {
            body {
                padding: 16px;
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

            .date-filter {
                width: 100%;
            }
        }
    </style>
</head>
@php
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
    $selectedCourtDayRate = (int) data_get($courtLookup->get($selectedCourtNumber), 'day_rate', 0);
    $selectedCourtAvailability = collect($courtAvailability)->firstWhere('court_number', $selectedCourtNumber);
    $selectedCourtSlot = collect(data_get($selectedCourtAvailability, 'slots', []))->firstWhere('slot', $selectedTimeSlot);
    $selectedOldPaddleRentQuantity = max(0, (int) old('paddle_rent_quantity', 0));
    $selectedNewPaddleRentQuantity = max(0, (int) old('new_paddle_rent_quantity', 0));
    $selectedBallQuantity = max(0, (int) old('ball_quantity', 0));
    $selectedCourtRate = $selectedTimeSlot !== ''
        ? (int) data_get($selectedCourtSlot, 'rate', $selectedCourtDayRate)
        : 0;
    $selectedRentalTotal = ($selectedOldPaddleRentQuantity * $oldPaddleRentRate) + ($selectedNewPaddleRentQuantity * $newPaddleRentRate) + ($selectedBallQuantity * $ballRate);
    $selectedBookingTotal = $selectedCourtRate > 0
        ? $selectedCourtRate + $selectedRentalTotal
        : 0;
@endphp
<body>
<div class="container">
    <div class="card">
        <div class="hero">
            <div class="brand-lockup">
                <x-application-logo class="brand-mark" />
                <div>
                    <h1 style="margin: 0;">Pickle BALLan ni Juan</h1>
                    <p class="muted" style="margin: 8px 0 0;">
                    Court reservations with member accounts, payment tracking, and an admin dashboard.
                    </p>
                    <p class="muted" style="margin: 8px 0 0;">
                        Follow us on Facebook for updates and announcements.
                    </p>
                </div>
            </div>

            <div class="hero-actions">
                <form method="GET" action="{{ route('reservations.index') }}" class="date-filter">
                    <label for="date" class="muted">View Date</label>
                    <input id="date" class="input" type="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()">
                </form>

                <a
                    href="{{ config('services.facebook.page_url') }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="button button-facebook"
                >
                    <svg style="width: 18px; height: 18px; margin-right: 8px;" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M13.5 22v-8.2h2.8l.4-3.2h-3.2V8.55c0-.93.27-1.55 1.6-1.55h1.7V4.1c-.3-.04-1.33-.1-2.52-.1-2.5 0-4.2 1.5-4.2 4.3v2.3H8v3.2h2.6V22h2.9Z"/>
                    </svg>
                    Visit Facebook
                </a>

                @auth
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard') }}" class="button-secondary">{{ auth()->user()->isAdmin() ? 'Open Admin Dashboard' : 'Open My Dashboard' }}</a>
                @else
                    <a href="{{ route('login') }}" class="button-secondary">Login</a>
                    <a href="{{ route('register') }}" class="button">Register</a>
                @endauth
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="error">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="error">
            <ul class="error-list">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid">
        <div>
            @auth
            <div class="card">
                <h2 class="panel-title">Reserve a Slot</h2>

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

                    <div class="notice-box">
                        <strong>No cancellation once paid or reserved.</strong><br><br>
                        <span class="muted" style="color: #9a3412;">
                            If mag-ulan and dili magamit ang court because walay atop, admin ra ang maka-unlock sa imong reschedule.
                            Once unlocked, you can choose a new court, date, and time within {{ $customerBookingWindowDays }} days from the original booking date.
                        </span>
                    </div>

                    <div class="notice-box">
                        <strong>Online payment is GCash only.</strong><br><br>
                        <span class="muted" style="color: #9a3412;">
                            If you want to pay walk-in or cash, please go directly to the office. Walk-in payments are handled by admin only.
                        </span>
                    </div>

                    <form method="POST" action="{{ route('reservations.store') }}">
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
                            Selected court: <strong id="selectedCourtLabel">{{ $selectedCourtLabel }}</strong> | Court rate:
                            <strong id="selectedCourtRate">{{ $selectedCourtRate > 0 ? 'PHP ' . number_format($selectedCourtRate, 2) : 'Choose time slot first' }}</strong>
                        </p>

                        <label for="time_slot">Time Slot</label>
                        <select id="time_slot" class="select" name="time_slot" required>
                            <option value="">Select time slot</option>
                            @foreach($timeSlots as $slot)
                                <option value="{{ $slot }}" @selected(old('time_slot') == $slot)>
                                    {{ $slot }}
                                </option>
                            @endforeach
                        </select>

                        <label for="players">Players</label>
                        <select id="players" class="select" name="players" required>
                            <option value="2" @selected(old('players') == '2')>2 Players</option>
                            <option value="4" @selected(old('players', '4') == '4')>4 Players</option>
                            <option value="6" @selected(old('players') == '6')>6 Players</option>
                            <option value="8" @selected(old('players') == '8')>8 Players</option>
                        </select>

                        <div style="display: grid; gap: 14px; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
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

                        <p class="muted" style="margin-top: 12px; margin-bottom: 14px;">
                            Estimated total: <strong id="selectedBookingTotal">{{ $selectedBookingTotal > 0 ? 'PHP ' . number_format($selectedBookingTotal, 2) : 'Choose time slot first' }}</strong>
                        </p>

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

                        <input type="hidden" name="payment_method" value="gcash">

                        <label>Payment Method</label>
                        <div class="account-box" style="margin-top: 6px;">
                            <strong>GCash</strong><br>
                            <span class="muted">Cash / walk-in payment is not available for online booking.</span>
                        </div>

                        <div id="gcashBox">
                            <p class="muted">
                                GCash Number: <strong>0917-123-4567</strong><br>
                                Account Name: <strong>Pickle BALLan ni Juan</strong><br>
                                Court Rate: <strong id="gcashCourtRate">{{ $selectedCourtRate > 0 ? 'PHP ' . number_format($selectedCourtRate, 2) : 'Choose time slot first' }}</strong><br>
                                New Paddle Rent: <strong id="gcashNewPaddleSummary">{{ $selectedNewPaddleRentQuantity > 0 ? $selectedNewPaddleRentQuantity . ' x PHP ' . number_format($newPaddleRentRate, 2) . ' = PHP ' . number_format($selectedNewPaddleRentQuantity * $newPaddleRentRate, 2) : 'None' }}</strong><br>
                                Old Paddle Rent: <strong id="gcashOldPaddleSummary">{{ $selectedOldPaddleRentQuantity > 0 ? $selectedOldPaddleRentQuantity . ' x PHP ' . number_format($oldPaddleRentRate, 2) . ' = PHP ' . number_format($selectedOldPaddleRentQuantity * $oldPaddleRentRate, 2) : 'None' }}</strong><br>
                                Ball Rent: <strong id="gcashBallSummary">{{ $selectedBallQuantity > 0 ? $selectedBallQuantity . ' x PHP ' . number_format($ballRate, 2) . ' = PHP ' . number_format($selectedBallQuantity * $ballRate, 2) : 'None' }}</strong><br>
                                Total Due: <strong id="gcashTotalAmount">{{ $selectedBookingTotal > 0 ? 'PHP ' . number_format($selectedBookingTotal, 2) : 'Choose time slot first' }}</strong>
                            </p>

                            <label for="payment_reference">GCash Reference Number</label>
                            <input id="payment_reference" class="input" type="text" name="payment_reference" value="{{ old('payment_reference') }}" placeholder="Enter GCash reference number">
                        </div>

                        <button class="button" type="submit">Pay and Confirm Reservation</button>
                    </form>
            </div>
            @endauth

            <div class="card">
                <h2 class="panel-title">Availability for {{ \Illuminate\Support\Carbon::parse($selectedDate)->format('F d, Y') }}</h2>
                <p class="muted" style="margin-bottom: 16px;">
                    Click a court to see which time slots are still available.
                </p>

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

        <div>
            <div class="card">
                <h2 class="panel-title">Reservations for {{ \Illuminate\Support\Carbon::parse($selectedDate)->format('F d, Y') }}</h2>

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

<script>
    function toggleReference() {
        const gcashBox = document.getElementById('gcashBox');

        if (!gcashBox) {
            return;
        }

        gcashBox.style.display = 'block';
    }

    function updateCourtPricing() {
        const courtSelect = document.getElementById('court_number');
        const timeSlotSelect = document.getElementById('time_slot');
        const newPaddleRentQuantityInput = document.getElementById('new_paddle_rent_quantity');
        const paddleRentQuantityInput = document.getElementById('paddle_rent_quantity');
        const ballQuantityInput = document.getElementById('ball_quantity');
        const selectedCourtLabel = document.getElementById('selectedCourtLabel');
        const selectedCourtRate = document.getElementById('selectedCourtRate');
        const selectedBookingTotal = document.getElementById('selectedBookingTotal');
        const gcashCourtRate = document.getElementById('gcashCourtRate');
        const gcashNewPaddleSummary = document.getElementById('gcashNewPaddleSummary');
        const gcashOldPaddleSummary = document.getElementById('gcashOldPaddleSummary');
        const gcashBallSummary = document.getElementById('gcashBallSummary');
        const gcashTotalAmount = document.getElementById('gcashTotalAmount');

        if (!courtSelect || !timeSlotSelect || !newPaddleRentQuantityInput || !paddleRentQuantityInput || !ballQuantityInput || !selectedCourtLabel || !selectedCourtRate || !selectedBookingTotal || !gcashCourtRate || !gcashNewPaddleSummary || !gcashOldPaddleSummary || !gcashBallSummary || !gcashTotalAmount) {
            return;
        }

        const oldPaddleRentRate = @json($oldPaddleRentRate);
        const newPaddleRentRate = @json($newPaddleRentRate);
        const ballRate = @json($ballRate);

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
        const normalizeQuantity = (value) => {
            const parsed = Number(value);

            if (!Number.isFinite(parsed) || parsed <= 0) {
                return 0;
            }

            return Math.floor(parsed);
        };

        const selectedOption = courtSelect.options[courtSelect.selectedIndex];
        const courtLabel = selectedOption?.dataset?.label || 'Select court';
        const dayRate = Number(selectedOption?.dataset?.dayRate || 0);
        const dayStart = selectedOption?.dataset?.dayStart || '{{ $defaultDayStartTime }}';
        const dayEnd = selectedOption?.dataset?.dayEnd || '{{ $defaultDayEndTime }}';
        const nightRate = Number(selectedOption?.dataset?.nightRate || dayRate);
        const nightStart = selectedOption?.dataset?.nightStart || '{{ $defaultNightStartTime }}';
        const nightEnd = selectedOption?.dataset?.nightEnd || '{{ $defaultNightEndTime }}';
        const selectedTimeSlot = timeSlotSelect.value;
        const newPaddleRentQuantity = normalizeQuantity(newPaddleRentQuantityInput.value);
        const oldPaddleRentQuantity = normalizeQuantity(paddleRentQuantityInput.value);
        const ballQuantity = normalizeQuantity(ballQuantityInput.value);
        const newPaddleSubtotal = newPaddleRentQuantity * newPaddleRentRate;
        const oldPaddleSubtotal = oldPaddleRentQuantity * oldPaddleRentRate;
        const ballSubtotal = ballQuantity * ballRate;
        let applicableRate = 0;

        if (selectedTimeSlot) {
            if (fallsWithinRange(selectedTimeSlot, dayStart, dayEnd)) {
                applicableRate = dayRate;
            } else if (fallsWithinRange(selectedTimeSlot, nightStart, nightEnd)) {
                applicableRate = nightRate;
            } else {
                applicableRate = dayRate;
            }
        }

        const formattedRate = applicableRate > 0 ? `PHP ${applicableRate.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        })}` : 'Choose time slot first';
        const formattedTotal = applicableRate > 0
            ? formatPhp(applicableRate + newPaddleSubtotal + oldPaddleSubtotal + ballSubtotal)
            : 'Choose time slot first';

        selectedCourtLabel.textContent = courtLabel;
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
    }

    document.addEventListener('DOMContentLoaded', () => {
        toggleReference();
        updateCourtPricing();

        const courtSelect = document.getElementById('court_number');

        if (courtSelect) {
            courtSelect.addEventListener('change', updateCourtPricing);
        }

        const timeSlotSelect = document.getElementById('time_slot');
        const newPaddleRentQuantityInput = document.getElementById('new_paddle_rent_quantity');
        const paddleRentQuantityInput = document.getElementById('paddle_rent_quantity');
        const ballQuantityInput = document.getElementById('ball_quantity');

        if (timeSlotSelect) {
            timeSlotSelect.addEventListener('change', updateCourtPricing);
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
