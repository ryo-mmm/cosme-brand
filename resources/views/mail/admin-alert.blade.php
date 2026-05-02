<x-mail::message>
# :rotating_light: {{ $alertTitle }}

**環境:** {{ app()->environment() }}
**発生日時:** {{ now()->format('Y-m-d H:i:s T') }}

---

## コンテキスト

<x-mail::table>
| フィールド | 値 |
|:---|:---|
@foreach($context as $key => $value)
| {{ $key }} | {{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value }} |
@endforeach
</x-mail::table>

---

このメールは自動送信されています。
{{ config('app.name') }} システム

</x-mail::message>
