<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Your Account Credentials</title>
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
                                Welcome to {{ $companyName }}!
                            </p>
                            <p style="margin:0 0 24px;font-size:15px;color:#6b7280;line-height:1.6;">
                                Hi {{ $name }}, your account has been created. Use the credentials below to sign in.
                            </p>

                            <hr style="border:none;border-top:1px solid #f3f4f6;margin:0 0 24px;" />

                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
                                <tr>
                                    <td style="padding:10px 14px;background-color:#f9fafb;border-radius:8px 8px 0 0;border:1px solid #e5e7eb;border-bottom:none;">
                                        <p style="margin:0;font-size:11px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:0.05em;">Email</p>
                                        <p style="margin:4px 0 0;font-size:15px;color:#111827;font-weight:500;">{{ $email }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 14px;background-color:#f9fafb;border-radius:0 0 8px 8px;border:1px solid #e5e7eb;">
                                        <p style="margin:0;font-size:11px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:0.05em;">Password</p>
                                        <p style="margin:4px 0 0;font-size:15px;color:#111827;font-weight:500;font-family:monospace;">{{ $password }}</p>
                                    </td>
                                </tr>
                            </table>

                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $verifyUrl }}"
                                           style="display:inline-block;background-color:#4f46e5;color:#ffffff;font-size:15px;font-weight:600;text-decoration:none;padding:14px 32px;border-radius:10px;">
                                            Verify Email &amp; Sign In
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:16px 0 0;font-size:13px;color:#9ca3af;text-align:center;">
                                This link expires in 24 hours.
                            </p>

                            <hr style="border:none;border-top:1px solid #f3f4f6;margin:24px 0;" />

                            <p style="margin:0;font-size:13px;color:#6b7280;">
                                If the button doesn't work, copy and paste this link into your browser:
                            </p>
                            <p style="margin:8px 0 0;font-size:12px;word-break:break-all;">
                                <a href="{{ $verifyUrl }}" style="color:#4f46e5;text-decoration:none;">{{ $verifyUrl }}</a>
                            </p>

                        </td>
                    </tr>

                    @include('emails.partials.footer')

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
