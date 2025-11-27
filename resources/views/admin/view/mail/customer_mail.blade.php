<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inquiry Received - ELEVORCE</title>
</head>
<body style="font-family: Arial, sans-serif; background-color:#f4f4f4; padding:20px; margin:0;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0"
           style="max-width:600px; margin:auto; background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.1);">

        <!-- Header with Logo -->
        <tr>
            <td style="background:#2c3e50; padding:20px; text-align:center;">
               <img src="{{ $message->embed(public_path('images/logo/temp_logo.png')) }}"
                    alt="Elevorce Logo"
                    style="max-height:50px; margin-bottom:10px;">
                <h2 style="margin:0; color:#fff; font-size:22px; letter-spacing:1px;">ELEVORCE</h2>
            </td>
        </tr>
        <!-- Body -->
        <tr>
            <td style="padding:25px; color:#333; line-height:1.6;">
                <h3 style="margin-top:0; color:#2c3e50;">Hello {{ $customer_name }},</h3>

                <p>Thank you for reaching out to <strong>Elevorce</strong>.</p>
                <p>We have successfully received your inquiry and our support team will review it shortly.</p>

                <!-- Inquiry Details -->
                <div style="margin:20px 0; padding:15px; background:#f9f9f9; border-left:4px solid #2c3e50; border-radius:4px;">
                    <h4 style="margin:0 0 10px; color:#2c3e50;">🔹 Inquiry Details</h4>
                    <p style="margin:5px 0;"><strong>Subject:</strong> {{ $inquiry_subject }}</p>
                    <p style="margin:5px 0;"><strong>Submitted on:</strong> {{ $date_time }}</p>
                </div>

                <p>Our support team will get back to you within
                    <strong>24–48 business hours</strong>.
                </p>
                <p>If your inquiry is urgent, please contact us directly at:
                    <a href="mailto:{{ $support_contact }}" style="color:#2c3e50; font-weight:bold; text-decoration:none;">
                        {{ $support_contact }}
                    </a>
                </p>

                <p style="margin-top:30px;">We appreciate your patience and look forward to assisting you.</p>
                <p style="margin:0;">Best Regards,<br>
                <strong>Team Elevorce</strong></p>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="background:#2c3e50; padding:15px; text-align:center; font-size:12px; color:#ddd;">
                © {{ date('Y') }} Elevorce. All rights reserved.
            </td>
        </tr>
    </table>
</body>
</html>
