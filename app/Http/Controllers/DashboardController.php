<?php

namespace App\Http\Controllers;

use App\Models\FacilitySetting;
use App\Models\Reservation;
use App\Models\User;
use App\Support\ReservationManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly ReservationManager $reservationManager,
    ) {
    }

    public function __invoke(Request $request): View
    {
        Reservation::expireStalePendingPayMongoReservations();

        $user = $request->user();

        if ($user->isAdmin()) {
            $selectedDate = $request->get('date', now()->toDateString());
            $customerSearch = trim($request->string('customer')->toString());
            $courtCount = $this->reservationManager->courtCount();
            $courts = $this->reservationManager->courts();
            $timeSlots = $this->reservationManager->timeSlots();
            $rescheduleFeatureReady = Reservation::rescheduleColumnsReady();
            $contactNumberReady = Reservation::contactNumberColumnReady();
            $rateSettingsReady = FacilitySetting::rateColumnsReady();
            [$reportStart, $reportEnd] = $this->resolveReportRange($request);

            $monitorReservations = Reservation::with('user')
                ->whereDate('booking_date', $selectedDate)
                ->when($customerSearch !== '', function ($query) use ($customerSearch) {
                    $query->where('customer_name', 'like', '%' . $customerSearch . '%');
                })
                ->orderBy('time_slot')
                ->orderBy('court_number')
                ->get();

            $rangeReservations = Reservation::with('user')
                ->whereBetween('booking_date', [
                    $reportStart->toDateString(),
                    $reportEnd->toDateString(),
                ])
                ->orderBy('booking_date')
                ->orderBy('time_slot')
                ->orderBy('court_number')
                ->get();

            $dailyReports = $rangeReservations
                ->groupBy(fn (Reservation $reservation) => $reservation->booking_date->toDateString())
                ->map(function ($reservations, string $date) {
                    return [
                        'date' => Carbon::parse($date),
                        'booking_count' => $reservations->count(),
                        'paid_income' => (float) $reservations->where('payment_status', 'Paid')->sum('amount'),
                        'walk_in_count' => $reservations->whereNull('user_id')->count(),
                        'customer_names' => $reservations->pluck('customer_name')->unique()->values(),
                    ];
                })
                ->values();

            $analyticsSeries = collect();
            $reservationsByDay = $rangeReservations
                ->groupBy(fn (Reservation $reservation) => $reservation->booking_date->toDateString());
            $rangeDays = max(1, $reportStart->diffInDays($reportEnd) + 1);

            for ($cursor = $reportStart->copy(); $cursor->lte($reportEnd); $cursor->addDay()) {
                $dateKey = $cursor->toDateString();
                $dayReservations = $reservationsByDay->get($dateKey, collect());

                $analyticsSeries->push([
                    'date' => $cursor->copy(),
                    'booking_count' => $dayReservations->count(),
                    'paid_income' => (float) $dayReservations->where('payment_status', 'Paid')->sum('amount'),
                    'walk_in_count' => $dayReservations->whereNull('user_id')->count(),
                    'member_booking_count' => $dayReservations->whereNotNull('user_id')->count(),
                ]);
            }

            $analyticsPeakBookingDay = $analyticsSeries
                ->sortByDesc('booking_count')
                ->first();
            $analyticsPeakIncomeDay = $analyticsSeries
                ->sortByDesc('paid_income')
                ->first();
            $analyticsTopCourts = $rangeReservations
                ->groupBy(fn (Reservation $reservation) => $this->reservationManager->courtLabel(
                    $reservation->court_number,
                    $reservation->court_name,
                ))
                ->map(function ($reservations, string $courtLabel) {
                    return [
                        'label' => $courtLabel,
                        'booking_count' => $reservations->count(),
                        'paid_income' => (float) $reservations->where('payment_status', 'Paid')->sum('amount'),
                    ];
                })
                ->sortByDesc('booking_count')
                ->take(4)
                ->values();
            $visitorBuckets = $rangeReservations
                ->filter(function (Reservation $reservation) {
                    if ($reservation->user_id !== null) {
                        return true;
                    }

                    return trim((string) $reservation->customer_name) !== '';
                })
                ->groupBy(function (Reservation $reservation) {
                    if ($reservation->user_id !== null) {
                        return 'user:' . $reservation->user_id;
                    }

                    return 'guest:' . mb_strtolower(trim((string) $reservation->customer_name));
                });
            $repeatCustomerCount = (int) $visitorBuckets
                ->filter(fn ($reservations) => $reservations->count() > 1)
                ->count();
            $repeatBookingCount = (int) $visitorBuckets
                ->sum(fn ($reservations) => max(0, $reservations->count() - 1));

            return view('dashboard', [
                'isAdmin' => true,
                'selectedDate' => $selectedDate,
                'dashboardRoute' => route('admin.dashboard'),
                'courtCount' => $courtCount,
                'courts' => $courts,
                'rateBoundaryOptions' => $this->reservationManager->rateBoundaryOptions(),
                'customerSearch' => $customerSearch,
                'rescheduleFeatureReady' => $rescheduleFeatureReady,
                'contactNumberReady' => $contactNumberReady,
                'rateSettingsReady' => $rateSettingsReady,
                'publicCustomerNamesSettingReady' => FacilitySetting::publicCustomerNamesColumnReady(),
                'showPublicCustomerNames' => FacilitySetting::publicCustomerNamesVisible(),
                'minCourtCountAllowed' => max(1, $this->reservationManager->maxReservedCourtForActiveReservations()),
                'timeSlots' => $timeSlots,
                'reservationRate' => $this->reservationManager->reservationFee(),
                'oldPaddleRentRate' => $this->reservationManager->oldPaddleRentRate(),
                'newPaddleRentRate' => $this->reservationManager->newPaddleRentRate(),
                'ballRate' => $this->reservationManager->ballRate(),
                'reportStartDate' => $reportStart->toDateString(),
                'reportEndDate' => $reportEnd->toDateString(),
                'reportRangeLabel' => $this->reportRangeLabel($reportStart, $reportEnd),
                'dailyReports' => $dailyReports,
                'analyticsSeries' => $analyticsSeries,
                'analyticsPeakBookingDay' => $analyticsPeakBookingDay,
                'analyticsPeakIncomeDay' => $analyticsPeakIncomeDay,
                'analyticsTopCourts' => $analyticsTopCourts,
                'analyticsSummary' => [
                    'range_days' => $rangeDays,
                    'average_bookings_per_day' => round($rangeReservations->count() / $rangeDays, 1),
                    'member_bookings' => $rangeReservations->whereNotNull('user_id')->count(),
                    'walk_in_bookings' => $rangeReservations->whereNull('user_id')->count(),
                    'paid_bookings' => $rangeReservations->where('payment_status', 'Paid')->count(),
                    'pending_or_unpaid_bookings' => $rangeReservations->where('payment_status', '!=', 'Paid')->count(),
                    'max_daily_bookings' => (int) $analyticsSeries->max('booking_count'),
                ],
                'quickRanges' => $this->quickRanges($selectedDate),
                'reservations' => $monitorReservations,
                'stats' => [
                    'registered_users' => User::count(),
                    'active_courts' => $courtCount,
                    'range_unique_visitors' => $visitorBuckets->count(),
                    'range_bookings' => $rangeReservations->count(),
                    'range_repeat_bookings' => $repeatBookingCount,
                    'range_repeat_customers' => $repeatCustomerCount,
                    'range_paid_income' => (float) $rangeReservations->where('payment_status', 'Paid')->sum('amount'),
                    'range_walk_in_reservations' => $rangeReservations->whereNull('user_id')->count(),
                    'report_days' => $dailyReports->count(),
                ],
            ]);
        }

        $today = now()->toDateString();
        $userReservations = $user->reservations()
            ->orderByDesc('booking_date')
            ->orderBy('time_slot')
            ->get();

        return view('dashboard', [
            'isAdmin' => false,
            'selectedDate' => $today,
            'dashboardRoute' => route('dashboard'),
            'courtCount' => $this->reservationManager->courtCount(),
            'courts' => $this->reservationManager->courts(),
            'rateBoundaryOptions' => $this->reservationManager->rateBoundaryOptions(),
            'customerSearch' => '',
            'timeSlots' => $this->reservationManager->timeSlots(),
            'customerBookingWindowDays' => $this->reservationManager->customerBookingWindowInDays(),
            'rescheduleFeatureReady' => Reservation::rescheduleColumnsReady(),
            'contactNumberReady' => Reservation::contactNumberColumnReady(),
            'reservations' => $userReservations,
            'reschedulableReservations' => $userReservations
                ->filter(fn (Reservation $reservation) => $reservation->isRescheduleUnlocked())
                ->values(),
            'stats' => [
                'my_reservations' => $userReservations->count(),
                'upcoming_reservations' => $userReservations
                    ->filter(fn (Reservation $reservation) => $reservation->booking_date->toDateString() >= $today)
                    ->count(),
                'paid_reservations' => $userReservations->where('payment_status', 'Paid')->count(),
                'pending_payments' => $userReservations->where('payment_status', '!=', 'Paid')->count(),
            ],
        ]);
    }

    public function admin(Request $request): View
    {
        abort_unless($request->user()?->isAdmin(), 403);

        return $this->__invoke($request);
    }

    public function updateCourtCount(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        if (! FacilitySetting::tableExists()) {
            return back()->with('error', 'Court settings table is not ready yet. Run php artisan migrate first.');
        }

        if (! FacilitySetting::courtDetailsReady()) {
            return back()->with('error', 'Court profile fields are not ready yet. Run php artisan migrate first.');
        }

        $validated = $request->validate([
            'court_count' => 'required|integer|min:1|max:50',
            'court_names' => 'sometimes|array',
            'court_names.*' => 'nullable|string|max:255',
            'court_day_rates' => 'sometimes|array',
            'court_day_rates.*' => 'nullable|integer|min:0|max:100000',
            'court_day_starts' => 'sometimes|array',
            'court_day_starts.*' => 'nullable|string|in:' . implode(',', $this->reservationManager->rateBoundaryOptions()),
            'court_day_ends' => 'sometimes|array',
            'court_day_ends.*' => 'nullable|string|in:' . implode(',', $this->reservationManager->rateBoundaryOptions()),
            'court_night_rates' => 'sometimes|array',
            'court_night_rates.*' => 'nullable|integer|min:0|max:100000',
            'court_night_starts' => 'sometimes|array',
            'court_night_starts.*' => 'nullable|string|in:' . implode(',', $this->reservationManager->rateBoundaryOptions()),
            'court_night_ends' => 'sometimes|array',
            'court_night_ends.*' => 'nullable|string|in:' . implode(',', $this->reservationManager->rateBoundaryOptions()),
        ]);

        $minCourtCountAllowed = max(1, $this->reservationManager->maxReservedCourtForActiveReservations());

        if ($validated['court_count'] < $minCourtCountAllowed) {
            return back()->withInput()->with('error', "Dili pwede moubos sa {$minCourtCountAllowed} courts kay naa pay active reservations ana nga court number.");
        }

        $defaultRate = $this->reservationManager->reservationFee();
        $submittedNames = $validated['court_names'] ?? [];
        $submittedDayRates = $validated['court_day_rates'] ?? [];
        $submittedDayStarts = $validated['court_day_starts'] ?? [];
        $submittedDayEnds = $validated['court_day_ends'] ?? [];
        $submittedNightRates = $validated['court_night_rates'] ?? [];
        $submittedNightStarts = $validated['court_night_starts'] ?? [];
        $submittedNightEnds = $validated['court_night_ends'] ?? [];
        $courtDetails = [];

        for ($courtNumber = 1; $courtNumber <= $validated['court_count']; $courtNumber++) {
            $dayRate = max(0, (int) ($submittedDayRates[$courtNumber] ?? $defaultRate));

            $courtDetails[$courtNumber] = [
                'name' => trim((string) ($submittedNames[$courtNumber] ?? '')) ?: FacilitySetting::defaultCourtName($courtNumber),
                'rate' => $dayRate,
                'day_rate' => $dayRate,
                'day_starts_at' => $submittedDayStarts[$courtNumber] ?? $this->reservationManager->defaultDayStartTime(),
                'day_ends_at' => $submittedDayEnds[$courtNumber] ?? $this->reservationManager->defaultDayEndTime(),
                'night_rate' => max(0, (int) ($submittedNightRates[$courtNumber] ?? $dayRate)),
                'night_starts_at' => $submittedNightStarts[$courtNumber] ?? $this->reservationManager->defaultNightStartTime(),
                'night_ends_at' => $submittedNightEnds[$courtNumber] ?? $this->reservationManager->defaultNightEndTime(),
            ];
        }

        FacilitySetting::current()->update([
            'court_count' => $validated['court_count'],
            'court_details' => $courtDetails,
        ]);

        return redirect(route('admin.dashboard', ['panel' => 'rates'], absolute: false))
            ->with('success', 'Court setup updated successfully.');
    }

    public function updateRates(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        if (! FacilitySetting::tableExists() || ! FacilitySetting::rateColumnsReady()) {
            return back()->with('error', 'Rate settings are not ready yet. Run php artisan migrate first.');
        }

        $validated = $request->validate([
            'paddle_rent_rate' => 'required|integer|min:0|max:100000',
            'new_paddle_rent_rate' => 'required|integer|min:0|max:100000',
            'ball_rate' => 'required|integer|min:0|max:100000',
        ]);

        FacilitySetting::current()->update($validated);

        return redirect(route('admin.dashboard', ['panel' => 'rates'], absolute: false))
            ->with('success', 'Rates updated successfully.');
    }

    public function updatePublicReservationVisibility(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        if (! FacilitySetting::tableExists() || ! FacilitySetting::publicCustomerNamesColumnReady()) {
            return back()->with('error', 'Public reservation visibility setting is not ready yet. Run php artisan migrate first.');
        }

        $request->validate([
            'show_public_customer_names' => 'nullable|boolean',
        ]);

        FacilitySetting::current()->update([
            'show_public_customer_names' => $request->boolean('show_public_customer_names'),
        ]);

        return redirect(route('admin.dashboard', ['panel' => 'rates'], absolute: false))
            ->with('success', 'Public reservation name visibility updated successfully.');
    }

    public function storeWalkIn(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $timeSlots = $this->reservationManager->timeSlots();

        if (! Reservation::contactNumberColumnReady()) {
            return back()->withInput()->with('error', 'Contact number field is not ready yet. Run php artisan migrate first.');
        }

        if (! Reservation::rentalColumnsReady()) {
            return back()->withInput()->with('error', 'Rental quantity fields are not ready yet. Run php artisan migrate first.');
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:30',
            'booking_date' => 'required|date',
            'time_slot' => 'required|string|in:' . implode(',', $timeSlots),
            'court_number' => 'required|integer|min:1|max:' . $this->reservationManager->courtCount(),
            'players' => 'required|integer|min:1|max:8',
            'paddle_rent_quantity' => 'nullable|integer|min:0|max:20',
            'new_paddle_rent_quantity' => 'nullable|integer|min:0|max:20',
            'ball_quantity' => 'nullable|integer|min:0|max:20',
            'payment_method' => 'required|string|in:gcash,cash',
            'payment_status' => 'required|string|in:Paid,Unpaid',
            'payment_reference' => 'nullable|string|max:255',
        ]);

        if ($validated['payment_method'] === 'gcash' && empty($validated['payment_reference'])) {
            return back()->withInput()->with('error', 'Walk-in GCash reservations need a reference number.');
        }

        if (! $this->reservationManager->isCourtAvailable(
            $validated['booking_date'],
            $validated['time_slot'],
            $validated['court_number'],
        )) {
            return back()->withInput()->with('error', 'Kana nga court booked na sa selected date ug time. Pili ug lain nga court.');
        }

        $reservation = $this->reservationManager->createReservation([
            'user_id' => null,
            'customer_name' => $validated['customer_name'],
            'contact_number' => $validated['contact_number'],
            'booking_date' => $validated['booking_date'],
            'time_slot' => $validated['time_slot'],
            'court_number' => $validated['court_number'],
            'players' => $validated['players'],
            'paddle_rent_quantity' => $validated['paddle_rent_quantity'] ?? 0,
            'new_paddle_rent_quantity' => $validated['new_paddle_rent_quantity'] ?? 0,
            'ball_quantity' => $validated['ball_quantity'] ?? 0,
            'payment_method' => $validated['payment_method'],
            'payment_reference' => $validated['payment_method'] === 'gcash'
                ? $validated['payment_reference']
                : null,
            'payment_status' => $validated['payment_status'],
        ]);

        return redirect()->route('reservations.receipt', $reservation)
            ->with('success', 'Walk-in reservation created successfully.');
    }

    public function unlockReschedule(Request $request, Reservation $reservation): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        if (! Reservation::rescheduleColumnsReady()) {
            return back()->with('error', 'Rain reschedule fields are not ready yet. Run php artisan migrate first.');
        }

        if ($reservation->user_id === null) {
            return back()->with('error', 'Walk-in reservations cannot use customer reschedule unlock.');
        }

        $deadline = $reservation->booking_date->copy()
            ->addDays($this->reservationManager->customerBookingWindowInDays());

        $reservation->update([
            'reschedule_unlocked_at' => now(),
            'reschedule_deadline' => $deadline,
            'reschedule_reason' => 'Rain / uncovered court',
        ]);

        return back()->with('success', "Reschedule unlocked for {$reservation->customer_name} until {$deadline->format('M d, Y')}.");
    }

    public function lockReschedule(Request $request, Reservation $reservation): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        if (! Reservation::rescheduleColumnsReady()) {
            return back()->with('error', 'Rain reschedule fields are not ready yet. Run php artisan migrate first.');
        }

        if ($reservation->user_id === null) {
            return back()->with('error', 'Walk-in reservations cannot use customer reschedule lock.');
        }

        $reservation->update([
            'reschedule_unlocked_at' => null,
            'reschedule_deadline' => null,
            'reschedule_reason' => null,
        ]);

        return back()->with('success', "Reschedule locked for {$reservation->customer_name}.");
    }

    private function resolveReportRange(Request $request): array
    {
        $defaultEnd = now()->startOfDay();
        $defaultStart = now()->subDays(6)->startOfDay();

        $reportStart = $request->filled('start_date')
            ? $this->parseDateOrDefault($request->string('start_date')->toString(), $defaultStart)
            : $defaultStart;

        $reportEnd = $request->filled('end_date')
            ? $this->parseDateOrDefault($request->string('end_date')->toString(), $defaultEnd)
            : $defaultEnd;

        if ($reportStart->gt($reportEnd)) {
            [$reportStart, $reportEnd] = [$reportEnd, $reportStart];
        }

        return [$reportStart, $reportEnd];
    }

    private function parseDateOrDefault(string $value, Carbon $fallback): Carbon
    {
        try {
            return Carbon::parse($value)->startOfDay();
        } catch (\Throwable) {
            return $fallback->copy();
        }
    }

    private function reportRangeLabel(Carbon $start, Carbon $end): string
    {
        if ($start->isSameDay($end)) {
            return $start->format('F d, Y');
        }

        return $start->format('M d, Y') . ' - ' . $end->format('M d, Y');
    }

    private function quickRanges(string $selectedDate): array
    {
        $today = now()->toDateString();
        $lastWeekStart = now()->subWeek()->startOfWeek()->toDateString();
        $lastWeekEnd = now()->subWeek()->endOfWeek()->toDateString();

        return [
            [
                'label' => 'Today',
                'date' => $selectedDate,
                'start_date' => $today,
                'end_date' => $today,
            ],
            [
                'label' => 'Last Week',
                'date' => $selectedDate,
                'start_date' => $lastWeekStart,
                'end_date' => $lastWeekEnd,
            ],
            [
                'label' => 'This Month',
                'date' => $selectedDate,
                'start_date' => now()->startOfMonth()->toDateString(),
                'end_date' => $today,
            ],
        ];
    }
}
