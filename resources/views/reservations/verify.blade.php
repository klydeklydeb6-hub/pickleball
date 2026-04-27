<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt Verification | Pickle BALLan ni Juan</title>
    <style>
        :root {
            --bg: #eff6ff;
            --panel: rgba(255, 255, 255, 0.95);
            --text: #0f172a;
            --muted: #475569;
            --line: #dbeafe;
            --brand: #1d4ed8;
            --brand-soft: #e0ebff;
            --ok: #166534;
            --ok-soft: #dcfce7;
            --bad: #991b1b;
            --bad-soft: #fee2e2;
            --shadow: 0 24px 60px rgba(15, 23, 42, 0.12);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Sora", "Segoe UI", sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(29, 78, 216, 0.12), transparent 32%),
                linear-gradient(180deg, #f8fbff 0%, var(--bg) 100%);
        }

        .page {
            width: min(980px, calc(100% - 32px));
            margin: 34px auto;
        }

        .card {
            border: 1px solid var(--line);
            border-radius: 28px;
            background: var(--panel);
            box-shadow: var(--shadow);
        }

        .hero {
            padding: 28px;
        }

        .eyebrow {
            margin: 0 0 10px;
            color: var(--brand);
            font-size: 0.76rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.24em;
        }

        h1 {
            margin: 0;
            font-size: clamp(2rem, 4vw, 3rem);
            line-height: 0.96;
            letter-spacing: -0.04em;
        }

        .lead {
            max-width: 720px;
            margin: 14px 0 0;
            color: var(--muted);
            font-size: 0.98rem;
            line-height: 1.8;
        }

        .content {
            display: grid;
            gap: 18px;
            grid-template-columns: 0.95fr 1.05fr;
            margin-top: 18px;
        }

        .panel {
            padding: 24px;
        }

        .field {
            margin-bottom: 16px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.84rem;
            font-weight: 700;
            color: var(--muted);
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        input {
            width: 100%;
            min-height: 52px;
            padding: 0 16px;
            border: 1px solid #cbd5e1;
            border-radius: 16px;
            font-size: 1rem;
            font-family: inherit;
            color: var(--text);
            background: #ffffff;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 50px;
            padding: 0 18px;
            border: none;
            border-radius: 16px;
            background: linear-gradient(135deg, #1d4ed8, #0f766e);
            color: #ffffff;
            font-size: 0.94rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .helper {
            margin-top: 14px;
            color: var(--muted);
            font-size: 0.9rem;
            line-height: 1.7;
        }

        .status {
            padding: 22px;
            border-radius: 24px;
        }

        .status-idle {
            background: linear-gradient(180deg, #f8fbff 0%, #eff6ff 100%);
            border: 1px solid var(--line);
        }

        .status-valid {
            background: linear-gradient(180deg, #f7fff9 0%, #ecfdf3 100%);
            border: 1px solid #bbf7d0;
        }

        .status-invalid {
            background: linear-gradient(180deg, #fff7f7 0%, #fff1f2 100%);
            border: 1px solid #fecdd3;
        }

        .status-kicker {
            margin: 0 0 8px;
            font-size: 0.76rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.2em;
        }

        .status-valid .status-kicker,
        .status-valid h2 {
            color: var(--ok);
        }

        .status-invalid .status-kicker,
        .status-invalid h2 {
            color: var(--bad);
        }

        .status-idle .status-kicker,
        .status-idle h2 {
            color: var(--brand);
        }

        .status h2 {
            margin: 0;
            font-size: 1.5rem;
            letter-spacing: -0.03em;
        }

        .status p {
            margin: 12px 0 0;
            color: var(--muted);
            line-height: 1.75;
        }

        .detail-grid {
            display: grid;
            gap: 12px;
            margin-top: 18px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .detail-card {
            padding: 14px 16px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.88);
            border: 1px solid rgba(255, 255, 255, 0.9);
        }

        .detail-label {
            margin: 0;
            color: var(--muted);
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.16em;
        }

        .detail-value {
            margin: 8px 0 0;
            font-size: 0.98rem;
            font-weight: 700;
            line-height: 1.55;
        }

        .link-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .link-muted {
            color: var(--text);
            background: #ffffff;
            border: 1px solid #cbd5e1;
        }

        @media (max-width: 860px) {
            .content {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .page {
                width: min(100% - 16px, 100%);
                margin: 10px auto 24px;
            }

            .hero,
            .panel {
                padding: 20px;
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }

            .link-row {
                flex-direction: column;
            }

            .button,
            .link-muted {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <section class="card hero">
            <p class="eyebrow">Official Verification Portal</p>
            <h1>Validate a receipt against the live booking record.</h1>
            <p class="lead">
                Enter the receipt number and verification code exactly as shown on the receipt. Only receipts that match the live record should be accepted.
            </p>
        </section>

        <section class="content">
            <div class="card panel">
                <form method="GET" action="{{ route('reservations.receipt.verify') }}">
                    <div class="field">
                        <label for="receipt_no">Receipt Number</label>
                        <input id="receipt_no" name="receipt_no" type="text" value="{{ $lookupReceiptNo }}" placeholder="Example: RCPT-2026-0001" required>
                    </div>

                    <div class="field">
                        <label for="verification_code">Verification Code</label>
                        <input id="verification_code" name="verification_code" type="text" value="{{ $lookupVerificationCode }}" placeholder="Example: A1B2 C3D4 E5F6" required>
                    </div>

                    <button class="button" type="submit">Verify Receipt</button>
                </form>

                <p class="helper">
                    Do not approve entry or payment claims from a screenshot alone. Always compare the receipt number and verification code with this live verification result.
                </p>

                <div class="link-row">
                    <a href="{{ route('reservations.index') }}" class="button link-muted">Open Booking Page</a>
                </div>
            </div>

            <div class="card panel">
                @if($verificationStatus === 'valid' && $reservation)
                    <div class="status status-valid">
                        <p class="status-kicker">Verified</p>
                        <h2>Receipt Verified</h2>
                        <p>
                            This receipt matches a live reservation record. The details below are the current official booking details stored by the system.
                        </p>

                        <div class="detail-grid">
                            <div class="detail-card">
                                <p class="detail-label">Receipt Number</p>
                                <p class="detail-value">{{ $reservation->receipt_no }}</p>
                            </div>

                            <div class="detail-card">
                                <p class="detail-label">Customer</p>
                                <p class="detail-value">{{ $reservation->customer_name }}</p>
                            </div>

                            <div class="detail-card">
                                <p class="detail-label">Booking Date</p>
                                <p class="detail-value">{{ $reservation->booking_date->format('F d, Y') }}</p>
                            </div>

                            <div class="detail-card">
                                <p class="detail-label">Schedule</p>
                                <p class="detail-value">{{ $reservation->timeRangeLabel() }}</p>
                            </div>

                            <div class="detail-card">
                                <p class="detail-label">Duration</p>
                                <p class="detail-value">{{ $reservation->durationLabel() }}</p>
                            </div>

                            <div class="detail-card">
                                <p class="detail-label">Court</p>
                                <p class="detail-value">{{ $courtLabel }}</p>
                            </div>

                            <div class="detail-card">
                                <p class="detail-label">Players</p>
                                <p class="detail-value">{{ $reservation->players }}</p>
                            </div>

                            <div class="detail-card">
                                <p class="detail-label">Payment Status</p>
                                <p class="detail-value">{{ $reservation->payment_status }}</p>
                            </div>

                            <div class="detail-card">
                                <p class="detail-label">Amount</p>
                                <p class="detail-value">PHP {{ number_format((float) $reservation->amount, 2) }}</p>
                            </div>
                        </div>
                    </div>
                @elseif($verificationStatus === 'invalid')
                    <div class="status status-invalid">
                        <p class="status-kicker">Invalid</p>
                        <h2>Receipt Not Verified</h2>
                        <p>
                            The receipt number and verification code do not match a valid live reservation record. This receipt should not be accepted until it is confirmed directly in the system.
                        </p>
                    </div>
                @else
                    <div class="status status-idle">
                        <p class="status-kicker">Ready</p>
                        <h2>Waiting for Verification</h2>
                        <p>
                            Use this page to check whether a receipt is legitimate. Once verified, the live reservation details will appear here.
                        </p>
                    </div>
                @endif
            </div>
        </section>
    </div>
</body>
</html>
