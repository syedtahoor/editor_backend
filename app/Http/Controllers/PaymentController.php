<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;
use App\Mail\PaymentNotificationMail;

class PaymentController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create a payment intent for donation
     */
    public function createPaymentIntent(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:1',
                'currency' => 'sometimes|string|size:3',
            ]);

            $amount = $request->amount;
            $currency = $request->currency ?? 'usd';

            // Convert to cents for Stripe
            $amountInCents = (int) ($amount * 100);

            $paymentIntent = PaymentIntent::create([
                'amount' => $amountInCents,
                'currency' => $currency,
                'metadata' => [
                    'donation_amount' => $amount,
                ],
            ]);

            return response()->json([
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
            ]);

        } catch (ApiErrorException $e) {
            return response()->json([
                'error' => 'Payment intent creation failed',
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Server error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Confirm payment and handle success
     */
    public function confirmPayment(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'payment_intent_id' => 'required|string',
            ]);

            $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);

            if ($paymentIntent->status === 'succeeded') {
                // Send email notification to admin
                try {
                    $emailData = [
                        'amount' => $paymentIntent->metadata['donation_amount'] ?? '0',
                        'payment_intent_id' => $paymentIntent->id,
                        'timestamp' => now()->format('d M Y, h:i A'),
                    ];

                    // Send email to admin
                    Mail::to(env('MAIL_ADMIN_TO'))->send(new PaymentNotificationMail($emailData));
                } catch (\Exception $emailError) {
                    // Log email error but don't fail the payment
                    Log::error('Failed to send payment notification email: ' . $emailError->getMessage());
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Payment successful!',
                    'payment_intent' => $paymentIntent,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not completed',
                    'status' => $paymentIntent->status,
                ], 400);
            }

        } catch (ApiErrorException $e) {
            return response()->json([
                'error' => 'Payment confirmation failed',
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Server error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get Stripe publishable key for frontend
     */
    public function getPublishableKey(): JsonResponse
    {
        return response()->json([
            'publishable_key' => config('services.stripe.key'),
        ]);
    }
}
