<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;

class StripeWebhookController extends CashierWebhookController
{
    /**
     * 決済失敗時：ユーザーにメール通知（将来的な拡張用）、ログ記録
     */
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

        Log::warning('Stripe invoice.payment_failed', [
            'user_id'       => $user->id,
            'email'         => $user->email,
            'invoice_id'    => $payload['data']['object']['id'] ?? null,
            'amount_due'    => $payload['data']['object']['amount_due'] ?? null,
            'attempt_count' => $payload['data']['object']['attempt_count'] ?? null,
        ]);

        // TODO: 決済失敗メール送信
        // Mail::to($user)->send(new PaymentFailedMail($payload['data']['object']));
    }

    /**
     * サブスクリプション強制削除時：DBのサブスクリプション状態を同期
     * Cashier の親実装でも subscription レコードは更新されるが、
     * 追加のビジネスロジックをここで実行する。
     */
    public function handleCustomerSubscriptionDeleted(array $payload): void
    {
        // まず親クラスの処理（Cashier の subscription レコード更新）を実行
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

        // TODO: 解約完了メール送信
        // Mail::to($user)->send(new SubscriptionCancelledMail());
    }
}
