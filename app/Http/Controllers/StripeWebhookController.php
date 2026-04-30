<?php

namespace App\Http\Controllers;

use App\Mail\PaymentFailedMail;
use App\Mail\SubscriptionCancelledMail;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;

class StripeWebhookController extends CashierWebhookController
{
    public function handleInvoicePaymentFailed(array $payload): void
    {
        $stripeCustomerId = $payload['data']['object']['customer'] ?? null;

        if (!$stripeCustomerId) {
            return;
        }

        $user = User::where('stripe_id', $stripeCustomerId)->first();

        if (!$user) {
            return;
        }

        $amountDue    = (int) ($payload['data']['object']['amount_due'] ?? 0);
        $attemptCount = (int) ($payload['data']['object']['attempt_count'] ?? 1);

        Log::warning('Stripe invoice.payment_failed', [
            'user_id'       => $user->id,
            'email'         => $user->email,
            'invoice_id'    => $payload['data']['object']['id'] ?? null,
            'amount_due'    => $amountDue,
            'attempt_count' => $attemptCount,
        ]);

        Mail::to($user)->queue(new PaymentFailedMail($user, $amountDue, $attemptCount));
    }

    public function handleCustomerSubscriptionDeleted(array $payload): void
    {
        parent::handleCustomerSubscriptionDeleted($payload);

        $stripeCustomerId = $payload['data']['object']['customer'] ?? null;

        if (!$stripeCustomerId) {
            return;
        }

        $user = User::where('stripe_id', $stripeCustomerId)->first();

        if (!$user) {
            return;
        }

        Log::info('Stripe customer.subscription.deleted', [
            'user_id'         => $user->id,
            'email'           => $user->email,
            'subscription_id' => $payload['data']['object']['id'] ?? null,
            'cancel_reason'   => $payload['data']['object']['cancellation_details']['reason'] ?? 'unknown',
        ]);

        Mail::to($user)->queue(new SubscriptionCancelledMail($user));
    }
}
