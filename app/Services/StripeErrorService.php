<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\CardException;

class StripeErrorService
{
    private static array $messages = [
        // カード拒否
        'card_declined'           => 'カードが拒否されました。別のカードをお試しください。',
        'do_not_honor'            => 'カードが拒否されました。カード発行元にお問い合わせください。',
        'do_not_try_again'        => 'カードが拒否されました。別のカードをご利用ください。',
        'fraudulent'              => 'このカードは現在ご利用いただけません。',
        'lost_card'               => 'このカードは使用できません。別のカードをお使いください。',
        'stolen_card'             => 'このカードは使用できません。別のカードをお使いください。',
        'pickup_card'             => 'このカードは使用できません。カード発行元にお問い合わせください。',
        'restricted_card'         => 'このカードはオンライン決済に対応していません。',
        'card_velocity_exceeded'  => 'カードの利用制限に達しました。しばらく経ってから再度お試しください。',

        // 残高・有効期限・番号
        'insufficient_funds'      => '残高が不足しています。',
        'expired_card'            => 'カードの有効期限が切れています。',
        'incorrect_cvc'           => 'セキュリティコード（CVC）が正しくありません。',
        'incorrect_number'        => 'カード番号が正しくありません。',
        'invalid_number'          => 'カード番号が無効です。',
        'invalid_expiry_month'    => '有効期限（月）が正しくありません。',
        'invalid_expiry_year'     => '有効期限（年）が正しくありません。',

        // 処理エラー
        'processing_error'        => '決済処理中にエラーが発生しました。しばらく経ってから再度お試しください。',
        'authentication_required' => '追加認証が必要です。カード発行元にお問い合わせください。',
        'currency_not_supported'  => 'このカードは円建て決済に対応していません。',
        'duplicate_transaction'   => '同じ取引が短時間に複数回送信されました。しばらく経ってから再度お試しください。',
    ];

    private static string $defaultMessage = '決済処理中にエラーが発生しました。カード情報をご確認のうえ、もう一度お試しください。';

    public static function toJapanese(ApiErrorException $e): string
    {
        // CardException はまず decline_code を優先
        if ($e instanceof CardException) {
            $declineCode = $e->getDeclineCode() ?? '';
            if (isset(self::$messages[$declineCode])) {
                return self::$messages[$declineCode];
            }
        }

        $stripeCode = $e->getStripeCode() ?? '';

        return self::$messages[$stripeCode] ?? self::$defaultMessage;
    }

    public static function fromCode(string $code): string
    {
        return self::$messages[$code] ?? self::$defaultMessage;
    }

    // ─── 構造化ログ ───────────────────────────────────────────────────────────

    /**
     * Stripe 例外を構造化ログに記録する。
     *
     * ログレベルの使い分け:
     *   warning — CardException（ユーザー起因、管理者対応不要）
     *   error   — ApiErrorException（Stripe API / 設定起因）
     *
     * 記録しない情報: カード番号・CVV・有効期限・メールアドレス等の個人情報。
     *
     * @param ApiErrorException $e
     * @param array             $context  user_id / action 等の追加フィールド
     */
    public static function log(ApiErrorException $e, array $context = []): void
    {
        $logData = array_merge(self::safeStripeFields($e), $context);

        if ($e instanceof CardException) {
            // カード拒否はユーザー操作起因。管理者エスカレーション不要。
            Log::warning('stripe.card_error', $logData);
        } elseif ($e->getHttpStatus() >= 500) {
            // Stripe サーバー障害 → エラー通知対象
            Log::error('stripe.server_error', $logData);
        } else {
            // 400 系: 設定・パラメータ起因
            Log::error('stripe.api_error', $logData);
        }
    }

    /**
     * 例外から安全なフィールドのみ抽出（機密情報を除外）。
     *
     * @return array<string, mixed>
     */
    private static function safeStripeFields(ApiErrorException $e): array
    {
        $fields = [
            'stripe_error_code' => $e->getStripeCode(),
            'stripe_request_id' => $e->getRequestId(),
            'http_status'       => $e->getHttpStatus(),
            'error_type'        => $e->getError()?->type,
        ];

        if ($e instanceof CardException) {
            $fields['decline_code'] = $e->getDeclineCode();
            // CardException のメッセージはカード関連の技術情報を含む可能性があるため省略
        } else {
            // API エラーは診断に有用なためメッセージを含める（Stripe のメッセージに PII は含まれない）
            $fields['stripe_message'] = mb_substr((string) $e->getMessage(), 0, 200);
        }

        return $fields;
    }
}
