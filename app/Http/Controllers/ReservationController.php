<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Support\ReservationManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function __construct(
        private readonly ReservationManager $reservationManager,
    ) {
    }

    public function index(Request $request): View
    {
        $selectedDate = $request->get('date', now()->toDateString());
        $timeSlots = $this->reservationManager->timeSlots();
        $courts = collect($this->reservationManager->courts());
        $courtCount = $courts->count();

        $bookings = Reservation::with('user')
            ->whereDate('booking_date', $selectedDate)
            ->orderBy('time_slot')
            ->orderBy('court_number')
            ->get();

        $courtAvailability = $courts->map(function (array $court) use ($bookings, $timeSlots) {
            $courtNumber = $court['number'];
            $courtBookings = $bookings
                ->where('court_number', $courtNumber)
                ->keyBy('time_slot');

            $slots = collect($timeSlots)->map(function (string $slot) use ($courtBookings, $courtNumber) {
                $reservation = $courtBookings->get($slot);

                return [
                    'slot' => $slot,
                    'available' => $reservation === null,
                    'reservation' => $reservation,
                    'rate' => $this->reservationManager->courtRateForTimeSlot($courtNumber, $slot),
                    'period' => $this->reservationManager->timePeriodForSlot($courtNumber, $slot),
                ];
            });

            return [
                'court_number' => $courtNumber,
                'court_name' => $court['name'],
                'court_label' => $court['label'],
                'rate' => $court['rate'],
                'day_rate' => $court['day_rate'],
                'day_starts_at' => $court['day_starts_at'],
                'day_ends_at' => $court['day_ends_at'],
                'night_rate' => $court['night_rate'],
                'night_starts_at' => $court['night_starts_at'],
                'night_ends_at' => $court['night_ends_at'],
                'rate_summary' => $court['rate_summary'],
                'available_slots' => $slots->where('available', true)->count(),
                'booked_slots' => $slots->where('available', false)->count(),
                'full' => $slots->every(fn ($slot) => ! $slot['available']),
                'slots' => $slots,
            ];
        });

        return view('reservations.index', [
            'selectedDate' => $selectedDate,
            'bookings' => $bookings,
            'courtAvailability' => $courtAvailability,
            'courts' => $courts->values()->all(),
            'courtCount' => $courtCount,
            'timeSlots' => $timeSlots,
            'rateBoundaryOptions' => $this->reservationManager->rateBoundaryOptions(),
            'minBookingDate' => now()->toDateString(),
            'maxBookingDate' => $this->reservationManager->maxCustomerBookingDate()->toDateString(),
            'customerBookingWindowDays' => $this->reservationManager->customerBookingWindowInDays(),
            'reservationFee' => $this->reservationManager->reservationFee(),
            'defaultDayStartTime' => $this->reservationManager->defaultDayStartTime(),
            'defaultDayEndTime' => $this->reservationManager->defaultDayEndTime(),
            'defaultNightStartTime' => $this->reservationManager->defaultNightStartTime(),
            'defaultNightEndTime' => $this->reservationManager->defaultNightEndTime(),
            'oldPaddleRentRate' => $this->reservationManager->oldPaddleRentRate(),
            'newPaddleRentRate' => $this->reservationManager->newPaddleRentRate(),
            'ballRate' => $this->reservationManager->ballRate(),
            'contactNumberReady' => Reservation::contactNumberColumnReady(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $timeSlots = $this->reservationManager->timeSlots();

        if (! Reservation::contactNumberColumnReady()) {
            return back()->withInput()->with('error', 'Contact number field is not ready yet. Run php artisan migrate first.');
        }

        if (! Reservation::rentalColumnsReady()) {
            return back()->withInput()->with('error', 'Rental quantity fields are not ready yet. Run php artisan migrate first.');
        }

        $validated = $request->validate([
            'booking_date' => 'required|date|after_or_equal:today|before_or_equal:' . $this->reservationManager->maxCustomerBookingDate()->toDateString(),
            'court_number' => 'required|integer|min:1|max:' . $this->reservationManager->courtCount(),
            'time_slot' => 'required|string|in:' . implode(',', $timeSlots),
            'players' => 'required|integer|min:1|max:8',
            'paddle_rent_quantity' => 'nullable|integer|min:0|max:20',
            'new_paddle_rent_quantity' => 'nullable|integer|min:0|max:20',
            'ball_quantity' => 'nullable|integer|min:0|max:20',
            'contact_number' => 'required|string|max:30',
            'payment_method' => 'required|string|in:gcash',
            'payment_reference' => 'required|string|max:255',
        ]);

        if (! $this->reservationManager->isCourtAvailable(
            $validated['booking_date'],
            $validated['time_slot'],
            $validated['court_number'],
        )) {
            return back()->withInput()->with('error', 'Kana nga court booked na sa selected date ug time. Pili ug laing court o time slot.');
        }

        $reservation = $this->reservationManager->createReservation([
            'user_id' => $user->id,
            'customer_name' => $user->name,
            'contact_number' => $validated['contact_number'],
            'booking_date' => $validated['booking_date'],
            'time_slot' => $validated['time_slot'],
            'court_number' => $validated['court_number'],
            'players' => $validated['players'],
            'paddle_rent_quantity' => $validated['paddle_rent_quantity'] ?? 0,
            'new_paddle_rent_quantity' => $validated['new_paddle_rent_quantity'] ?? 0,
            'ball_quantity' => $validated['ball_quantity'] ?? 0,
            'payment_method' => $validated['payment_method'],
            'payment_reference' => $validated['payment_reference'],
            'payment_status' => 'Paid',
        ]);

        return redirect()->route('reservations.receipt', $reservation)
            ->with('success', 'Reservation confirmed successfully.');
    }

    public function reschedule(Request $request, Reservation $reservation): RedirectResponse
    {
        $user = $request->user();

        abort_unless($reservation->user_id === $user->id, 403);

        if (! Reservation::rescheduleColumnsReady()) {
            return back()->with('error', 'Rain reschedule fields are not ready yet. Run php artisan migrate first.');
        }

        if (! $reservation->isRescheduleUnlocked()) {
            return back()->with('error', 'This reservation is not unlocked for reschedule. Admin ra ang maka-unlock ani during rainy-day cases.');
        }

        $deadline = $reservation->reschedule_deadline?->toDateString() ?? now()->toDateString();
        $timeSlots = $this->reservationManager->timeSlots();

        $validated = $request->validate([
            'booking_date' => 'required|date|after_or_equal:today|before_or_equal:' . $deadline,
            'court_number' => 'required|integer|min:1|max:' . $this->reservationManager->courtCount(),
            'time_slot' => 'required|string|in:' . implode(',', $timeSlots),
        ]);

        $sameSchedule = $reservation->booking_date->toDateString() === $validated['booking_date']
            && $reservation->time_slot === $validated['time_slot']
            && $reservation->court_number === (int) $validated['court_number'];

        if ($sameSchedule) {
            return back()->withInput()->with('error', 'Palihug pili ug laing date, court, or time slot for the reschedule.');
        }

        if (! $this->reservationManager->isCourtAvailable(
            $validated['booking_date'],
            $validated['time_slot'],
            $validated['court_number'],
            $reservation->id,
        )) {
            return back()->withInput()->with('error', 'Kana nga court booked na sa selected date ug time. Pili ug laing schedule.');
        }

        $reservation->update([
            'booking_date' => $validated['booking_date'],
            'court_number' => $validated['court_number'],
            'court_name' => $this->reservationManager->courtName((int) $validated['court_number']),
            'time_slot' => $validated['time_slot'],
            'amount' => $this->reservationManager->calculateReservationAmount(
                (int) $validated['court_number'],
                $validated['time_slot'],
                (int) ($reservation->paddle_rent_quantity ?? 0),
                (int) ($reservation->new_paddle_rent_quantity ?? 0),
                (int) ($reservation->ball_quantity ?? 0),
            ),
            'reschedule_unlocked_at' => null,
            'reschedule_deadline' => null,
            'reschedule_reason' => null,
        ]);

        return redirect()->route('reservations.receipt', $reservation)
            ->with('success', 'Reservation rescheduled successfully.');
    }

    public function receipt(Request $request, Reservation $reservation): View
    {
        $user = $request->user();

        abort_unless(
            $user->isAdmin() || $reservation->user_id === $user->id,
            403
        );

        $reservation->loadMissing('user');

        return view('reservations.receipt', [
            'reservation' => $reservation,
            'courtLabel' => $this->reservationManager->courtLabel(
                $reservation->court_number,
                $reservation->court_name,
            ),
        ]);
    }
}
