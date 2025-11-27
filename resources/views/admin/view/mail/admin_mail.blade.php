<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Inquiry Notification - Elevorce</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f4f4f4; padding:20px; margin:0;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0"
           style="max-width:650px; margin:auto; background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.1);">

        <!-- Header -->
        <tr>
            <td style="background:#34495e; padding:20px; text-align:center;">
                <img src="{{ $message->embed(public_path('images/logo/temp_logo.png')) }}" alt="Elevorce Logo" style="max-height:50px; margin-bottom:8px;">
                <h2 style="margin:0; color:#fff; font-size:20px; letter-spacing:1px;">📩 New Inquiry Notification</h2>
            </td>
        </tr>

        <!-- Body -->
        <tr>
            <td style="padding:25px; color:#333; line-height:1.6;">
                <h3 style="margin-top:0; color:#2c3e50;">Hello Admin,</h3>
                <p>You have received a new inquiry from the <strong>Elevorce</strong> website. Please review the details below:</p>

                <!-- Inquiry Details -->
                <div style="margin:20px 0; padding:15px; background:#fafafa; border-left:4px solid #34495e; border-radius:4px;">
                    <h4 style="margin:0 0 10px; color:#2c3e50;">🔹 Inquiry Details</h4>
                    <p style="margin:6px 0;"><strong>Name:</strong> {{ $customer_name }}</p>
                    <p style="margin:6px 0;"><strong>Email:</strong> {{ $customer_email }}</p>
                    <p style="margin:6px 0;"><strong>Phone:</strong> {{ $customer_phone }}</p>
                    <p style="margin:6px 0;"><strong>Company:</strong> {{ $customer_company }}</p>
                    <p style="margin:6px 0;"><strong>Subject:</strong> {{ $inquiry_subject }}</p>
                    <p style="margin:6px 0; word-wrap:break-word; max-width:550px;">
                        <strong>Message:</strong><br>{{ $customer_message }}
                    </p>
                    <p style="margin:6px 0;"><strong>📅 Submitted On:</strong> {{ $date_time }}</p>
                </div>

                <p>Please login to the
                    <strong>Elevorce Admin Panel → Inquiries Section</strong>
                    to manage and respond to this inquiry.
                </p>

                <p style="margin-top:30px;">Best Regards,<br>
                <strong>Elevorce System Notification</strong></p>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="background:#34495e; padding:15px; text-align:center; font-size:12px; color:#ddd;">
                © {{ date('Y') }} Elevorce. All rights reserved.
            </td>
        </tr>
    </table>
</body>
</html>
