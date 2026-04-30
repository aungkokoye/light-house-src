<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Account Deactivated</title>
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
                                Your account has been deactivated
                            </p>
                            <p style="margin:0 0 24px;font-size:15px;color:#6b7280;line-height:1.6;">
                                Hi {{ $name }}, your Light House account has been deactivated by an administrator. You will no longer be able to sign in.
                            </p>

                            <!-- Divider -->
                            <hr style="border:none;border-top:1px solid #f3f4f6;margin:0 0 24px;" />

                            <p style="margin:0;font-size:14px;color:#6b7280;line-height:1.6;">
                                If you believe this was done in error, please contact our support team for assistance.
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
