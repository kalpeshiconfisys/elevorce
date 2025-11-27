<?php

namespace App\Http\Controllers\API;

use App\Events\NewContactUs;
use App\Http\Controllers\Controller;

use App\Models\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Locale;
use ResourceBundle;


class FrontendController extends Controller
{

   public function contactUs(Request $request)
    {



        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable',
            'subject' => 'required|string',
            'message' => 'required|string',
            // 'g-recaptcha-response' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        // $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
        //     'secret' => env('RECAPTCHA_SECRET'),
        //     'response' => $request->input('g-recaptcha-response'),
        // ]);

        // $captchaResult = $response->json();
        // if (!isset($captchaResult['success']) || !$captchaResult['success']) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'reCAPTCHA verification failed.',
        //     ], 400);
        // }

        $contactUs = ContactUs::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'phone'        => $request->phone,
            'subject'      => $request->subject,
            'message'      => $request->message,
            'company_name' => $request->company,
        ]);

        $customerData = [
            'customer_name'   => $request->name,
            'inquiry_subject' => $request->subject,
            'date_time'       => now()->format('d M Y, h:i A'),
            'support_contact' => 'contact@elevorce.com'
        ];

        $adminData = [
            'customer_name'    => $request->name,
            'customer_email'   => $request->email,
            'customer_phone'   => $request->phone,
            'customer_company' => $request->company ?? 'N/A',
            'inquiry_subject'  => $request->subject,
            'customer_message' => $request->message,
            'date_time'        => now()->format('d M Y, h:i A')
        ];

        // Mail::send('admin.view.mail.customer_mail', $customerData, function ($message) use ($request) {
        //     $message->to($request->email)->subject('Thank you for contacting Elevorce – We’ve received your inquiry');
        // });

        Mail::send('admin.view.mail.admin_mail', $adminData, function ($message) use ($request) {
            $message->to($request->email)->subject('New Inquiry Received – ' . $request->name);
        });

        event(new NewContactUs($contactUs->toArray()));

        return $this->success(true, 'Message sent successfully.');
    }

}
