<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{
    public function createPayment(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'currency' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $currency = $request->currency;
        $amount = $request->amount;
        
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $paymentIntent = PaymentIntent::create([
            'amount' => $amount * 100,
            'currency' => $currency,
            'payment_method_types' => ['card'],
        ]);

        $payment = Payment::create([
            'payment_intent_id' => $paymentIntent->id,
            'amount' => $amount,
            'currency' => $currency,
            'status' => 'pending',
        ]);

        return response()->json([
            'clientSecret' => $paymentIntent->client_secret,
            'payment_id' => $payment->id,
            'paymentIntentId' => $paymentIntent->id,
        ]);

    }

    public function markPaymentSuccess(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'status' => 'required',
            'payment_intent_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $payment = Payment::where('payment_intent_id', $request->payment_intent_id)->first();
        if (!$payment) {
            return $this->error('Payment not found');
        }
        $payment->update([
            'status' =>  $request->status,
        ]);

        return $this->success(true,'Payment status updated successfully',[]);

    }

}
