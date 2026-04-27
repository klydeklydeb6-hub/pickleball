<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt {{ $reservation->receipt_no }} | Pickle BALLan ni Juan</title>
    <style>
        :root {
            --page-bg: #edf2f7;
            --paper: #fffef8;
            --paper-line: #d9e1ea;
            --paper-shadow: 0 28px 70px rgba(15, 23, 42, 0.16);
            --text: #1f3f67;
            --muted: #6a7f98;
            --accent: #1d5fb8;
            --accent-soft: #e8f1ff;
            --good: #1c7a4c;
            --good-soft: #e7f7ef;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top, rgba(29, 95, 184, 0.08), transparent 30%),
                linear-gradient(180deg, #f8fbff 0%, var(--page-bg) 100%);
        }

        a {
            color: inherit;
        }

        .shell {
            width: min(760px, calc(100% - 24px));
            margin: 28px auto 40px;
        }

        .receipt {
            position: relative;
            overflow: hidden;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.92), rgba(255, 255, 255, 0.98)),
                repeating-linear-gradient(
                    0deg,
                    rgba(31, 63, 103, 0.018) 0,
                    rgba(31, 63, 103, 0.018) 2px,
                    transparent 2px,
                    transparent 7px
                );
            border: 1px solid rgba(217, 225, 234, 0.92);
            border-radius: 28px;
            box-shadow: var(--paper-shadow);
        }

        .receipt::before,
        .receipt::after {
            content: "";
            position: absolute;
            left: 24px;
            right: 24px;
            height: 10px;
            background:
                radial-gradient(circle at 5px 5px, transparent 5px, rgba(255, 255, 255, 0.92) 5px);
            background-size: 20px 10px;
            opacity: 0.9;
            pointer-events: none;
        }

        .receipt::before {
            top: 0;
            transform: translateY(-50%);
        }

        .receipt::after {
            bottom: 0;
            transform: translateY(50%) rotate(180deg);
        }

        .receipt-body {
            padding: 32px 34px 28px;
        }

        .brand {
            text-align: center;
        }

        .logo {
            display: block;
            width: 168px;
            max-width: 48%;
            margin: 0 auto 14px;
        }

        .brand-title {
            margin: 0;
            font-size: clamp(2rem, 4vw, 2.7rem);
            font-weight: 800;
            letter-spacing: -0.04em;
            text-transform: uppercase;
        }

        .brand-subtitle {
            margin: 10px auto 0;
            max-width: 420px;
            color: var(--muted);
            font-size: 0.98rem;
            line-height: 1.7;
        }

        .brand-link {
            display: inline-block;
            margin-top: 10px;
            color: var(--accent);
            font-weight: 700;
            text-decoration: none;
        }

        .divider {
            margin: 26px 0;
            border: 0;
            border-top: 1px dashed var(--paper-line);
        }

        .section-title {
            margin: 0 0 16px;
            text-align: center;
            color: var(--accent);
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            text-transform: uppercase;
        }

        .meta {
            display: grid;
            gap: 12px 18px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            margin-bottom: 18px;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            padding-bottom: 8px;
            border-bottom: 1px solid rgba(217, 225, 234, 0.85);
        }

        .meta-label {
            color: var(--muted);
            font-size: 0.9rem;
            font-weight: 700;
        }

        .meta-value {
            text-align: right;
            font-size: 1rem;
            font-weight: 800;
        }

        .status-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 16px;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 0.84rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .pill-accent {
            color: var(--accent);
            background: var(--accent-soft);
        }

        .pill-good {
            color: var(--good);
            background: var(--good-soft);
        }

        .items {
            width: 100%;
            border-collapse: collapse;
        }

        .items th,
        .items td {
            padding: 12px 0;
            border-bottom: 1px solid rgba(217, 225, 234, 0.85);
            vertical-align: top;
        }

        .items th {
            color: var(--accent);
            font-size: 0.92rem;
            font-weight: 800;
            text-align: left;
        }

        .items th:nth-child(2),
        .items td:nth-child(2) {
            width: 90px;
            text-align: center;
        }

        .items th:nth-child(3),
        .items td:nth-child(3) {
            width: 160px;
            text-align: right;
        }

        .item-name {
            margin: 0;
            font-size: 1.03rem;
            font-weight: 800;
        }

        .item-meta,
        .item-note {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 0.92rem;
            line-height: 1.6;
        }

        .summary {
            margin-top: 14px;
            margin-left: auto;
            width: min(280px, 100%);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            padding: 10px 0;
            border-bottom: 1px solid rgba(217, 225, 234, 0.85);
            font-weight: 700;
        }

        .summary-row.total {
            margin-top: 4px;
            color: var(--accent);
            font-size: 1.2rem;
            font-weight: 800;
        }

        .thank-you {
            margin: 28px 0 0;
            text-align: center;
            font-size: clamp(1.8rem, 5vw, 2.4rem);
            font-weight: 300;
            letter-spacing: -0.03em;
        }

        .verification {
            margin-top: 24px;
            padding: 18px 20px;
            border: 1px dashed rgba(29, 95, 184, 0.34);
            border-radius: 22px;
            background: rgba(232, 241, 255, 0.55);
            text-align: center;
        }

        .verification-label {
            margin: 0;
            color: var(--muted);
            font-size: 0.82rem;
            font-weight: 800;
            letter-spacing: 0.16em;
            text-transform: uppercase;
        }

        .verification-code {
            margin: 12px 0 0;
            font-size: clamp(1.1rem, 4vw, 1.5rem);
            font-weight: 900;
            letter-spacing: 0.28em;
        }

        .verification-note {
            margin: 12px auto 0;
            max-width: 440px;
            color: var(--muted);
            font-size: 0.92rem;
            line-height: 1.7;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 12px;
            margin-top: 18px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            padding: 0 18px;
            border: 1px solid transparent;
            border-radius: 16px;
            font-size: 0.94rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .button-primary {
            color: #ffffff;
            background: linear-gradient(135deg, #1d5fb8, #2d79df);
        }

        .button-secondary {
            color: var(--text);
            background: #ffffff;
            border-color: rgba(217, 225, 234, 0.95);
        }

        .footer-note {
            margin: 22px 0 0;
            text-align: center;
            color: var(--muted);
            font-size: 0.9rem;
            line-height: 1.7;
        }

        @media print {
            body {
                background: #ffffff;
            }

            .shell {
                width: 100%;
                margin: 0;
            }

            .receipt {
                border-radius: 0;
                box-shadow: none;
                border: 0;
            }

            .actions {
                display: none;
            }
        }

        @media (max-width: 640px) {
            .shell {
                width: min(100% - 14px, 100%);
                margin: 14px auto 24px;
            }

            .receipt-body {
                padding: 24px 18px 22px;
            }

            .meta {
                grid-template-columns: 1fr;
            }

            .meta-row {
                padding-bottom: 10px;
            }

            .meta-value {
                max-width: 58%;
            }

            .items th:nth-child(2),
            .items td:nth-child(2) {
                width: 62px;
            }

            .items th:nth-child(3),
            .items td:nth-child(3) {
                width: 120px;
            }

            .verification-code {
                letter-spacing: 0.16em;
            }

            .actions {
                flex-direction: column;
            }

            .button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    @php
        $paymentStatus = strtoupper((string) $reservation->payment_status);
        $paymentMethod = strtoupper((string) $reservation->payment_method);
        $dashboardUrl = auth()->check() && auth()->user()->isAdmin()
            ? route('admin.dashboard')
            : route('dashboard');
        $formattedVerificationCode = implode(' ', str_split($verificationCode, 4));
        $facebookUrl = config('services.facebook.page_url');
        $statusClass = strtolower((string) $reservation->payment_status) === 'paid'
            ? 'pill-good'
            : 'pill-accent';
        $amountLabel = strtolower((string) $reservation->payment_status) === 'paid'
            ? 'Total Paid'
            : 'Total Due';
        $rentalSummary = collect([
            ['singular' => 'new paddle', 'plural' => 'new paddles', 'quantity' => max(0, (int) ($reservation->new_paddle_rent_quantity ?? 0))],
            ['singular' => 'old paddle', 'plural' => 'old paddles', 'quantity' => max(0, (int) ($reservation->paddle_rent_quantity ?? 0))],
            ['singular' => 'ball', 'plural' => 'balls', 'quantity' => max(0, (int) ($reservation->ball_quantity ?? 0))],
        ])->filter(fn (array $item) => $item['quantity'] > 0);
        $rentalNote = $rentalSummary
            ->map(fn (array $item) => $item['quantity'] . ' ' . ($item['quantity'] === 1 ? $item['singular'] : $item['plural']))
            ->implode(' | ');
    @endphp

    <div class="shell">
        <section class="receipt">
            <div class="receipt-body">
                <header class="brand">
                    <x-application-logo class="logo" />
                    <h1 class="brand-title">Pickle BALLan ni Juan</h1>
                    <p class="brand-subtitle">
                        Official booking receipt for your court reservation.
                    </p>
                    <a href="{{ $facebookUrl }}" target="_blank" rel="noopener noreferrer" class="brand-link">
                        Facebook Page
                    </a>
                </header>

                <hr class="divider">

                <section>
                    <h2 class="section-title">Receipt</h2>

                    <div class="meta">
                        <div class="meta-row">
                            <span class="meta-label">Receipt No.</span>
                            <span class="meta-value">{{ $reservation->receipt_no }}</span>
                        </div>

                        <div class="meta-row">
                            <span class="meta-label">Date</span>
                            <span class="meta-value">{{ $reservation->created_at?->format('m/d/Y') ?? now()->format('m/d/Y') }}</span>
                        </div>

                        <div class="meta-row">
                            <span class="meta-label">Customer</span>
                            <span class="meta-value">{{ $reservation->customer_name }}</span>
                        </div>

                        <div class="meta-row">
                            <span class="meta-label">Payment</span>
                            <span class="meta-value">{{ $paymentMethod }}</span>
                        </div>
                    </div>

                    <div class="status-row">
                        <span class="pill {{ $statusClass }}">{{ $paymentStatus }}</span>
                        <span class="pill pill-accent">{{ $reservation->booking_date->format('F d, Y') }}</span>
                    </div>

                    <table class="items">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <p class="item-name">Court reservation</p>
                                    <p class="item-meta">
                                        {{ $courtLabel }} | {{ $reservation->timeRangeLabel() }} | {{ $reservation->durationLabel() }} | {{ $reservation->players }} player{{ $reservation->players === 1 ? '' : 's' }}
                                    </p>
                                    @if($rentalNote !== '')
                                        <p class="item-note">Extras: {{ $rentalNote }}</p>
                                    @endif
                                </td>
                                <td>1</td>
                                <td>PHP {{ number_format((float) $reservation->amount, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="summary">
                        <div class="summary-row total">
                            <span>{{ $amountLabel }}</span>
                            <span>PHP {{ number_format((float) $reservation->amount, 2) }}</span>
                        </div>
                    </div>
                </section>

                <p class="thank-you">Thank you for playing!</p>

                <section class="verification">
                    <p class="verification-label">Verification Code</p>
                    <p class="verification-code">{{ $formattedVerificationCode }}</p>
                    <p class="verification-note">
                        Open the official verification page before accepting a screenshot or printed copy.
                    </p>

                    <div class="actions">
                        <a href="{{ $verificationLookupUrl }}" class="button button-primary">Verify This Receipt</a>
                        <button type="button" class="button button-secondary" onclick="window.print()">Print Receipt</button>
                        <a href="{{ $dashboardUrl }}" class="button button-secondary">Back to Dashboard</a>
                    </div>
                </section>

                <p class="footer-note">
                    Keep this receipt for support and booking confirmation.
                </p>
            </div>
        </section>
    </div>
</body>
</html>
