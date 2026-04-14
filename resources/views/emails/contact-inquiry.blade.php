<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>New Contact Inquiry</title>
</head>
<body style="margin:0;padding:0;background-color:#f8fafc;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f8fafc;padding:40px 0;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;">

                    <!-- Logo / Header -->
                    <tr>
                        <td align="center" style="padding-bottom:32px;">
                            <p style="margin:0;font-size:20px;font-weight:700;color:#1e3a5f;letter-spacing:1px;">
                                LIGHTHOUSE <span style="color:#dc2626;">PRINTING SOLUTIONS</span>
                            </p>
                        </td>
                    </tr>

                    <!-- Card -->
                    <tr>
                        <td style="background-color:#ffffff;border-radius:16px;border:1px solid #e5e7eb;padding:40px;">

                            <p style="margin:0 0 4px;font-size:22px;font-weight:700;color:#111827;">
                                New Inquiry Received
                            </p>
                            <p style="margin:0 0 28px;font-size:14px;color:#6b7280;">
                                Someone submitted the contact form on your website.
                            </p>

                            <hr style="border:none;border-top:1px solid #f3f4f6;margin:0 0 24px;" />

                            <!-- Fields -->
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding-bottom:16px;">
                                        <p style="margin:0 0 4px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:#9ca3af;">Name</p>
                                        <p style="margin:0;font-size:15px;color:#111827;font-weight:600;">{{ $name }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom:16px;">
                                        <p style="margin:0 0 4px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:#9ca3af;">Contact (Phone / Email)</p>
                                        <p style="margin:0;font-size:15px;color:#111827;">{{ $contact }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom:16px;">
                                        <p style="margin:0 0 4px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:#9ca3af;">Service Interested In</p>
                                        <p style="margin:0;font-size:15px;color:#111827;">{{ $service }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom:0;">
                                        <p style="margin:0 0 4px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:#9ca3af;">Message</p>
                                        <p style="margin:0;font-size:15px;color:#111827;line-height:1.6;white-space:pre-wrap;">{{ $body }}</p>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="padding-top:24px;">
                            <p style="margin:0;font-size:12px;color:#9ca3af;">
                                &copy; {{ date('Y') }} Lighthouse Printing Solutions, Yangon, Myanmar.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>