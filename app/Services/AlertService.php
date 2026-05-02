<?php

namespace App\Services;

use App\Mail\AdminAlertMail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * 致命的なエラーを管理者に通知するサービス。
 *
 * 設定方法（.env）:
 *   SLACK_ALERT_WEBHOOK_URL=https://hooks.slack.com/services/xxx
 *   ADMIN_EMAIL=admin@example.com
 *
 * どちらも未設定の場合は critical ログのみ記録する。
 */
class AlertService
{
    /**
     * 致命的エラーを管理者に通知する。
     *
     * @param string $title   通知タイトル（例: "Stripe API サーバー障害"）
     * @param array  $context 追加コンテキスト（user_id, action 等）
     */
    public static function critical(string $title, array $context = []): void
    {
        $payload = array_merge($context, [
            'app_env'     => app()->environment(),
            'occurred_at' => now()->toIso8601String(),
        ]);

        // 1. アプリケーションログ（常に記録）
        Log::critical($title, $payload);

        // 2. Slack Incoming Webhook（SLACK_ALERT_WEBHOOK_URL 設定時のみ）
        self::notifySlack($title, $payload);

        // 3. 管理者メール（ADMIN_EMAIL 設定時のみ）
        self::notifyEmail($title, $payload);
    }

    private static function notifySlack(string $title, array $context): void
    {
        $webhookUrl = config('services.slack.alert_webhook');
        if (!$webhookUrl) {
            return;
        }

        try {
            $env    = $context['app_env'] ?? app()->environment();
            $fields = [];
            foreach ($context as $key => $value) {
                // 内部フィールドは Slack 表示から除く
                if (in_array($key, ['app_env', 'occurred_at'], true)) {
                    continue;
                }
                $fields[] = [
                    'title' => $key,
                    'value' => is_array($value) ? json_encode($value) : (string) $value,
                    'short' => true,
                ];
            }

            Http::timeout(5)->post($webhookUrl, [
                'attachments' => [[
                    'color'      => 'danger',
                    'title'      => "[{$env}] :rotating_light: {$title}",
                    'fields'     => $fields,
                    'footer'     => config('app.url'),
                    'footer_icon' => 'https://platform.slack-edge.com/img/default_application_icon.png',
                    'ts'         => now()->timestamp,
                ]],
            ]);
        } catch (\Exception $e) {
            // 通知失敗はサービス継続を妨げない
            Log::error('AlertService: Slack 通知失敗', ['error' => $e->getMessage()]);
        }
    }

    private static function notifyEmail(string $title, array $context): void
    {
        $adminEmail = config('subscription.admin_email');
        if (!$adminEmail) {
            return;
        }

        try {
            Mail::to($adminEmail)->queue(new AdminAlertMail($title, $context));
        } catch (\Exception $e) {
            Log::error('AlertService: 管理者メール送信失敗', ['error' => $e->getMessage()]);
        }
    }
}
