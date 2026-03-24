<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Verify your email</title>
</head>
<body style="margin:0;padding:0;background-color:#f8fafc;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f8fafc;padding:40px 0;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;">

                    <!-- Logo -->
                    <tr>
                        <td align="center" style="padding-bottom:32px;">
                            <table cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="background-color:#4f46e5;border-radius:10px;width:36px;height:36px;text-align:center;vertical-align:middle;">
                                        <span style="color:#ffffff;font-size:18px;font-weight:bold;line-height:36px;">&#9788;</span>
                                    </td>
                                    <td style="padding-left:10px;font-size:18px;font-weight:600;color:#111827;vertical-align:middle;">
                                        Light House
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Card -->
                    <tr>
                        <td style="background-color:#ffffff;border-radius:16px;border:1px solid #e5e7eb;padding:40px;">

                            <!-- Heading -->
                            <p style="margin:0 0 8px;font-size:22px;font-weight:700;color:#111827;">
                                Verify your email address
                            </p>
                            <p style="margin:0 0 24px;font-size:15px;color:#6b7280;line-height:1.6;">
                                Hi {{ $name }}, thanks for signing up! Please confirm your email address to activate your account.
                            </p>

                            <!-- Divider -->
                            <hr style="border:none;border-top:1px solid #f3f4f6;margin:0 0 24px;" />

                            <!-- Button -->
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $url }}"
                                           style="display:inline-block;background-color:#4f46e5;color:#ffffff;font-size:15px;font-weight:600;text-decoration:none;padding:14px 32px;border-radius:10px;">
                                            Verify email address
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Expiry note -->
                            <p style="margin:24px 0 0;font-size:13px;color:#9ca3af;text-align:center;">
                                This link expires in 24 hours.
                            </p>

                            <!-- Divider -->
                            <hr style="border:none;border-top:1px solid #f3f4f6;margin:24px 0;" />

                            <!-- Fallback URL -->
                            <p style="margin:0;font-size:13px;color:#6b7280;">
                                If the button doesn't work, copy and paste this link into your browser:
                            </p>
                            <p style="margin:8px 0 0;font-size:12px;word-break:break-all;">
                                <a href="{{ $url }}" style="color:#4f46e5;text-decoration:none;">{{ $url }}</a>
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="padding-top:24px;">
                            <p style="margin:0;font-size:12px;color:#9ca3af;">
                                &copy; {{ date('Y') }} Light House. All rights reserved.
                            </p>
                            <p style="margin:4px 0 0;font-size:12px;color:#d1d5db;">
                                If you didn't create an account, you can safely ignore this email.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>