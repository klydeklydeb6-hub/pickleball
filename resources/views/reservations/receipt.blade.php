<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Receipt</title>
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background: #f4f7fb;
            margin: 0;
            padding: 24px;
            color: #0f172a;
        }

        .card {
            max-width: 720px;
            margin: auto;
            background: white;
            border-radius: 24px;
            padding: 28px;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
            border: 1px solid rgba(15, 23, 42, 0.06);
        }

        .row {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .row:last-of-type {
            border-bottom: none;
        }

        .label {
            color: #64748b;
        }

        .actions {
            margin-top: 24px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .notice {
            margin-top: 20px;
            border-radius: 18px;
            padding: 16px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            color: #9a3412;
        }

        .btn, .btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 16px;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 600;
        }

        .btn {
            background: #0f172a;
            color: white;
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #0f172a;
        }

        @media (max-width: 640px) {
            body {
                padding: 16px;
            }

            .row {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

@php
    $oldPaddleRentQuantity = max(0, (int) ($reservation->paddle_rent_quantity ?? 0));
    $newPaddleRentQuantity = max(0, (int) ($reservation->new_paddle_rent_quantity ?? 0));
    $ballQuantity = max(0, (int) ($reservation->ball_quantity ?? 0));
@endphp

<div class="card">
    <h1 style="margin-top: 0;">Reservation Receipt</h1>
    <p style="color: #64748b; margin-top: -6px;">Keep this page as your booking confirmation.</p>

    <div class="row">
        <span class="label">Receipt No.</span>
        <strong>{{ $reservation->receipt_no }}</strong>
    </div>
    <div class="row">
        <span class="label">Reserved By</span>
        <strong>{{ $reservation->customer_name }}</strong>
    </div>
    <div class="row">
        <span class="label">Account Email</span>
        <strong>{{ $reservation->user?->email ?? 'Walk-in / No linked account' }}</strong>
    </div>
    <div class="row">
        <span class="label">Contact Number</span>
        <strong>{{ $reservation->contact_number ?: 'N/A' }}</strong>
    </div>
    <div class="row">
        <span class="label">Date</span>
        <strong>{{ $reservation->booking_date->format('F d, Y') }}</strong>
    </div>
    <div class="row">
        <span class="label">Schedule</span>
        <strong>{{ $reservation->timeRangeLabel() }}</strong>
    </div>
    <div class="row">
        <span class="label">Duration</span>
        <strong>{{ $reservation->durationLabel() }}</strong>
    </div>
    <div class="row">
        <span class="label">Court</span>
        <strong>{{ $courtLabel }}</strong>
    </div>
    <div class="row">
        <span class="label">Players</span>
        <strong>{{ $reservation->players }}</strong>
    </div>
    <div class="row">
        <span class="label">New Paddle Rent</span>
        <strong>{{ $newPaddleRentQuantity > 0 ? $newPaddleRentQuantity . ' paddle' . ($newPaddleRentQuantity === 1 ? '' : 's') : 'None' }}</strong>
    </div>
    <div class="row">
        <span class="label">Old Paddle Rent</span>
        <strong>{{ $oldPaddleRentQuantity > 0 ? $oldPaddleRentQuantity . ' paddle' . ($oldPaddleRentQuantity === 1 ? '' : 's') : 'None' }}</strong>
    </div>
    <div class="row">
        <span class="label">Ball Rent</span>
        <strong>{{ $ballQuantity > 0 ? $ballQuantity . ' ball' . ($ballQuantity === 1 ? '' : 's') : 'None' }}</strong>
    </div>
    <div class="row">
        <span class="label">Amount</span>
        <strong>PHP {{ number_format($reservation->amount, 2) }}</strong>
    </div>
    <div class="row">
        <span class="label">Payment Method</span>
        <strong>{{ strtoupper($reservation->payment_method) }}</strong>
    </div>
    <div class="row">
        <span class="label">Payment Status</span>
        <strong>{{ $reservation->payment_status }}</strong>
    </div>
    <div class="row">
        <span class="label">Reference</span>
        <strong>{{ $reservation->payment_reference ?: 'N/A' }}</strong>
    </div>
    <div class="row">
        <span class="label">Created At</span>
        <strong>{{ $reservation->created_at->format('M d, Y h:i A') }}</strong>
    </div>

    <div class="notice">
        <strong>No cancellation once paid or reserved.</strong><br><br>
        If mag-ulan and dili magamit ang court because walay atop, admin ra ang maka-unlock sa reschedule.
        Once unlocked, the customer can choose a new slot within 15 days from the original booking date.
    </div>

    <div class="actions">
        <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard') }}" class="btn-secondary">{{ auth()->user()->isAdmin() ? 'Back to Admin Dashboard' : 'Back to My Dashboard' }}</a>
        <a href="{{ route('reservations.index') }}" class="btn">Back to Booking Page</a>
    </div>
</div>

</body>
</html>
