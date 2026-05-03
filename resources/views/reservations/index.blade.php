<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.soft-ui-head', ['title' => 'Book a Court - ' . config('app.name', 'Pickle BALLan Ni Juan')])
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
    $selectedDateHeadline = \Illuminate\Support\Carbon::parse($selectedDate)->format('F d, Y');
    $totalCourtSlots = collect($courtAvailability)->sum(fn (array $court) => count($court['slots']));
    $totalAvailableSlots = collect($courtAvailability)->sum(fn (array $court) => (int) $court['available_slots']);
    $totalBookedSlots = collect($courtAvailability)->sum(fn (array $court) => (int) $court['booked_slots']);
    $publicReservationsCount = $bookings->count();
    $bookingAuthRedirect = 'booking';
    $loginUrl = route('login', ['redirect_to' => $bookingAuthRedirect]);
    $registerUrl = route('register', ['redirect_to' => $bookingAuthRedirect]);
    $dashboardUrl = auth()->check()
        ? (auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard'))
        : null;
    $dashboardLabel = auth()->check()
        ? (auth()->user()->isAdmin() ? 'Admin Dashboard' : 'My Dashboard')
        : null;
@endphp
<body class="soft-public-page">
    <nav class="navbar navbar-expand-lg blur blur-rounded top-0 z-index-3 shadow my-3 py-2 mx-4">
        <div class="container-fluid">
            <a class="navbar-brand font-weight-bolder d-flex align-items-center" href="{{ route('reservations.index') }}">
                <span class="soft-brand-mark soft-brand-mark-xs me-2">PB</span>
                Pickle BALLan Ni Juan
            </a>

            <button class="navbar-toggler shadow-none ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#publicNavigation" aria-controls="publicNavigation" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon mt-2">
                    <span class="navbar-toggler-bar bar1"></span>
                    <span class="navbar-toggler-bar bar2"></span>
                    <span class="navbar-toggler-bar bar3"></span>
                </span>
            </button>

            <div class="collapse navbar-collapse" id="publicNavigation">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link me-2" href="{{ config('services.facebook.page_url') }}" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-facebook opacity-6 text-dark me-1"></i>
                            Facebook
                        </a>
                    </li>
                    @auth
                        @if(! auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a class="btn btn-sm bg-gradient-info mb-0 me-2" href="#booking-section">Book Now</a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="btn btn-sm btn-outline-primary mb-0" href="{{ $dashboardUrl }}">{{ $dashboardLabel }}</a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="btn btn-sm bg-gradient-info mb-0 me-2" href="{{ $loginUrl }}">Book Now</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link me-2" href="{{ $loginUrl }}">
                                <i class="fas fa-key opacity-6 text-dark me-1"></i>
                                Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-sm btn-outline-primary mb-0" href="{{ $registerUrl }}">Register</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <header class="page-header soft-public-hero mx-3 border-radius-xl" style="background-image: url('{{ asset('soft-ui-dashboard-main/assets/img/curved-images/curved14.jpg') }}');">
        <span class="mask bg-gradient-dark opacity-6"></span>
        <div class="container-fluid px-4 py-6">
            <div class="row align-items-center min-vh-50">
                <div class="col-lg-7">
                    <p class="text-white text-uppercase font-weight-bold text-xs mb-3">Public Booking Dashboard</p>
                    <h1 class="text-white font-weight-bolder mb-3">Pickle BALLan Ni Juan</h1>
                    <p class="text-white opacity-8 lead mb-4">
                        Browse live court availability, review rates, and reserve a slot with your account.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        @auth
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard', ['panel' => 'booking']) }}" class="btn bg-gradient-light mb-0">Open Admin Booking</a>
                            @else
                                <a href="#booking-section" class="btn bg-gradient-light mb-0">Reserve a Slot</a>
                            @endif
                            <a href="{{ $dashboardUrl }}" class="btn btn-outline-white mb-0">{{ $dashboardLabel }}</a>
                        @else
                            <a href="{{ $loginUrl }}" class="btn bg-gradient-light mb-0">Sign In to Book</a>
                            <a href="{{ $registerUrl }}" class="btn btn-outline-white mb-0">Create Account</a>
                        @endauth
                    </div>
                </div>

                <div class="col-lg-4 ms-auto mt-4 mt-lg-0">
                    <div class="card blur shadow-lg">
                        <div class="card-body">
                            <p class="text-xs text-uppercase text-gradient text-info font-weight-bolder mb-2">Viewing Date</p>
                            <h5 class="font-weight-bolder mb-3">{{ $selectedDateHeadline }}</h5>
                            <form method="GET" action="{{ route('reservations.index') }}">
                                <label for="date" class="form-label text-sm">Change date</label>
                                <div class="input-group">
                                    <input id="date" class="form-control" type="date" name="date" value="{{ $selectedDate }}" min="{{ $minBookingDate }}" max="{{ $maxBookingDate }}">
                                    <button class="btn bg-gradient-info mb-0" type="submit">View</button>
                                </div>
                            </form>
                            <p class="text-sm text-secondary mb-0 mt-3">
                                Booking window ends {{ \Illuminate\Support\Carbon::parse($maxBookingDate)->format('F d, Y') }}.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div id="userAgreementOverlay" class="soft-agreement-overlay" role="dialog" aria-modal="true" aria-labelledby="agreementTitle">
        <div class="card soft-agreement-card">
            <div class="card-body p-4 p-lg-5">
                <p class="text-xs text-uppercase text-gradient text-info font-weight-bolder mb-2">User Agreement</p>
                <h3 id="agreementTitle" class="font-weight-bolder">Please review and accept before entering the website.</h3>
                <p class="text-secondary">
                    This booking platform follows a strict reservation policy. By continuing, you confirm that you understand the terms below.
                </p>

                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <div class="card bg-gray-100 h-100">
                            <div class="card-body p-3">
                                <h6>Final Booking Policy</h6>
                                <p class="text-sm text-secondary mb-0">All reservations are final once submitted and confirmed. No cancellation, refund, reversal, or transfer will be allowed.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-gray-100 h-100">
                            <div class="card-body p-3">
                                <h6>Rain and Court Availability</h6>
                                <p class="text-sm text-secondary mb-0">Rescheduling is not automatic. An administrator must unlock the reservation before any replacement slot can be selected.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-gray-100 h-100">
                            <div class="card-body p-3">
                                <h6>Payment Accuracy</h6>
                                <p class="text-sm text-secondary mb-0">You are responsible for choosing the correct date, court, time slot, rentals, and payment details before confirming.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-gray-100 h-100">
                            <div class="card-body p-3">
                                <h6>Reservation Information</h6>
                                <p class="text-sm text-secondary mb-0">Public reservation cards hide customer identity. Admin can still review full booking details inside the dashboard.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning text-white text-sm mt-4">
                    Confirmed reservations are binding and cannot be cancelled after booking.
                </div>

                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mt-4">
                    <div class="form-check">
                        <input id="agreementAccepted" class="form-check-input" type="checkbox">
                        <label class="form-check-label text-sm" for="agreementAccepted">
                            I have read and agree to the reservation policy.
                        </label>
                    </div>
                    <button id="agreementAcceptButton" class="btn bg-gradient-info mb-0" type="button" disabled>I Agree and Enter the Website</button>
                </div>
            </div>
        </div>
    </div>

    <main class="soft-public-main">
        <div class="container-fluid px-4 pb-5">
            @if(session('success'))
                <div class="alert alert-success text-white">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger text-white">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger text-white">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @auth
                @if(auth()->user()->isAdmin())
                    <div class="card bg-gradient-dark mb-4">
                        <div class="card-body d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                            <div>
                                <p class="text-white text-xs text-uppercase font-weight-bolder mb-1">Admin Shortcut</p>
                                <h5 class="text-white mb-1">Need the admin controls?</h5>
                                <p class="text-white opacity-8 mb-0">Open the full dashboard while reviewing the public booking page.</p>
                            </div>
                            <a href="{{ route('admin.dashboard') }}" class="btn bg-gradient-light mb-0">Open Admin Dashboard</a>
                        </div>
                    </div>
                @endif
            @endauth

            <div class="row g-4 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card soft-public-stat">
                        <div class="card-body p-3">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">Open Slots</p>
                            <h5 class="font-weight-bolder mb-0">{{ $totalAvailableSlots }}</h5>
                            <p class="text-xs text-secondary mb-0">Across {{ count($courtAvailability) }} courts.</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card soft-public-stat">
                        <div class="card-body p-3">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">Booked Slots</p>
                            <h5 class="font-weight-bolder mb-0">{{ $totalBookedSlots }}</h5>
                            <p class="text-xs text-secondary mb-0">{{ $publicReservationsCount }} reservation{{ $publicReservationsCount === 1 ? '' : 's' }} today.</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card soft-public-stat">
                        <div class="card-body p-3">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Slots</p>
                            <h5 class="font-weight-bolder mb-0">{{ $totalCourtSlots }}</h5>
                            <p class="text-xs text-secondary mb-0">Hourly court availability.</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card soft-public-stat">
                        <div class="card-body p-3">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">Booking Window</p>
                            <h5 class="font-weight-bolder mb-0">{{ $customerBookingWindowDays }} days</h5>
                            <p class="text-xs text-secondary mb-0">Reserve ahead online.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-xl-8">
                    @if(! auth()->check() || ! auth()->user()->isAdmin())
                        <div class="card mb-4">
                            <div class="card-header pb-0">
                                <h5 class="mb-1">Current Rates</h5>
                                <p class="text-sm text-secondary mb-0">Published court and rental rates for online booking.</p>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-lg-3 col-sm-6">
                                        <div class="card bg-gray-100 h-100">
                                            <div class="card-body p-3">
                                                <p class="text-sm text-secondary mb-1">Court Rate</p>
                                                <h5 class="mb-0">PHP {{ number_format($reservationFee, 2) }}</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <div class="card bg-gray-100 h-100">
                                            <div class="card-body p-3">
                                                <p class="text-sm text-secondary mb-1">Old Paddle</p>
                                                <h5 class="mb-0">PHP {{ number_format($oldPaddleRentRate, 2) }}</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <div class="card bg-gray-100 h-100">
                                            <div class="card-body p-3">
                                                <p class="text-sm text-secondary mb-1">New Paddle</p>
                                                <h5 class="mb-0">PHP {{ number_format($newPaddleRentRate, 2) }}</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <div class="card bg-gray-100 h-100">
                                            <div class="card-body p-3">
                                                <p class="text-sm text-secondary mb-1">Ball</p>
                                                <h5 class="mb-0">PHP {{ number_format($ballRate, 2) }}</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-sm text-secondary mb-0 mt-3">
                                    Court Rate: PHP {{ number_format($reservationFee, 2) }} |
                                    Paddle Rent: PHP {{ number_format($oldPaddleRentRate, 2) }} |
                                    Ball Rate: PHP {{ number_format($ballRate, 2) }}
                                </p>
                            </div>
                        </div>
                    @endif

                    @guest
                        <div class="card mb-4">
                            <div class="card-body p-4 d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                                <div>
                                    <p class="text-xs text-uppercase text-gradient text-info font-weight-bolder mb-2">Members Only</p>
                                    <h5 class="mb-1">Sign in before booking a court.</h5>
                                    <p class="text-sm text-secondary mb-0">You can still browse availability here. Login or register to submit a reservation.</p>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ $loginUrl }}" class="btn bg-gradient-info mb-0">Login</a>
                                    <a href="{{ $registerUrl }}" class="btn btn-outline-primary mb-0">Register</a>
                                </div>
                            </div>
                        </div>
                    @endguest

                    @auth
                        @if(! auth()->user()->isAdmin())
                            <div id="booking-section" class="card mb-4">
                                <div class="card-header pb-0">
                                    <h5 class="mb-1">Reserve a Slot</h5>
                                    <p class="text-sm text-secondary mb-0">Choose a court, start time, duration, rentals, and payment details.</p>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-warning text-white text-sm">
                                        No cancellation once a booking is confirmed. Rain reschedule needs admin unlock.
                                    </div>

                                    @if(! $contactNumberReady)
                                        <div class="alert alert-danger text-white text-sm">
                                            Contact number setup is not ready yet. Admin needs to run the latest migration first.
                                        </div>
                                    @endif

                                    @if(! ($durationReady ?? true))
                                        <div class="alert alert-danger text-white text-sm">
                                            Booking duration setup is not ready yet. Admin needs to run the latest migration first.
                                        </div>
                                    @endif

                                    <div class="card bg-gray-100 mb-4">
                                        <div class="card-body p-3">
                                            <h6 class="mb-1">{{ auth()->user()->name }}</h6>
                                            <p class="text-sm text-secondary mb-0">{{ auth()->user()->email }}</p>
                                            <p class="text-sm text-secondary mb-0 mt-2">Your reservation will be saved under your account name automatically.</p>
                                        </div>
                                    </div>

                                    <form method="POST" action="{{ route('reservations.store') }}">
                                        @csrf

                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="booking_date" class="form-label">Booking Date</label>
                                                <input id="booking_date" class="form-control" type="date" name="booking_date" value="{{ old('booking_date', $selectedDate) }}" min="{{ $minBookingDate }}" max="{{ $maxBookingDate }}" required>
                                                <p class="text-xs text-secondary mt-2 mb-0">Customers can book until {{ \Illuminate\Support\Carbon::parse($maxBookingDate)->format('F d, Y') }}.</p>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="court_number" class="form-label">Select Court</label>
                                                <select id="court_number" class="form-select" name="court_number" required>
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
                                                <p class="text-xs text-secondary mt-2 mb-0">
                                                    Selected court: <strong id="selectedCourtLabel">{{ $selectedCourtLabel }}</strong> |
                                                    Court subtotal: <strong id="selectedCourtRate">{{ $hasSelectedSchedule ? 'PHP ' . number_format($selectedCourtRate, 2) : 'Choose time slot first' }}</strong>
                                                </p>
                                            </div>

                                            <div class="col-md-4">
                                                <label for="time_slot" class="form-label">Start Time</label>
                                                <select id="time_slot" class="form-select" name="time_slot" required>
                                                    <option value="">Select time slot</option>
                                                    @foreach($timeSlots as $slot)
                                                        <option value="{{ $slot }}" @selected(old('time_slot') == $slot)>{{ $slot }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-4">
                                                <label for="duration_hours" class="form-label">How Many Hours</label>
                                                <select id="duration_hours" class="form-select" name="duration_hours" required>
                                                    @foreach($selectedDurationOptions as $durationOption)
                                                        <option value="{{ $durationOption }}" @selected($selectedDurationHours === (int) $durationOption)>
                                                            {{ $durationOption }} hour{{ (int) $durationOption === 1 ? '' : 's' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-4">
                                                <label for="players" class="form-label">Players</label>
                                                <select id="players" class="form-select" name="players" required>
                                                    <option value="2" @selected(old('players') == '2')>2 Players</option>
                                                    <option value="4" @selected(old('players', '4') == '4')>4 Players</option>
                                                    <option value="6" @selected(old('players') == '6')>6 Players</option>
                                                    <option value="8" @selected(old('players') == '8')>8 Players</option>
                                                </select>
                                            </div>

                                            <div class="col-12">
                                                <p class="text-sm text-secondary mb-0">
                                                    Selected schedule: <strong id="selectedScheduleLabel">{{ $selectedScheduleLabel }}</strong>
                                                </p>
                                            </div>

                                            <div class="col-md-4">
                                                <label for="new_paddle_rent_quantity" class="form-label">New Paddle Quantity</label>
                                                <input id="new_paddle_rent_quantity" class="form-control" type="number" name="new_paddle_rent_quantity" min="0" max="20" value="{{ old('new_paddle_rent_quantity', 0) }}">
                                                <p class="text-xs text-secondary mt-2 mb-0">Rate: PHP {{ number_format($newPaddleRentRate, 2) }}</p>
                                            </div>

                                            <div class="col-md-4">
                                                <label for="paddle_rent_quantity" class="form-label">Old Paddle Quantity</label>
                                                <input id="paddle_rent_quantity" class="form-control" type="number" name="paddle_rent_quantity" min="0" max="20" value="{{ old('paddle_rent_quantity', 0) }}">
                                                <p class="text-xs text-secondary mt-2 mb-0">Rate: PHP {{ number_format($oldPaddleRentRate, 2) }}</p>
                                            </div>

                                            <div class="col-md-4">
                                                <label for="ball_quantity" class="form-label">Ball Quantity</label>
                                                <input id="ball_quantity" class="form-control" type="number" name="ball_quantity" min="0" max="20" value="{{ old('ball_quantity', 0) }}">
                                                <p class="text-xs text-secondary mt-2 mb-0">Rate: PHP {{ number_format($ballRate, 2) }}</p>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="card bg-gradient-dark h-100">
                                                    <div class="card-body p-3">
                                                        <p class="text-white text-sm mb-1">Estimated Total</p>
                                                        <h4 class="text-white mb-0" id="selectedBookingTotal">{{ $hasSelectedSchedule ? 'PHP ' . number_format($selectedBookingTotal, 2) : 'Choose time slot first' }}</h4>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="contact_number" class="form-label">Contact Number</label>
                                                <input id="contact_number" class="form-control" type="text" name="contact_number" value="{{ old('contact_number') }}" placeholder="Enter your active mobile number" required>
                                                <p class="text-xs text-secondary mt-2 mb-0">Used for rain reschedule or booking concerns.</p>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="payment_method" class="form-label">Payment Method</label>
                                                <select id="payment_method" class="form-select" name="payment_method" required>
                                                    @if($payMongoCheckoutReady ?? false)
                                                        <option value="paymongo" @selected($selectedPaymentMethod === 'paymongo')>PayMongo Payment Link</option>
                                                    @endif
                                                    <option value="gcash" @selected($selectedPaymentMethod === 'gcash')>Manual GCash Reference</option>
                                                </select>
                                            </div>

                                            <div class="col-12">
                                                <div id="payMongoBox" class="card bg-gray-100" style="display: {{ $selectedPaymentMethod === 'paymongo' ? 'block' : 'none' }};">
                                                    <div class="card-body p-3">
                                                        <h6>PayMongo Payment Link</h6>
                                                        <p class="text-sm text-secondary mb-2">Your reservation will be saved with pending payment, then you will be redirected to PayMongo.</p>
                                                        <p class="text-sm mb-0">Current total: <strong id="paymongoTotalAmount">{{ $hasSelectedSchedule ? 'PHP ' . number_format($selectedBookingTotal, 2) : 'Choose time slot first' }}</strong></p>
                                                    </div>
                                                </div>

                                                <div id="gcashBox" class="card bg-gray-100" style="display: {{ $selectedPaymentMethod === 'gcash' ? 'block' : 'none' }};">
                                                    <div class="card-body p-3">
                                                        <h6>Manual GCash Payment</h6>
                                                        <p class="text-sm text-secondary">
                                                            GCash Number: <strong>0917-123-4567</strong><br>
                                                            Account Name: <strong>Pickle BALLan ni Juan</strong><br>
                                                            Court Subtotal: <strong id="gcashCourtRate">{{ $hasSelectedSchedule ? 'PHP ' . number_format($selectedCourtRate, 2) : 'Choose time slot first' }}</strong><br>
                                                            New Paddle Rent: <strong id="gcashNewPaddleSummary">{{ $selectedNewPaddleRentQuantity > 0 ? $selectedNewPaddleRentQuantity . ' x PHP ' . number_format($newPaddleRentRate, 2) . ' = PHP ' . number_format($selectedNewPaddleRentQuantity * $newPaddleRentRate, 2) : 'None' }}</strong><br>
                                                            Old Paddle Rent: <strong id="gcashOldPaddleSummary">{{ $selectedOldPaddleRentQuantity > 0 ? $selectedOldPaddleRentQuantity . ' x PHP ' . number_format($oldPaddleRentRate, 2) . ' = PHP ' . number_format($selectedOldPaddleRentQuantity * $oldPaddleRentRate, 2) : 'None' }}</strong><br>
                                                            Ball Rent: <strong id="gcashBallSummary">{{ $selectedBallQuantity > 0 ? $selectedBallQuantity . ' x PHP ' . number_format($ballRate, 2) . ' = PHP ' . number_format($selectedBallQuantity * $ballRate, 2) : 'None' }}</strong><br>
                                                            Total Due: <strong id="gcashTotalAmount">{{ $hasSelectedSchedule ? 'PHP ' . number_format($selectedBookingTotal, 2) : 'Choose time slot first' }}</strong>
                                                        </p>

                                                        <label for="payment_reference" class="form-label">GCash Reference Number</label>
                                                        <input id="payment_reference" class="form-control" type="text" name="payment_reference" value="{{ old('payment_reference') }}" placeholder="Enter GCash reference number">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <button id="paymentSubmitButton" class="btn bg-gradient-info w-100 mb-0" type="submit">
                                                    {{ $selectedPaymentMethod === 'paymongo' ? 'Save Booking and Open PayMongo Link' : 'Pay and Confirm Reservation' }}
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif
                    @endauth

                    <div class="card">
                        <div class="card-header pb-0">
                            <h5 class="mb-1">Availability for {{ $selectedDateHeadline }}</h5>
                            <p class="text-sm text-secondary mb-0">Open a court to view hourly slot status and rates.</p>
                        </div>
                        <div class="card-body">
                            <div class="accordion" id="courtAvailabilityAccordion">
                                @foreach($courtAvailability as $court)
                                    <div class="accordion-item mb-3 border-radius-lg border">
                                        <h2 class="accordion-header" id="court-heading-{{ $court['court_number'] }}">
                                            <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#court-panel-{{ $court['court_number'] }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="court-panel-{{ $court['court_number'] }}">
                                                <span class="font-weight-bold me-3">{{ $court['court_label'] }}</span>
                                                @if($court['full'])
                                                    <span class="badge bg-gradient-danger">Full</span>
                                                @else
                                                    <span class="badge bg-gradient-success">{{ $court['available_slots'] }} open</span>
                                                @endif
                                            </button>
                                        </h2>
                                        <div id="court-panel-{{ $court['court_number'] }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="court-heading-{{ $court['court_number'] }}" data-bs-parent="#courtAvailabilityAccordion">
                                            <div class="accordion-body">
                                                <p class="text-xs text-secondary">{{ $court['rate_summary'] }} | {{ $court['booked_slots'] }} booked | {{ count($court['slots']) }} total slots</p>
                                                <div class="soft-slot-grid">
                                                    @foreach($court['slots'] as $slot)
                                                        <div class="soft-slot-chip {{ $slot['available'] ? 'is-open' : 'is-booked' }}">
                                                            <h6 class="mb-1">{{ $slot['slot'] }}</h6>
                                                            <p class="text-xs text-secondary mb-2">{{ $slot['period'] }} | PHP {{ number_format($slot['rate'], 2) }}</p>
                                                            @if($slot['available'])
                                                                <span class="badge bg-gradient-success">Available</span>
                                                            @else
                                                                <span class="badge bg-gradient-danger">Booked</span>
                                                                @if(auth()->check() && auth()->user()->isAdmin() && $slot['reservation'])
                                                                    <p class="text-xs text-secondary mt-2 mb-0">{{ $slot['reservation']->customer_name }}</p>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="card mb-4">
                        <div class="card-header pb-0">
                            <h5 class="mb-1">Daily Snapshot</h5>
                            <p class="text-sm text-secondary mb-0">Live numbers for {{ $selectedDateHeadline }}.</p>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="card bg-gray-100 h-100">
                                        <div class="card-body p-3">
                                            <p class="text-xs text-secondary mb-1">Reservations</p>
                                            <h5 class="mb-0">{{ $publicReservationsCount }}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="card bg-gray-100 h-100">
                                        <div class="card-body p-3">
                                            <p class="text-xs text-secondary mb-1">Courts</p>
                                            <h5 class="mb-0">{{ count($courtAvailability) }}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="card bg-gray-100 h-100">
                                        <div class="card-body p-3">
                                            <p class="text-xs text-secondary mb-1">Open</p>
                                            <h5 class="mb-0">{{ $totalAvailableSlots }}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="card bg-gray-100 h-100">
                                        <div class="card-body p-3">
                                            <p class="text-xs text-secondary mb-1">Booked</p>
                                            <h5 class="mb-0">{{ $totalBookedSlots }}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header pb-0">
                            <h5 class="mb-1">Reservations for {{ $selectedDateHeadline }}</h5>
                            <p class="text-sm text-secondary mb-0">Visible bookings for the selected date.</p>
                        </div>
                        <div class="card-body">
                            @forelse($bookings as $booking)
                                <div class="card bg-gray-100 mb-3">
                                    <div class="card-body p-3">
                                        @php
                                            $viewerIsAdmin = auth()->check() && auth()->user()->isAdmin();
                                            $viewerOwnsBooking = auth()->check() && $booking->user_id === auth()->id();
                                            $canSeeBookingDetails = $viewerIsAdmin || $viewerOwnsBooking;
                                        @endphp

                                        @if($viewerIsAdmin)
                                            <h6 class="mb-1">{{ $booking->customer_name }}</h6>
                                            <p class="text-xs text-secondary mb-2">
                                                {{ $booking->contact_number ?: 'No contact number' }}
                                                @if($booking->user?->email)
                                                    | {{ $booking->user->email }}
                                                @elseif($booking->user_id === null)
                                                    | Walk-in / No account
                                                @endif
                                            </p>
                                        @elseif($viewerOwnsBooking)
                                            <h6 class="mb-1">Your reservation</h6>
                                            <span class="badge bg-gradient-info mb-2">This booking is yours</span>
                                        @else
                                            <h6 class="mb-1">Reserved slot</h6>
                                        @endif

                                        @php
                                            $paddleRentQuantity = max(0, (int) ($booking->paddle_rent_quantity ?? 0));
                                            $newPaddleRentQuantity = max(0, (int) ($booking->new_paddle_rent_quantity ?? 0));
                                            $ballQuantity = max(0, (int) ($booking->ball_quantity ?? 0));
                                        @endphp

                                        <p class="text-sm text-secondary mb-1">{{ $resolveCourtLabel($booking->court_number, $booking->court_name) }}</p>
                                        <p class="text-sm mb-1">{{ $booking->timeRangeLabel() }} | {{ $booking->durationLabel() }}</p>
                                        @if($canSeeBookingDetails)
                                            <p class="text-sm text-secondary mb-1">{{ $booking->players }} players</p>
                                        @endif
                                        @if($canSeeBookingDetails && ($paddleRentQuantity > 0 || $newPaddleRentQuantity > 0 || $ballQuantity > 0))
                                            <p class="text-xs text-secondary mb-1">
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
                                            </p>
                                        @endif
                                        @if($canSeeBookingDetails)
                                            <p class="text-xs text-secondary mb-0">
                                                PHP {{ number_format($booking->amount, 2) }} | {{ strtoupper($booking->payment_method) }} | {{ $booking->payment_status }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-secondary mb-0">Wala pay naka-book ani nga adlaw.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @include('layouts.soft-ui-scripts')

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
