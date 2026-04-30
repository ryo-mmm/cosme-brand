<x-mail::message>
# お支払いに失敗しました

{{ $user->name }} 様

大変恐れ入りますが、定期便のお支払い処理に失敗しました。

**請求金額:** ¥{{ number_format($amountDue / 100) }}
**試行回数:** {{ $attemptCount }} 回目

お支払い方法をご確認のうえ、マイページよりカード情報を更新してください。

<x-mail::button :url="config('app.url') . '/mypage'">
マイページで確認する
</x-mail::button>

ご不明な点がございましたら、お気軽にお問い合わせください。

{{ config('app.name') }}
</x-mail::message>
