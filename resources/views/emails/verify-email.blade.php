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
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:680px;">

                    @include('emails.partials.header')

                    <!-- Card -->
                    <tr>
                        <td style="background-color:#ffffff;border-radius:16px;border:1px solid #e5e7eb;padding:40px;">

                            <p style="margin:0 0 8px;font-size:22px;font-weight:700;color:#111827;">
                                Verify your email address
                            </p>
                            <p style="margin:0 0 24px;font-size:15px;color:#6b7280;line-height:1.6;">
                                Hi {{ $name }}, thanks for signing up! Please confirm your email address to activate your account.
                            </p>

                            <hr style="border:none;border-top:1px solid #f3f4f6;margin:0 0 24px;" />

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

                            <p style="margin:24px 0 0;font-size:13px;color:#9ca3af;text-align:center;">
                                This link expires in 24 hours.
                            </p>

                            <hr style="border:none;border-top:1px solid #f3f4f6;margin:24px 0;" />

                            <p style="margin:0;font-size:13px;color:#6b7280;">
                                If the button doesn't work, copy and paste this link into your browser:
                            </p>
                            <p style="margin:8px 0 0;font-size:12px;word-break:break-all;">
                                <a href="{{ $url }}" style="color:#4f46e5;text-decoration:none;">{{ $url }}</a>
                            </p>

                        </td>
                    </tr>

                    @include('emails.partials.footer')

                    <tr>
                        <td align="center" style="padding-top:8px;">
                            <p style="margin:0;font-size:12px;color:#d1d5db;">
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
