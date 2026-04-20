<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class FacilitySetting extends Model
{
    protected static ?bool $tableExists = null;

    protected static ?bool $rateColumnsReady = null;

    protected static ?bool $courtDetailsReady = null;

    protected $guarded = [];

    protected $casts = [
        'court_details' => 'array',
    ];

    public static function defaults(): array
    {
        return [
            'id' => 1,
            'court_count' => 9,
            'reservation_rate' => 500,
            'paddle_rent_rate' => 50,
            'new_paddle_rent_rate' => 60,
            'ball_rate' => 0,
            'court_details' => [],
        ];
    }

    public static function defaultCourtName(int $courtNumber): string
    {
        return "Court {$courtNumber}";
    }

    public static function tableExists(): bool
    {
        if (static::$tableExists !== null) {
            return static::$tableExists;
        }

        return static::$tableExists = Schema::hasTable((new static)->getTable());
    }

    public static function rateColumnsReady(): bool
    {
        if (! static::tableExists()) {
            return false;
        }

        if (static::$rateColumnsReady !== null) {
            return static::$rateColumnsReady;
        }

        foreach (['reservation_rate', 'paddle_rent_rate', 'new_paddle_rent_rate', 'ball_rate'] as $column) {
            if (! Schema::hasColumn((new static)->getTable(), $column)) {
                return static::$rateColumnsReady = false;
            }
        }

        return static::$rateColumnsReady = true;
    }

    public static function courtDetailsReady(): bool
    {
        if (! static::tableExists()) {
            return false;
        }

        if (static::$courtDetailsReady !== null) {
            return static::$courtDetailsReady;
        }

        return static::$courtDetailsReady = Schema::hasColumn((new static)->getTable(), 'court_details');
    }

    public static function current(): self
    {
        $defaults = static::defaults();

        if (! static::tableExists()) {
            return new static($defaults);
        }

        $fillableDefaults = [
            'court_count' => $defaults['court_count'],
        ];

        if (static::rateColumnsReady()) {
            $fillableDefaults['reservation_rate'] = $defaults['reservation_rate'];
            $fillableDefaults['paddle_rent_rate'] = $defaults['paddle_rent_rate'];
            $fillableDefaults['new_paddle_rent_rate'] = $defaults['new_paddle_rent_rate'];
            $fillableDefaults['ball_rate'] = $defaults['ball_rate'];
        }

        if (static::courtDetailsReady()) {
            $fillableDefaults['court_details'] = static::normalizeCourtDetails(
                $defaults['court_details'],
                $defaults['court_count'],
                $defaults['reservation_rate'],
            );
        }

        return static::query()->firstOrCreate(['id' => 1], $fillableDefaults);
    }

    public static function currentCourtCount(): int
    {
        return (int) static::current()->court_count;
    }

    public static function currentReservationRate(): int
    {
        if (! static::rateColumnsReady()) {
            return static::defaults()['reservation_rate'];
        }

        return (int) (static::current()->reservation_rate ?? static::defaults()['reservation_rate']);
    }

    public static function currentPaddleRentRate(): int
    {
        if (! static::rateColumnsReady()) {
            return static::defaults()['paddle_rent_rate'];
        }

        return (int) (static::current()->paddle_rent_rate ?? static::defaults()['paddle_rent_rate']);
    }

    public static function currentNewPaddleRentRate(): int
    {
        if (! static::rateColumnsReady()) {
            return static::defaults()['new_paddle_rent_rate'];
        }

        return (int) (static::current()->new_paddle_rent_rate ?? static::defaults()['new_paddle_rent_rate']);
    }

    public static function currentBallRate(): int
    {
        if (! static::rateColumnsReady()) {
            return static::defaults()['ball_rate'];
        }

        return (int) (static::current()->ball_rate ?? static::defaults()['ball_rate']);
    }

    public static function currentCourtDetails(): array
    {
        $defaults = static::defaults();

        if (! static::tableExists()) {
            return static::normalizeCourtDetails(
                $defaults['court_details'],
                $defaults['court_count'],
                $defaults['reservation_rate'],
            );
        }

        $settings = static::current();
        $courtCount = (int) ($settings->court_count ?? $defaults['court_count']);
        $defaultRate = static::rateColumnsReady()
            ? (int) ($settings->reservation_rate ?? $defaults['reservation_rate'])
            : $defaults['reservation_rate'];

        return static::normalizeCourtDetails(
            static::courtDetailsReady() ? $settings->court_details : $defaults['court_details'],
            $courtCount,
            $defaultRate,
        );
    }

    public static function normalizeCourtDetails(?array $courtDetails, int $courtCount, int $defaultRate): array
    {
        $courtDetails = is_array($courtDetails) ? $courtDetails : [];
        $normalized = [];
        $defaultDayStart = '5:00 AM';
        $defaultDayEnd = '5:00 PM';
        $defaultNightStart = '5:00 PM';
        $defaultNightEnd = '12:00 AM';

        for ($courtNumber = 1; $courtNumber <= $courtCount; $courtNumber++) {
            $detail = $courtDetails[$courtNumber] ?? $courtDetails[(string) $courtNumber] ?? [];

            if (! is_array($detail)) {
                $detail = [];
            }

            $name = trim((string) ($detail['name'] ?? ''));
            $legacyRate = $detail['rate'] ?? null;
            $dayRate = $detail['day_rate'] ?? $detail['morning_rate'] ?? $legacyRate;
            $nightRate = $detail['night_rate'] ?? $detail['evening_rate'] ?? $legacyRate;
            $dayStartsAt = trim((string) ($detail['day_starts_at'] ?? $defaultDayStart));
            $dayEndsAt = trim((string) ($detail['day_ends_at'] ?? ($detail['evening_starts_at'] ?? $defaultDayEnd)));
            $nightStartsAt = trim((string) ($detail['night_starts_at'] ?? ($detail['evening_starts_at'] ?? $defaultNightStart)));
            $nightEndsAt = trim((string) ($detail['night_ends_at'] ?? $defaultNightEnd));

            $resolvedDayRate = is_numeric($dayRate) ? max(0, (int) $dayRate) : max(0, $defaultRate);
            $resolvedNightRate = is_numeric($nightRate) ? max(0, (int) $nightRate) : $resolvedDayRate;

            $normalized[$courtNumber] = [
                'name' => $name !== '' ? $name : static::defaultCourtName($courtNumber),
                'rate' => $resolvedDayRate,
                'day_rate' => $resolvedDayRate,
                'day_starts_at' => $dayStartsAt !== '' ? $dayStartsAt : $defaultDayStart,
                'day_ends_at' => $dayEndsAt !== '' ? $dayEndsAt : $defaultDayEnd,
                'night_rate' => $resolvedNightRate,
                'night_starts_at' => $nightStartsAt !== '' ? $nightStartsAt : $defaultNightStart,
                'night_ends_at' => $nightEndsAt !== '' ? $nightEndsAt : $defaultNightEnd,
            ];
        }

        return $normalized;
    }
}
