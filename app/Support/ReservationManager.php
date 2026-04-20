<?php

namespace App\Support;

use App\Models\FacilitySetting;
use App\Models\Reservation;

class ReservationManager
{
    public function timeSlots(): array
    {
        return [
            '6:00 AM', '7:00 AM', '8:00 AM', '9:00 AM', '10:00 AM', '11:00 AM',
            '1:00 PM', '2:00 PM', '3:00 PM', '4:00 PM', '5:00 PM', '6:00 PM', '7:00 PM', '8:00 PM',
        ];
    }

    public function courtCount(): int
    {
        return FacilitySetting::currentCourtCount();
    }

    public function reservationFee(): int
    {
        return FacilitySetting::currentReservationRate();
    }

    public function rateBoundaryOptions(): array
    {
        return [
            '12:00 AM', '1:00 AM', '2:00 AM', '3:00 AM', '4:00 AM', '5:00 AM',
            '6:00 AM', '7:00 AM', '8:00 AM', '9:00 AM', '10:00 AM', '11:00 AM',
            '12:00 PM', '1:00 PM', '2:00 PM', '3:00 PM', '4:00 PM', '5:00 PM',
            '6:00 PM', '7:00 PM', '8:00 PM', '9:00 PM', '10:00 PM', '11:00 PM',
        ];
    }

    public function defaultDayStartTime(): string
    {
        return '5:00 AM';
    }

    public function defaultDayEndTime(): string
    {
        return '5:00 PM';
    }

    public function defaultNightStartTime(): string
    {
        return '5:00 PM';
    }

    public function defaultNightEndTime(): string
    {
        return '12:00 AM';
    }

    public function courts(): array
    {
        $courts = [];

        foreach (FacilitySetting::currentCourtDetails() as $courtNumber => $detail) {
            $courtNumber = (int) $courtNumber;
            $courtName = trim((string) ($detail['name'] ?? FacilitySetting::defaultCourtName($courtNumber)));
            $dayRate = is_numeric($detail['day_rate'] ?? null)
                ? max(0, (int) $detail['day_rate'])
                : (is_numeric($detail['rate'] ?? null) ? max(0, (int) $detail['rate']) : $this->reservationFee());
            $nightRate = is_numeric($detail['night_rate'] ?? null)
                ? max(0, (int) $detail['night_rate'])
                : $dayRate;
            $dayStartsAt = $this->normalizeRateBoundary($detail['day_starts_at'] ?? null, $this->defaultDayStartTime());
            $dayEndsAt = $this->normalizeRateBoundary($detail['day_ends_at'] ?? null, $this->defaultDayEndTime());
            $nightStartsAt = $this->normalizeRateBoundary($detail['night_starts_at'] ?? null, $this->defaultNightStartTime());
            $nightEndsAt = $this->normalizeRateBoundary($detail['night_ends_at'] ?? null, $this->defaultNightEndTime());

            $courts[] = [
                'number' => $courtNumber,
                'name' => $courtName !== '' ? $courtName : FacilitySetting::defaultCourtName($courtNumber),
                'label' => $this->courtLabel($courtNumber, $courtName),
                'rate' => $dayRate,
                'day_rate' => $dayRate,
                'day_starts_at' => $dayStartsAt,
                'day_ends_at' => $dayEndsAt,
                'night_rate' => $nightRate,
                'night_starts_at' => $nightStartsAt,
                'night_ends_at' => $nightEndsAt,
                'rate_summary' => $this->courtRateSummary($courtNumber, [
                    'day_rate' => $dayRate,
                    'day_starts_at' => $dayStartsAt,
                    'day_ends_at' => $dayEndsAt,
                    'night_rate' => $nightRate,
                    'night_starts_at' => $nightStartsAt,
                    'night_ends_at' => $nightEndsAt,
                ]),
            ];
        }

        return $courts;
    }

    public function courtDetail(int $courtNumber): array
    {
        foreach ($this->courts() as $court) {
            if ($court['number'] === $courtNumber) {
                return $court;
            }
        }

        return [
            'number' => $courtNumber,
            'name' => FacilitySetting::defaultCourtName($courtNumber),
            'label' => FacilitySetting::defaultCourtName($courtNumber),
            'rate' => $this->reservationFee(),
            'day_rate' => $this->reservationFee(),
            'day_starts_at' => $this->defaultDayStartTime(),
            'day_ends_at' => $this->defaultDayEndTime(),
            'night_rate' => $this->reservationFee(),
            'night_starts_at' => $this->defaultNightStartTime(),
            'night_ends_at' => $this->defaultNightEndTime(),
            'rate_summary' => $this->courtRateSummary($courtNumber, [
                'day_rate' => $this->reservationFee(),
                'day_starts_at' => $this->defaultDayStartTime(),
                'day_ends_at' => $this->defaultDayEndTime(),
                'night_rate' => $this->reservationFee(),
                'night_starts_at' => $this->defaultNightStartTime(),
                'night_ends_at' => $this->defaultNightEndTime(),
            ]),
        ];
    }

    public function courtName(int $courtNumber): string
    {
        return $this->courtDetail($courtNumber)['name'];
    }

    public function courtLabel(int $courtNumber, ?string $courtName = null): string
    {
        $defaultName = FacilitySetting::defaultCourtName($courtNumber);
        $courtName = trim((string) ($courtName ?: $this->courtDetail($courtNumber)['name']));

        if ($courtName === '' || $courtName === $defaultName) {
            return $defaultName;
        }

        return "{$courtName} ({$defaultName})";
    }

    public function courtRate(int $courtNumber): int
    {
        return $this->courtDetail($courtNumber)['rate'];
    }

    public function courtRateForTimeSlot(int $courtNumber, string $timeSlot): int
    {
        $court = $this->courtDetail($courtNumber);

        if ($this->timeFallsWithinRange($timeSlot, $court['day_starts_at'], $court['day_ends_at'])) {
            return (int) ($court['day_rate'] ?? $court['rate'] ?? $this->reservationFee());
        }

        if ($this->timeFallsWithinRange($timeSlot, $court['night_starts_at'], $court['night_ends_at'])) {
            return (int) ($court['night_rate'] ?? $court['day_rate'] ?? $court['rate'] ?? $this->reservationFee());
        }

        return (int) ($court['day_rate'] ?? $court['rate'] ?? $this->reservationFee());
    }

    public function timePeriodForSlot(int $courtNumber, string $timeSlot): string
    {
        $court = $this->courtDetail($courtNumber);

        if ($this->timeFallsWithinRange($timeSlot, $court['day_starts_at'], $court['day_ends_at'])) {
            return 'Day Rate';
        }

        if ($this->timeFallsWithinRange($timeSlot, $court['night_starts_at'], $court['night_ends_at'])) {
            return 'Night Rate';
        }

        return 'Default Rate';
    }

    public function paddleRentRate(): int
    {
        return FacilitySetting::currentPaddleRentRate();
    }

    public function oldPaddleRentRate(): int
    {
        return FacilitySetting::currentPaddleRentRate();
    }

    public function newPaddleRentRate(): int
    {
        return FacilitySetting::currentNewPaddleRentRate();
    }

    public function ballRate(): int
    {
        return FacilitySetting::currentBallRate();
    }

    public function calculateEquipmentTotal(
        int $oldPaddleRentQuantity = 0,
        int $newPaddleRentQuantity = 0,
        int $ballQuantity = 0,
    ): int {
        return ($this->normalizeQuantity($oldPaddleRentQuantity) * $this->oldPaddleRentRate())
            + ($this->normalizeQuantity($newPaddleRentQuantity) * $this->newPaddleRentRate())
            + ($this->normalizeQuantity($ballQuantity) * $this->ballRate());
    }

    public function calculateReservationAmount(
        int $courtNumber,
        string $timeSlot,
        int $oldPaddleRentQuantity = 0,
        int $newPaddleRentQuantity = 0,
        int $ballQuantity = 0,
    ): int {
        $courtRate = $timeSlot !== ''
            ? $this->courtRateForTimeSlot($courtNumber, $timeSlot)
            : $this->courtRate($courtNumber);

        return $courtRate + $this->calculateEquipmentTotal(
            $oldPaddleRentQuantity,
            $newPaddleRentQuantity,
            $ballQuantity,
        );
    }

    public function createReservation(array $attributes): Reservation
    {
        $courtNumber = (int) ($attributes['court_number'] ?? 0);
        $court = $courtNumber > 0 ? $this->courtDetail($courtNumber) : null;
        $timeSlot = (string) ($attributes['time_slot'] ?? '');
        $oldPaddleRentQuantity = $this->normalizeQuantity($attributes['paddle_rent_quantity'] ?? 0);
        $newPaddleRentQuantity = $this->normalizeQuantity($attributes['new_paddle_rent_quantity'] ?? 0);
        $ballQuantity = $this->normalizeQuantity($attributes['ball_quantity'] ?? 0);

        return Reservation::create([
            ...$attributes,
            'court_name' => $attributes['court_name'] ?? ($court['name'] ?? null),
            'paddle_rent_quantity' => $oldPaddleRentQuantity,
            'new_paddle_rent_quantity' => $newPaddleRentQuantity,
            'ball_quantity' => $ballQuantity,
            'amount' => $attributes['amount'] ?? (
                $courtNumber > 0 && $timeSlot !== ''
                    ? $this->calculateReservationAmount($courtNumber, $timeSlot, $oldPaddleRentQuantity, $newPaddleRentQuantity, $ballQuantity)
                    : (($court['day_rate'] ?? $court['rate'] ?? $this->reservationFee()) + $this->calculateEquipmentTotal($oldPaddleRentQuantity, $newPaddleRentQuantity, $ballQuantity))
            ),
            'receipt_no' => $attributes['receipt_no'] ?? $this->generateReceiptNumber(),
        ]);
    }

    public function customerBookingWindowInDays(): int
    {
        return 15;
    }

    public function maxCustomerBookingDate(): \Illuminate\Support\Carbon
    {
        return now()->addDays($this->customerBookingWindowInDays())->startOfDay();
    }

    public function findAvailableCourt(string $bookingDate, string $timeSlot): ?int
    {
        $existingBookings = Reservation::query()
            ->whereDate('booking_date', $bookingDate)
            ->where('time_slot', $timeSlot)
            ->pluck('court_number')
            ->all();

        for ($courtNumber = 1; $courtNumber <= $this->courtCount(); $courtNumber++) {
            if (! in_array($courtNumber, $existingBookings, true)) {
                return $courtNumber;
            }
        }

        return null;
    }

    public function isCourtAvailable(string $bookingDate, string $timeSlot, int $courtNumber, ?int $ignoreReservationId = null): bool
    {
        $query = Reservation::query()
            ->whereDate('booking_date', $bookingDate)
            ->where('time_slot', $timeSlot)
            ->where('court_number', $courtNumber);

        if ($ignoreReservationId !== null) {
            $query->whereKeyNot($ignoreReservationId);
        }

        return ! $query->exists();
    }

    public function maxReservedCourtForActiveReservations(): int
    {
        return (int) Reservation::query()
            ->whereDate('booking_date', '>=', now()->toDateString())
            ->max('court_number');
    }

    private function generateReceiptNumber(): string
    {
        return 'RCPT-' . now()->format('YmdHis') . '-' . random_int(100, 999);
    }

    private function normalizeRateBoundary(?string $value, string $fallback): string
    {
        $value = trim((string) $value);

        if ($value !== '' && in_array($value, $this->rateBoundaryOptions(), true)) {
            return $value;
        }

        return $fallback;
    }

    private function normalizeQuantity($value): int
    {
        return max(0, (int) $value);
    }

    private function timeFallsWithinRange(string $timeSlot, ?string $from, ?string $to): bool
    {
        $slotMinutes = $this->timeToMinutes($timeSlot);
        $fromMinutes = $this->timeToMinutes((string) $from);
        $toMinutes = $this->timeToMinutes((string) $to);

        if ($slotMinutes === null || $fromMinutes === null || $toMinutes === null) {
            return false;
        }

        if ($fromMinutes === $toMinutes) {
            return true;
        }

        if ($fromMinutes < $toMinutes) {
            return $slotMinutes >= $fromMinutes && $slotMinutes < $toMinutes;
        }

        return $slotMinutes >= $fromMinutes || $slotMinutes < $toMinutes;
    }

    private function courtRateSummary(int $courtNumber, array $court): string
    {
        $dayStartsAt = $this->normalizeRateBoundary($court['day_starts_at'] ?? null, $this->defaultDayStartTime());
        $dayEndsAt = $this->normalizeRateBoundary($court['day_ends_at'] ?? null, $this->defaultDayEndTime());
        $nightStartsAt = $this->normalizeRateBoundary($court['night_starts_at'] ?? null, $this->defaultNightStartTime());
        $nightEndsAt = $this->normalizeRateBoundary($court['night_ends_at'] ?? null, $this->defaultNightEndTime());
        $dayRate = (int) ($court['day_rate'] ?? $court['rate'] ?? $this->reservationFee());
        $nightRate = (int) ($court['night_rate'] ?? $dayRate);

        return $dayStartsAt . ' - ' . $dayEndsAt . ': PHP ' . number_format($dayRate, 2)
            . ' | ' . $nightStartsAt . ' - ' . $nightEndsAt . ': PHP ' . number_format($nightRate, 2);
    }

    private function timeToMinutes(string $time): ?int
    {
        $parsed = \DateTime::createFromFormat('g:i A', $time);

        if (! $parsed) {
            return null;
        }

        return ((int) $parsed->format('H')) * 60 + (int) $parsed->format('i');
    }
}
