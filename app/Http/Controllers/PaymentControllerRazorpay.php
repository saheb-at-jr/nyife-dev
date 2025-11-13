<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\BalanceHistory;
use App\Models\Payment;

class PaymentControllerRazorpay extends Controller
{
    public function verify(Request $request)
    {
        $paymentId     = $request->input('paymentId');
        $signature     = $request->input('razorpay_signature');
        $paymentLinkId = $request->input('razorpay_payment_link_id');

        Log::info("Verifying Razorpay payment", [
            'paymentId' => $paymentId,
            'signature' => $signature,
            'paymentLinkId' => $paymentLinkId,
        ]);

        // Step 1: Verify signature only if provided
        if ($signature) {
            $generated_signature = hash_hmac(
                'sha256',
                $paymentLinkId . '|' . $paymentId,
                env('RAZORPAY_KEY_SECRET')
            );

            if (!hash_equals($generated_signature, $signature)) {
                return response()->json([
                    'status' => 'failed',
                    'reason' => 'Invalid signature',
                ]);
            }
        }

        // Step 2: Fetch payment details using Razorpay API
        $response = Http::withBasicAuth(
            env('RAZORPAY_KEY_ID'),
            env('RAZORPAY_KEY_SECRET')
        )->get("https://api.razorpay.com/v1/payments/{$paymentId}");

        if (!$response->successful()) {
            return response()->json([
                'status' => 'error',
                'reason' => 'Unable to fetch payment details',
            ]);
        }

        $payment = $response->json();
        $userId  = $payment['notes']['user_id'] ?? null;

        if ($payment['status'] !== 'captured') {
            return response()->json([
                'status' => 'failed',
                'reason' => $payment['status'],
            ]);
        }

        // ✅ Prevent duplicate processing
        $alreadyProcessed = Payment::where('payment_id', $payment['id'])->exists();
        if ($alreadyProcessed) {
            return response()->json([
                'status' => 'failed',
                'reason' => 'Payment already processed',
            ]);
        }

        $paid_amount = $payment['amount'] / 100;

        // Original paid amount
        $paid_amount = $request->paid_amount;
        
        // Calculate GST (18%) 
        $gstAmount = $paid_amount * 0.18;
        
        // Net amount to credit (after GST deduction)
        $netAmount = $paid_amount - $gstAmount;
        
        // Update user balance
        $user = User::findOrFail($userId);
        $user->balance += $netAmount;
        $user->save();
        
        // History entry for credited amount (after GST deduction)
        BalanceHistory::create([
            'user_id'       => $userId,
            'amount'        => $netAmount,
            'balance_after' => $user->balance,
            'type'          => 'credit',
            'note'          => 'Razorpay Payment Credited (after GST deduction)',
        ]);
        
        // History entry for GST deduction
        BalanceHistory::create([
            'user_id'       => $userId,
            'amount'        => -$gstAmount,
            'balance_after' => $user->balance, // remains same, no change to wallet
            'type'          => 'debit',
            'note'          => '18% GST Deduction from Razorpay Payment',
        ]);


        // Step 5: Save payment details
        Payment::create([
            'user_id'   => $userId,
            'payment_id'=> $payment['id'],
            'order_id'  => $payment['order_id'] ?? null,
            'amount'    => $paid_amount,
            'currency'  => $payment['currency'],
            'status'    => $payment['status'],
            'method'    => $payment['method'] ?? null,
            'notes'     => $payment['notes'] ?? [],
        ]);

        return response()->json([
            'status'   => 'success',
            'amount'   => $paid_amount,
            'currency' => $payment['currency'],
            'id'       => $payment['id'],
        ]);
    }
}
