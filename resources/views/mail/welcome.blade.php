<x-mail::message>
# ご登録ありがとうございます

{{ $user->name }} 様

LUMIÈRE BOTANIQUE へのご登録が完了いたしました。

30〜40代の働く女性のために、自然の力で肌本来の美しさを引き出すオーガニックコスメをお届けします。

まずは **肌質診断** を受けて、あなたに合ったスキンケアを見つけてみましょう。

<x-mail::button :url="route('diagnosis')" color="primary">
無料 肌質診断をはじめる
</x-mail::button>

---

ご不明な点はお気軽にお問い合わせください。
受付時間：平日 10:00〜17:00
info@lumiere-botanique.jp

{{ config('app.name') }}
</x-mail::message>
