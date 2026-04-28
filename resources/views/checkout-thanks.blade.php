<x-app-layout>
    <x-slot name="title">お申し込み完了</x-slot>

    <div class="max-w-2xl mx-auto" style="padding: 6rem 1.5rem; text-align:center;">

        {{-- Icon --}}
        <div style="width:64px; height:64px; background:#F5F5F0; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 2rem;">
            <svg width="28" height="28" fill="none" stroke="#4A5859" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
            </svg>
        </div>

        <p style="font-size:0.65rem; letter-spacing:0.3em; color:#8A9899; margin-bottom:0.75rem; text-transform:uppercase;">Thank You</p>

        @if($type === 'subscription')
            <h1 style="font-family:'Noto Serif JP', serif; font-size:1.75rem; font-weight:300; color:#2E3A3B; margin-bottom:1.25rem;">
                定期便のお申し込みが<br>完了しました
            </h1>
            <p style="font-size:0.875rem; color:#8A9899; line-height:1.9; margin-bottom:2.5rem;">
                ご登録いただいたメールアドレスに確認メールをお送りしました。<br>
                初回のお届けは <strong style="color:#4A5859;">5〜7営業日以内</strong> を予定しております。
            </p>
            <div style="background:#F5F5F0; border-radius:4px; padding:1.5rem 2rem; margin-bottom:2.5rem; text-align:left;">
                <p style="font-size:0.7rem; letter-spacing:0.1em; color:#8A9899; margin-bottom:0.75rem; text-transform:uppercase;">定期便について</p>
                <ul style="font-size:0.82rem; color:#4A5859; line-height:2; list-style:none; padding:0; margin:0;">
                    <li>・ 毎月自動でお届け・自動決済いたします</li>
                    <li>・ マイページよりいつでも配送のスキップが可能です</li>
                    <li>・ 解約はマイページより次回発送日3日前までに受付</li>
                </ul>
            </div>
        @else
            <h1 style="font-family:'Noto Serif JP', serif; font-size:1.75rem; font-weight:300; color:#2E3A3B; margin-bottom:1.25rem;">
                ご購入ありがとうございます
            </h1>
            <p style="font-size:0.875rem; color:#8A9899; line-height:1.9; margin-bottom:2.5rem;">
                ご注文を承りました。<br>
                <strong style="color:#4A5859;">3〜5営業日以内</strong> に発送いたします。
            </p>
        @endif

        <div style="display:flex; flex-direction:column; gap:1rem; align-items:center;">
            <a href="{{ route('mypage') }}"
               style="display:inline-block; background:#4A5859; color:#fff; padding:1rem 2.5rem; font-size:0.85rem; letter-spacing:0.15em; text-decoration:none; border-radius:2px; transition:opacity .2s;"
               onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                マイページへ
            </a>
            <a href="{{ route('top') }}" style="font-size:0.8rem; color:#8A9899; text-decoration:none;">
                トップページへ戻る
            </a>
        </div>
    </div>
</x-app-layout>
