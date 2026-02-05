@php
    $greeting = 'Hello ' . ($employeeName ?: 'there') . ',';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Credentials</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; background-color: #f6f7fb; margin: 0; padding: 24px; color: #1f1f1f;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb;">
        <tr>
            <td style="padding: 24px; background: #0d47a1; color: #ffffff;">
                <h2 style="margin: 0; font-size: 20px;">Your {{ strtoupper($accountType) }} Account is Ready</h2>
                <p style="margin: 8px 0 0; font-size: 14px; opacity: 0.85;">Access the portal and verify the information below.</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 24px;">
                <p style="margin-top: 0;">{{ $greeting }}</p>
                <p>We created a new {{ strtoupper($accountType) }} account in the HR system. Use the credentials below to sign in.</p>

                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin: 18px 0; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; width: 160px;">Full Name</td>
                        <td>{{ $employeeName }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold;">Email</td>
                        <td>{{ $email }}</td>
                    </tr>
                    @if($department)
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold;">Department</td>
                        <td>{{ $department }}</td>
                    </tr>
                    @endif
                    @if($position)
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold;">Position</td>
                        <td>{{ $position }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold;">Temporary Password</td>
                        <td><strong>{{ $password }}</strong></td>
                    </tr>
                </table>
                <p style="font-size: 14px; color: #4b5563;">For security, please change your password right after logging in.</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 16px 24px; background: #f1f5f9; font-size: 13px; color: #6b7280;">
                <p style="margin: 0;">If you did not expect this email, please contact HR immediately.</p>
            </td>
        </tr>
    </table>
</body>
</html>
