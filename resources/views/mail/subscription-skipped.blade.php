<x-mail::message>
# 配送スキップを受け付けました

{{ $user->name }} 様

今月の配送スキップを受け付けました。

<x-mail::panel>
**次回配送・請求予定日**
{{ $newDate->format('Y年m月d日') }}
</x-mail::panel>

スキップ後の配送スケジュールはマイページよりご確認いただけます。

<x-mail::button :url="route('mypage')" color="primary">
マイページで確認する
</x-mail::button>

---

ご不明な点はお気軽にお問い合わせください。
受付時間：平日 10:00〜17:00
info@lumiere-botanique.jp

{{ config('app.name') }}
</x-mail::message>
