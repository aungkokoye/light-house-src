<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Invoice {{ $invoice->invoice_no }}</title>
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
                                    <p style="margin:0;font-size:24px;font-weight:700;color:#4f46e5;letter-spacing:2px;">INVOICE</p>
                                    <p style="margin:4px 0 0;font-size:13px;font-weight:600;font-family:monospace;color:#374151;">{{ $invoice->invoice_no }}</p>
                                    <p style="margin:4px 0 0;font-size:11px;color:#6b7280;">Date: {{ $invoice->created_at->format('d M Y') }}</p>
                                    @if($invoice->createdBy)
                                    <p style="margin:2px 0 0;font-size:11px;color:#6b7280;">Created by: {{ $invoice->createdBy->name }}</p>
                                    @endif
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
                                    <p style="margin:0;font-size:14px;font-weight:600;color:#111827;">{{ $invoice->customer->name }}</p>
                                    @if($invoice->customer->company_name)
                                    <p style="margin:3px 0 0;font-size:12px;color:#374151;">{{ $invoice->customer->company_name }}</p>
                                    @endif
                                    <p style="margin:3px 0 0;font-size:11px;color:#6b7280;">{{ $invoice->customer->email }}</p>
                                </td>
                                <td style="vertical-align:top;width:50%;">
                                    <p style="margin:0 0 6px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#9ca3af;">Summary</p>
                                    <table width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="font-size:12px;color:#6b7280;padding:2px 0;">Subtotal</td>
                                            <td style="font-size:12px;color:#374151;text-align:right;padding:2px 0;">{{ number_format($invoice->jobs->sum('total')) }}</td>
                                        </tr>
                                        @if($invoice->discount > 0)
                                        <tr>
                                            <td style="font-size:12px;color:#6b7280;padding:2px 0;">Discount</td>
                                            <td style="font-size:12px;color:#374151;text-align:right;padding:2px 0;">- {{ number_format($invoice->discount) }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td style="font-size:13px;font-weight:700;color:#4f46e5;padding:6px 0 0;border-top:1px solid #e5e7eb;">Total</td>
                                            <td style="font-size:13px;font-weight:700;color:#4f46e5;text-align:right;padding:6px 0 0;border-top:1px solid #e5e7eb;">{{ number_format($invoice->total) }}</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <hr style="border:none;border-top:1px solid #f3f4f6;margin:0 0 20px;" />

                        <!-- Invoice Items -->
                        <p style="margin:0 0 10px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#9ca3af;">Invoice Items</p>
                        <table width="100%" cellpadding="0" cellspacing="0" style="font-size:12px;margin-bottom:24px;">
                            <thead>
                                <tr style="background-color:#f9fafb;">
                                    <th style="padding:8px 10px;text-align:left;font-size:10px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid #e5e7eb;">#</th>
                                    <th style="padding:8px 10px;text-align:left;font-size:10px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid #e5e7eb;">Product</th>
                                    <th style="padding:8px 10px;text-align:left;font-size:10px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid #e5e7eb;">Service</th>
                                    <th style="padding:8px 10px;text-align:right;font-size:10px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid #e5e7eb;">Qty</th>
                                    <th style="padding:8px 10px;text-align:right;font-size:10px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid #e5e7eb;">Unit Price</th>
                                    <th style="padding:8px 10px;text-align:left;font-size:10px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid #e5e7eb;">Delivery</th>
                                    <th style="padding:8px 10px;text-align:right;font-size:10px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid #e5e7eb;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->jobs as $i => $job)
                                <tr>
                                    <td style="padding:8px 10px;color:#9ca3af;border-bottom:1px solid #f3f4f6;">{{ $i + 1 }}</td>
                                    <td style="padding:8px 10px;color:#374151;border-bottom:1px solid #f3f4f6;">{{ $job->product?->name ?? '—' }}</td>
                                    <td style="padding:8px 10px;color:#374151;border-bottom:1px solid #f3f4f6;">{{ $job->service?->name ?? '—' }}</td>
                                    <td style="padding:8px 10px;color:#111827;text-align:right;border-bottom:1px solid #f3f4f6;">{{ $job->quantity }}</td>
                                    <td style="padding:8px 10px;color:#111827;text-align:right;border-bottom:1px solid #f3f4f6;">{{ number_format($job->unit_price) }}</td>
                                    <td style="padding:8px 10px;color:#6b7280;border-bottom:1px solid #f3f4f6;">{{ $job->delivery_date?->format('d M Y') }}</td>
                                    <td style="padding:8px 10px;color:#111827;font-weight:500;text-align:right;border-bottom:1px solid #f3f4f6;">{{ number_format($job->total) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        @if($invoice->note)
                        <p style="margin:0 0 24px;font-size:12px;color:#6b7280;background:#f9fafb;padding:10px 14px;border-radius:8px;">
                            <strong style="color:#374151;">Note:</strong> {{ $invoice->note }}
                        </p>
                        @endif

                        <hr style="border:none;border-top:1px solid #f3f4f6;margin:0 0 20px;" />

                        <!-- Payments -->
                        <p style="margin:0 0 10px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#9ca3af;">Payments</p>
                        <table width="100%" cellpadding="0" cellspacing="0" style="font-size:12px;">
                            <thead>
                                <tr style="background-color:#f9fafb;">
                                    <th style="padding:8px 10px;text-align:left;font-size:10px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #e5e7eb;">#</th>
                                    <th style="padding:8px 10px;text-align:left;font-size:10px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #e5e7eb;">Type</th>
                                    <th style="padding:8px 10px;text-align:left;font-size:10px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #e5e7eb;">Stage</th>
                                    <th style="padding:8px 10px;text-align:right;font-size:10px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #e5e7eb;">Amount</th>
                                    <th style="padding:8px 10px;text-align:left;font-size:10px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #e5e7eb;">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoice->payments as $i => $pmt)
                                <tr>
                                    <td style="padding:8px 10px;color:#9ca3af;border-bottom:1px solid #f3f4f6;">{{ $i + 1 }}</td>
                                    <td style="padding:8px 10px;color:#374151;border-bottom:1px solid #f3f4f6;">{{ $pmt->type_id == 1 ? 'Cash' : ($pmt->type_id == 2 ? 'Bank' : 'Other') }}{{ $pmt->bank ? ' — ' . $pmt->bank->name : '' }}</td>
                                    <td style="padding:8px 10px;color:#374151;border-bottom:1px solid #f3f4f6;">{{ $pmt->stage == 1 ? 'Advance' : 'Final' }}</td>
                                    <td style="padding:8px 10px;color:#111827;font-weight:500;text-align:right;border-bottom:1px solid #f3f4f6;">{{ number_format($pmt->amount) }}</td>
                                    <td style="padding:8px 10px;color:#6b7280;border-bottom:1px solid #f3f4f6;">{{ $pmt->payment_date?->format('d M Y') }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="5" style="padding:12px 10px;color:#9ca3af;text-align:center;">No payments yet.</td></tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr style="background-color:#f9fafb;">
                                    <td colspan="4" style="padding:8px 10px;text-align:right;font-size:12px;color:#6b7280;border-top:1px solid #e5e7eb;">Total Paid</td>
                                    <td style="padding:8px 10px;font-weight:600;color:#111827;border-top:1px solid #e5e7eb;">{{ number_format($invoice->payments->sum('amount')) }}</td>
                                </tr>
                                <tr style="background-color:#eef2ff;">
                                    <td colspan="4" style="padding:8px 10px;text-align:right;font-size:13px;font-weight:700;color:#374151;border-top:1px solid #e5e7eb;">Balance</td>
                                    <td style="padding:8px 10px;font-size:13px;font-weight:700;color:#4f46e5;border-top:1px solid #e5e7eb;">{{ number_format($invoice->total - $invoice->payments->sum('amount')) }}</td>
                                </tr>
                            </tfoot>
                        </table>

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
