<?php

namespace App\Http\Controllers;

use App\Mail\PaymentFailedMail;
use App\Mail\SubscriptionCancelledMail;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;

class StripeWebhookController extends CashierWebhookController
{
    public function handleChargeSucceeded(array $payload): void
    {
        $charge = $payload['data']['object'];
        $stripeCustomerId = $charge['customer'] ?? null;

        $user = $stripeCustomerId
            ? User::where('stripe_id', $stripeCustomerId)->first()
            : null;

        Order::firstOrCreate(
            ['stripe_charge_id' => $charge['id']],
            [
                'user_id'              => $user?->id,
                'stripe_invoice_id'    => $charge['invoice'] ?? null,
                'amount'               => (int) ($charge['amount'] ?? 0),
                'currency'             => $charge['currency'] ?? 'jpy',
                'status'               => 'succeeded',
                'description'          => $charge['description'] ?? null,
                'payment_method_type'  => $charge['payment_method_details']['type'] ?? null,
            ]
        );

        Log::info('Order created from charge.succeeded', [
            'stripe_charge_id' => $charge['id'],
            'user_id'          => $user?->id,
            'amount'           => $charge['amount'] ?? 0,
        ]);
    }

    public function handleChargeRefunded(array $payload): void
    {
        $charge = $payload['data']['object'];

        $updated = Order::where('stripe_charge_id', $charge['id'])
            ->update([
                'status'      => 'refunded',
                'refunded_at' => now(),
            ]);

        Log::info('Order marked refunded from charge.refunded', [
            'stripe_charge_id' => $charge['id'],
            'updated_rows'     => $updated,
        ]);
    }

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
