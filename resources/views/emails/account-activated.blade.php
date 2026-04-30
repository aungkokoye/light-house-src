<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Account Activated</title>
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
                                Your account is now active!
                            </p>
                            <p style="margin:0 0 24px;font-size:15px;color:#6b7280;line-height:1.6;">
                                Hi {{ $name }}, your account has been approved by our team. You can now sign in to Light House Printing Solutions.
                            </p>

                            <!-- Divider -->
                            <hr style="border:none;border-top:1px solid #f3f4f6;margin:0 0 24px;" />

                            <!-- Button -->
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center">
                                        @if($isGoogleUser)
                                        <a href="{{ url('/auth/google/redirect') }}"
                                           style="display:inline-block;background-color:#ffffff;color:#374151;font-size:15px;font-weight:600;text-decoration:none;padding:14px 32px;border-radius:10px;border:1px solid #d1d5db;">
                                            <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google" width="18" height="18" style="vertical-align:middle;margin-right:8px;" />
                                            <span style="vertical-align:middle;">Sign in with Google</span>
                                        </a>
                                        @else
                                        <a href="{{ url('/login') }}"
                                           style="display:inline-block;background-color:#4f46e5;color:#ffffff;font-size:15px;font-weight:600;text-decoration:none;padding:14px 32px;border-radius:10px;">
                                            Sign in to your account
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            <!-- Divider -->
                            <hr style="border:none;border-top:1px solid #f3f4f6;margin:24px 0;" />

                            <p style="margin:0;font-size:13px;color:#6b7280;">
                                If the button doesn't work, copy and paste this link into your browser:
                            </p>
                            <p style="margin:8px 0 0;font-size:12px;word-break:break-all;">
                                @if($isGoogleUser)
                                <a href="{{ url('/auth/google/redirect') }}" style="color:#4f46e5;text-decoration:none;">{{ url('/auth/google/redirect') }}</a>
                                @else
                                <a href="{{ url('/login') }}" style="color:#4f46e5;text-decoration:none;">{{ url('/login') }}</a>
                                @endif
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="padding-top:24px;">
                            <p style="margin:0;font-size:12px;color:#9ca3af;">
                                &copy; {{ date('Y') }} Light House. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
