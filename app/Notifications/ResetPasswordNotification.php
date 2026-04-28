<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = $this->resetUrl($notifiable);

        return (new MailMessage)
            ->subject('【LUMIÈRE BOTANIQUE】パスワードの再設定')
            ->greeting($notifiable->name . ' 様')
            ->line('パスワード再設定のリクエストを受け付けました。')
            ->action('パスワードを再設定する', $url)
            ->line('このリンクは ' . config('auth.passwords.' . config('auth.defaults.passwords') . '.expire') . ' 分間有効です。')
            ->line('パスワード再設定のリクエストをご自身でされていない場合は、このメールを無視してください。')
            ->salutation('LUMIÈRE BOTANIQUE');
    }
}
