<x-app-layout>
    <x-slot name="title">マイページ</x-slot>

    <div class="max-w-4xl mx-auto px-6 py-12">

        <div style="margin-bottom:3rem;">
            <p style="font-size:0.65rem; letter-spacing:0.3em; color:#8A9899; margin-bottom:0.5rem; text-transform:uppercase;">My Page</p>
            <h1 style="font-family:'Noto Serif JP', serif; font-size:1.75rem; font-weight:300; color:#2E3A3B;">こんにちは、{{ auth()->user()->name }} さん</h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">

            {{-- Subscription Status Card --}}
            <div class="md:col-span-2" style="background:#fff; border:1px solid #E8E4DC; border-radius:4px; padding:2rem;">
                <h2 style="font-family:'Noto Serif JP', serif; font-size:1rem; font-weight:400; color:#2E3A3B; margin-bottom:1.5rem; padding-bottom:0.75rem; border-bottom:1px solid #E8E4DC;">定期便のステータス</h2>

                @if($subscription && $subscription->active())
                    <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:1.5rem;">
                        <div style="width:10px; height:10px; background:#4A5859; border-radius:50%;"></div>
                        <span style="font-size:0.85rem; color:#4A5859; font-weight:500;">定期便 配送中</span>
                    </div>

                    @if($nextBillingDate)
                    <div style="background:#F5F5F0; border-radius:4px; padding:1.25rem; margin-bottom:1.5rem;">
                        <p style="font-size:0.7rem; letter-spacing:0.1em; color:#8A9899; margin-bottom:0.3rem;">次回配送・請求予定日</p>
                        <p style="font-size:1.3rem; font-family:'Noto Serif JP', serif; color:#2E3A3B;">{{ $nextBillingDate->format('Y年m月d日') }}</p>
                        @php $daysLeft = now()->diffInDays($nextBillingDate, false); @endphp
                        <p style="font-size:0.75rem; color:#8A9899; margin-top:0.3rem;">あと {{ $daysLeft }} 日</p>
                    </div>
                    @endif

                    {{-- Skip Button --}}
                    <div style="border:1px solid #E8E4DC; border-radius:4px; padding:1.25rem; margin-bottom:1rem;">
                        <h3 style="font-size:0.85rem; font-weight:500; color:#2E3A3B; margin-bottom:0.5rem;">今月のスキップ</h3>
                        <p style="font-size:0.75rem; color:#8A9899; margin-bottom:1rem; line-height:1.7;">
                            旅行中や在庫が残っているときは、配送を1ヶ月スキップできます。<br>配送予定日の3日前まで受付可能です。
                        </p>
                        @if($canSkip)
                            <form method="POST" action="{{ route('mypage.skip') }}" onsubmit="return confirm('今月の配送を1ヶ月スキップしますか？')">
                                @csrf
                                <button type="submit" style="background:transparent; border:1px solid #4A5859; color:#4A5859; padding:0.75rem 1.5rem; font-size:0.8rem; letter-spacing:0.1em; border-radius:2px; cursor:pointer; transition:all .2s; font-family:'Noto Sans JP', sans-serif;" onmouseover="this.style.background='#4A5859'; this.style.color='#fff'" onmouseout="this.style.background='transparent'; this.style.color='#4A5859'">
                                    1ヶ月スキップする
                                </button>
                            </form>
                        @else
                            <p style="font-size:0.8rem; color:#B0BFBF; display:flex; align-items:center; gap:0.5rem;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                配送予定日が近いためスキップできません（3日前まで受付）
                            </p>
                        @endif
                    </div>

                    <a href="#" style="font-size:0.75rem; color:#c0392b; text-decoration:none;">定期便を解約する</a>

                @else
                    <div style="text-align:center; padding:2rem 0;">
                        <p style="font-size:0.85rem; color:#8A9899; margin-bottom:1.5rem;">現在、有効な定期便はありません。</p>
                        <a href="{{ route('diagnosis') }}" style="display:inline-block; background:#4A5859; color:#fff; padding:0.875rem 2rem; font-size:0.8rem; letter-spacing:0.1em; text-decoration:none; border-radius:2px;">肌質診断からはじめる</a>
                    </div>
                @endif
            </div>

            {{-- Quick Links --}}
            <div style="display:flex; flex-direction:column; gap:1rem;">
                <a href="{{ route('profile.edit') }}" style="background:#fff; border:1px solid #E8E4DC; border-radius:4px; padding:1.25rem 1.5rem; text-decoration:none; display:flex; align-items:center; gap:1rem; transition:border-color .2s;" onmouseover="this.style.borderColor='#4A5859'" onmouseout="this.style.borderColor='#E8E4DC'">
                    <svg width="20" height="20" fill="none" stroke="#4A5859" stroke-width="1.2" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <div>
                        <p style="font-size:0.85rem; color:#2E3A3B; font-weight:500;">アカウント設定</p>
                        <p style="font-size:0.7rem; color:#8A9899; margin-top:0.2rem;">プロフィール・メールアドレス変更</p>
                    </div>
                </a>
                <a href="{{ route('diagnosis') }}" style="background:#fff; border:1px solid #E8E4DC; border-radius:4px; padding:1.25rem 1.5rem; text-decoration:none; display:flex; align-items:center; gap:1rem; transition:border-color .2s;" onmouseover="this.style.borderColor='#4A5859'" onmouseout="this.style.borderColor='#E8E4DC'">
                    <svg width="20" height="20" fill="none" stroke="#4A5859" stroke-width="1.2" viewBox="0 0 24 24"><path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    <div>
                        <p style="font-size:0.85rem; color:#2E3A3B; font-weight:500;">肌質診断</p>
                        <p style="font-size:0.7rem; color:#8A9899; margin-top:0.2rem;">再診断・おすすめを更新</p>
                    </div>
                </a>
                <a href="{{ route('products.index') }}" style="background:#fff; border:1px solid #E8E4DC; border-radius:4px; padding:1.25rem 1.5rem; text-decoration:none; display:flex; align-items:center; gap:1rem; transition:border-color .2s;" onmouseover="this.style.borderColor='#4A5859'" onmouseout="this.style.borderColor='#E8E4DC'">
                    <svg width="20" height="20" fill="none" stroke="#4A5859" stroke-width="1.2" viewBox="0 0 24 24"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <div>
                        <p style="font-size:0.85rem; color:#2E3A3B; font-weight:500;">商品を追加</p>
                        <p style="font-size:0.7rem; color:#8A9899; margin-top:0.2rem;">定期便アイテムをカスタマイズ</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- Diagnosis History --}}
        @if($diagnoses->count() > 0)
        <div style="background:#fff; border:1px solid #E8E4DC; border-radius:4px; padding:2rem;">
            <h2 style="font-family:'Noto Serif JP', serif; font-size:1rem; font-weight:400; color:#2E3A3B; margin-bottom:1.5rem; padding-bottom:0.75rem; border-bottom:1px solid #E8E4DC;">過去の診断履歴</h2>
            <div style="display:flex; flex-direction:column; gap:1rem;">
                @foreach($diagnoses as $diagnosis)
                <div style="display:flex; align-items:center; justify-content:space-between; padding:1rem; background:#F5F5F0; border-radius:4px; flex-wrap:wrap; gap:1rem;">
                    <div style="display:flex; align-items:center; gap:1rem;">
                        <span style="display:inline-block; padding:0.3rem 0.75rem; border:1px solid #4A5859; border-radius:100px; font-size:0.75rem; color:#4A5859; white-space:nowrap;">{{ $diagnosis->skin_type_label }}</span>
                        <span style="font-size:0.75rem; color:#8A9899;">{{ $diagnosis->created_at->format('Y年m月d日') }}</span>
                    </div>
                    <a href="{{ route('products.index') }}?skin_type={{ $diagnosis->skin_type }}" style="font-size:0.75rem; color:#4A5859; text-decoration:none;">この肌タイプの商品を見る →</a>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
