<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class Reservation extends Model
{
    protected static ?bool $rescheduleColumnsReady = null;

    protected static ?bool $contactNumberColumnReady = null;

    protected static ?bool $rentalColumnsReady = null;

    protected $fillable = [
        'user_id',
        'customer_name',
        'contact_number',
        'booking_date',
        'time_slot',
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
}
