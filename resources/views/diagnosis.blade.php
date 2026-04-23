<x-app-layout>
    <x-slot name="title">肌質診断</x-slot>

    {{-- Hero --}}
    <section style="background:#fff; padding:4rem 1.5rem 3rem; text-align:center; border-bottom:1px solid #E8E4DC;">
        <p style="font-size:0.65rem; letter-spacing:0.3em; color:#8A9899; margin-bottom:1rem; text-transform:uppercase;">Skin Diagnosis</p>
        <h1 style="font-family:'Noto Serif JP', serif; font-size:clamp(1.5rem, 3vw, 2.2rem); font-weight:300; color:#2E3A3B; margin-bottom:1rem;">無料 肌質診断</h1>
        <p style="font-size:0.85rem; color:#5A6B6C; line-height:1.9; max-width:500px; margin:0 auto;">
            7つの質問に答えるだけで、あなたの肌タイプを診断。<br>最適なオーガニックコスメセットをご提案します。所要時間：約2分
        </p>
    </section>

    {{-- Vue.js Diagnosis App --}}
    <section style="padding:3rem 1.5rem 6rem;">
        <div id="skin-diagnosis-app"></div>
    </section>
</x-app-layout>
