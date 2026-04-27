<?php

namespace App\Support;

use App\Models\Reservation;

class PayMongoCheckout
{
    public function isConfigured(): bool
    {
        return trim((string) config('services.paymongo.payment_link_url')) !== '';
    }

    public function pendingCheckoutTtlInMinutes(): int
    {
        return max(5, (int) config('services.paymongo.pending_ttl_minutes', 30));
    }

    public function paymentLinkUrl(Reservation $reservation): string
    {
        $link = trim((string) config('services.paymongo.payment_link_url'));

        return strtr($link, [
            '{receipt_no}' => (string) $reservation->receipt_no,
            '{reservation_id}' => (string) $reservation->id,
            '{amount}' => number_format((float) $reservation->amount, 2, '.', ''),
            '{customer_name}' => (string) $reservation->customer_name,
            '{booking_date}' => $reservation->booking_date->toDateString(),
            '{time_slot}' => $reservation->timeRangeLabel(),
            '{court_number}' => (string) $reservation->court_number,
        ]);
    }
}
