<?php

namespace App\Models;

use App\Support\ReservationManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Reservation extends Model
{
    protected static ?bool $rescheduleColumnsReady = null;

    protected static ?bool $contactNumberColumnReady = null;

    protected static ?bool $rentalColumnsReady = null;

    protected static ?bool $durationColumnReady = null;

    protected $fillable = [
        'user_id',
        'customer_name',
        'contact_number',
        'booking_date',
        'time_slot',
        'duration_hours',
        'court_number',
        'court_name',
        'players',
        'paddle_rent_quantity',
        'new_paddle_rent_quantity',
        'ball_quantity',
        'payment_method',
        'payment_reference',
        'payment_status',
        'reschedule_unlocked_at',
        'reschedule_deadline',
        'reschedule_reason',
        'amount',
        'receipt_no',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'duration_hours' => 'integer',
            'paddle_rent_quantity' => 'integer',
            'new_paddle_rent_quantity' => 'integer',
            'ball_quantity' => 'integer',
            'amount' => 'decimal:2',
            'reschedule_unlocked_at' => 'datetime',
            'reschedule_deadline' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isRescheduleUnlocked(): bool
    {
        if (! static::rescheduleColumnsReady()) {
            return false;
        }

        return $this->user_id !== null
            && $this->reschedule_unlocked_at !== null
            && $this->reschedule_deadline !== null
            && now()->startOfDay()->lte($this->reschedule_deadline);
    }

    public function durationHours(): int
    {
        if (! static::durationColumnReady()) {
            return 1;
        }

        return max(1, (int) ($this->getAttribute('duration_hours') ?? 1));
    }

    public function durationLabel(): string
    {
        $durationHours = $this->durationHours();

        return $durationHours . ' hour' . ($durationHours === 1 ? '' : 's');
    }

    public function occupiedTimeSlots(): array
    {
        return app(ReservationManager::class)->occupiedTimeSlotsForReservation($this);
    }

    public function timeRangeLabel(): string
    {
        return app(ReservationManager::class)->formatReservationTimeRange(
            (string) $this->time_slot,
            $this->durationHours(),
        );
    }

    public function verificationCode(): string
    {
        $createdAt = $this->created_at?->toIso8601String() ?? 'missing-created-at';

        return Str::upper(substr(
            hash_hmac(
                'sha256',
                implode('|', [
                    (string) $this->getKey(),
                    (string) $this->receipt_no,
                    $createdAt,
                ]),
                (string) config('app.key'),
            ),
            0,
            12,
        ));
    }

    public function hasVerificationCode(?string $code): bool
    {
        $normalizedCode = Str::upper(preg_replace('/[^A-Z0-9]/', '', (string) $code));

        if ($normalizedCode === '') {
            return false;
        }

        return hash_equals($this->verificationCode(), $normalizedCode);
    }

    public function isPendingPayMongoCheckout(): bool
    {
        return strtolower((string) $this->payment_method) === 'paymongo'
            && strtolower((string) $this->payment_status) === 'pending';
    }

    public static function expireStalePendingPayMongoReservations(): void
    {
        $ttlMinutes = max(5, (int) config('services.paymongo.pending_ttl_minutes', 30));

        static::query()
            ->where('payment_method', 'paymongo')
            ->where('payment_status', 'Pending')
            ->where('created_at', '<', now()->subMinutes($ttlMinutes))
            ->delete();
    }

    public static function rescheduleColumnsReady(): bool
    {
        if (static::$rescheduleColumnsReady !== null) {
            return static::$rescheduleColumnsReady;
        }

        foreach (['reschedule_unlocked_at', 'reschedule_deadline', 'reschedule_reason'] as $column) {
            if (! Schema::hasColumn('reservations', $column)) {
                return static::$rescheduleColumnsReady = false;
            }
        }

        return static::$rescheduleColumnsReady = true;
    }

    public static function contactNumberColumnReady(): bool
    {
        if (static::$contactNumberColumnReady !== null) {
            return static::$contactNumberColumnReady;
        }

        return static::$contactNumberColumnReady = Schema::hasColumn('reservations', 'contact_number');
    }

    public static function rentalColumnsReady(): bool
    {
        if (static::$rentalColumnsReady !== null) {
            return static::$rentalColumnsReady;
        }

        foreach (['paddle_rent_quantity', 'new_paddle_rent_quantity', 'ball_quantity'] as $column) {
            if (! Schema::hasColumn('reservations', $column)) {
                return static::$rentalColumnsReady = false;
            }
        }

        return static::$rentalColumnsReady = true;
    }

    public static function durationColumnReady(): bool
    {
        if (static::$durationColumnReady !== null) {
            return static::$durationColumnReady;
        }

        return static::$durationColumnReady = Schema::hasColumn('reservations', 'duration_hours');
    }
}
