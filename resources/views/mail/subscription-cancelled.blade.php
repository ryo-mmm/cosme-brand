<x-mail::message>
# 定期便の解約を受け付けました

{{ $user->name }} 様

定期便の解約を受け付けました。

現在の請求期間終了日まではお届けを継続いたします。
期間終了後、自動的に配送・請求が停止されます。

<x-mail::panel>
またいつでもご利用いただけます。再開をご希望の際はサイトよりお申し込みください。
</x-mail::panel>

<x-mail::button :url="route('top')" color="primary">
サイトへ戻る
</x-mail::button>

---

ご利用いただきありがとうございました。
またのご利用をお待ちしております。

ご不明な点はお気軽にお問い合わせください。
受付時間：平日 10:00〜17:00
info@lumiere-botanique.jp

{{ config('app.name') }}
</x-mail::message>
