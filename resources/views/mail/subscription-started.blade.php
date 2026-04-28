<x-mail::message>
# 定期便のお申し込みが完了しました

{{ $user->name }} 様

LUMIÈRE BOTANIQUE 定期便のお申し込みありがとうございます。
お申し込みが正常に完了いたしました。

初回のお届けは **5〜7営業日以内** を予定しております。

<x-mail::panel>
**定期便について**
- 毎月自動でお届け・自動決済いたします
- マイページよりいつでも配送のスキップ・解約が可能です
- 次回配送予定日はマイページよりご確認ください
</x-mail::panel>

<x-mail::button :url="route('mypage')" color="primary">
マイページで確認する
</x-mail::button>

---

ご不明な点はお気軽にお問い合わせください。
受付時間：平日 10:00〜17:00
info@lumiere-botanique.jp

{{ config('app.name') }}
</x-mail::message>
