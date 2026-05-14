<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Payment Receipt — {{ $payment->invoice->invoice_no }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f8fafc;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f8fafc;padding:32px 0;">
    <tr>
        <td align="center">
            <table width="100%" cellpadding="0" cellspacing="0" style="max-width:680px;">

                <!-- Header: Logo + Company Info -->
                <tr>
                    <td style="padding-bottom:24px;">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="vertical-align:top;">
                                    <table cellpadding="0" cellspacing="0" style="margin-bottom:8px;">
                                        <tr>
                                            <td style="vertical-align:middle;padding-right:10px;">
                                                <img src="{{ asset('images/logo.png') }}" alt="{{ $companyName }}" height="44" style="display:block;" />
                                            </td>
                                            <td style="vertical-align:middle;">
                                                <p style="margin:0;font-size:15px;font-weight:800;color:#111827;">{{ $companyName }}</p>
                                            </td>
                                        </tr>
                                    </table>
                                    <table cellpadding="0" cellspacing="0" style="margin-top:6px;">
                                        <tr>
                                            <td style="padding:2px 6px 2px 0;vertical-align:middle;">
                                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                                            </td>
                                            <td style="font-size:11px;color:#6b7280;padding:2px 0;">{{ $companyAddress }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding:2px 6px 2px 0;vertical-align:middle;">
                                                <svg width="11" height="11" viewBox="0 0 24 24" fill="#9ca3af"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                            </td>
                                            <td style="font-size:11px;color:#6b7280;padding:2px 0;">{{ $companyFacebook }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding:2px 6px 2px 0;vertical-align:middle;">
                                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                                            </td>
                                            <td style="font-size:11px;color:#6b7280;padding:2px 0;">{{ $companyPhone }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding:2px 6px 2px 0;vertical-align:middle;">
                                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                                            </td>
                                            <td style="font-size:11px;color:#6b7280;padding:2px 0;">{{ $companyEmail }}</td>
                                        </tr>
                                    </table>
                                </td>
                                <td style="vertical-align:top;text-align:right;">
                                    <p style="margin:0;font-size:24px;font-weight:700;color:#059669;letter-spacing:2px;">RECEIPT</p>
                                    <p style="margin:4px 0 0;font-size:13px;font-weight:600;font-family:monospace;color:#374151;">{{ $payment->invoice->invoice_no }}</p>
                                    <p style="margin:4px 0 0;font-size:11px;color:#6b7280;">Date: {{ $payment->payment_date?->format('d M Y') }}</p>
                                    <p style="margin:4px 0 0;font-size:11px;color:#6b7280;">Created by: {{ $payment->createdBy?->name ?? '—' }}</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Card -->
                <tr>
                    <td style="background-color:#ffffff;border-radius:16px;border:1px solid #e5e7eb;padding:32px;">

                        <!-- Bill To + Summary -->
                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
                            <tr>
                                <td style="vertical-align:top;width:50%;">
                                    <p style="margin:0 0 6px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#9ca3af;">Bill To</p>
                                    <p style="margin:0;font-size:14px;font-weight:600;color:#111827;">{{ $payment->invoice->customer->name }}</p>
                                    @if($payment->invoice->customer->companyProfile?->name)
                                    <p style="margin:3px 0 0;font-size:12px;color:#374151;">{{ $payment->invoice->customer->companyProfile->name }}</p>
                                    @endif
                                    <p style="margin:3px 0 0;font-size:11px;color:#6b7280;">{{ $payment->invoice->customer->email }}</p>
                                </td>
                                <td style="vertical-align:top;width:50%;">
                                    <p style="margin:0 0 6px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#9ca3af;">Invoice Reference</p>
                                    <table width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="font-size:12px;color:#6b7280;padding:2px 0;">Invoice No</td>
                                            <td style="font-size:12px;color:#374151;text-align:right;padding:2px 0;font-family:monospace;font-weight:600;">{{ $payment->invoice->invoice_no }}</td>
                                        </tr>
                                        <tr>
                                            <td style="font-size:13px;font-weight:700;color:#059669;padding:6px 0 0;border-top:1px solid #e5e7eb;">Amount Paid</td>
                                            <td style="font-size:13px;font-weight:700;color:#059669;text-align:right;padding:6px 0 0;border-top:1px solid #e5e7eb;">{{ number_format($payment->amount) }}</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <hr style="border:none;border-top:1px solid #f3f4f6;margin:0 0 20px;" />

                        <!-- Payment Details -->
                        <p style="margin:0 0 10px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#9ca3af;">Payment Details</p>
                        <table width="100%" cellpadding="0" cellspacing="0" style="font-size:12px;">
                            <thead>
                                <tr style="background-color:#f9fafb;">
                                    <th style="padding:8px 10px;text-align:left;font-size:10px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #e5e7eb;">Type</th>
                                    <th style="padding:8px 10px;text-align:left;font-size:10px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #e5e7eb;">Bank</th>
                                    <th style="padding:8px 10px;text-align:left;font-size:10px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #e5e7eb;">Stage</th>
                                    <th style="padding:8px 10px;text-align:right;font-size:10px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #e5e7eb;">Amount</th>
                                    <th style="padding:8px 10px;text-align:left;font-size:10px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #e5e7eb;">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="padding:10px;color:#374151;border-bottom:1px solid #f3f4f6;">{{ $payment->type_id == 1 ? 'Cash' : ($payment->type_id == 2 ? 'Bank' : 'Other') }}</td>
                                    <td style="padding:10px;color:#374151;border-bottom:1px solid #f3f4f6;">{{ $payment->bank?->name ?? '—' }}</td>
                                    <td style="padding:10px;color:#374151;border-bottom:1px solid #f3f4f6;">{{ $payment->stage == 1 ? 'Advance / Deposit' : 'Final Payment' }}</td>
                                    <td style="padding:10px;color:#111827;font-weight:600;text-align:right;border-bottom:1px solid #f3f4f6;">{{ number_format($payment->amount) }}</td>
                                    <td style="padding:10px;color:#6b7280;border-bottom:1px solid #f3f4f6;">{{ $payment->payment_date?->format('d M Y') }}</td>
                                </tr>
                            </tbody>
                        </table>

                        @if($payment->note)
                        <p style="margin:16px 0 0;font-size:12px;color:#6b7280;background:#f9fafb;padding:10px 14px;border-radius:8px;">
                            <strong style="color:#374151;">Note:</strong> {{ $payment->note }}
                        </p>
                        @endif

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td align="center" style="padding-top:24px;">
                        <p style="margin:0;font-size:12px;color:#9ca3af;">&copy; {{ date('Y') }} {{ $companyName }}. All rights reserved.</p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
