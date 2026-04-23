<x-app-layout>
    <x-slot name="title">定期便 申し込み</x-slot>

    <div class="max-w-4xl mx-auto px-6 py-12">
        <div style="text-align:center; margin-bottom:3rem;">
            <p style="font-size:0.65rem; letter-spacing:0.3em; color:#8A9899; margin-bottom:1rem; text-transform:uppercase;">Checkout</p>
            <h1 style="font-family:'Noto Serif JP', serif; font-size:clamp(1.5rem, 3vw, 2rem); font-weight:300; color:#2E3A3B;">定期便 お申し込み</h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-12">

            {{-- Order Summary --}}
            <div class="md:col-span-2">
                <h2 style="font-family:'Noto Serif JP', serif; font-size:1rem; font-weight:400; color:#2E3A3B; margin-bottom:1.5rem; padding-bottom:0.75rem; border-bottom:1px solid #E8E4DC;">ご注文内容</h2>
                @forelse($products as $product)
                <div style="display:flex; gap:1rem; margin-bottom:1.5rem; padding-bottom:1.5rem; border-bottom:1px solid #F0EDE6;">
                    <div style="width:60px; height:60px; background:#F0EDE6; border-radius:4px; flex-shrink:0; display:flex; align-items:center; justify-content:center;">
                        <svg width="24" height="24" fill="none" stroke="#C4A882" stroke-width="1" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <div style="flex:1;">
                        <p style="font-size:0.85rem; color:#2E3A3B; margin-bottom:0.25rem;">{{ $product->name }}</p>
                        <p style="font-size:0.75rem; color:#8A9899;">定期便（毎月お届け）</p>
                        <p style="font-size:0.9rem; color:#4A5859; margin-top:0.25rem;">¥{{ number_format($product->subscription_price) }}/月</p>
                    </div>
                </div>
                @empty
                <p style="font-size:0.85rem; color:#8A9899;">商品が選択されていません。</p>
                @endforelse

                @if($products->count() > 0)
                <div style="background:#F5F5F0; border-radius:4px; padding:1.25rem; margin-top:1rem;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:0.5rem;">
                        <span style="font-size:0.8rem; color:#5A6B6C;">小計</span>
                        <span style="font-size:0.8rem; color:#4A5859;">¥{{ number_format($products->sum('subscription_price')) }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:0.75rem;">
                        <span style="font-size:0.8rem; color:#5A6B6C;">送料</span>
                        <span style="font-size:0.8rem; color:#4A5859;">無料</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding-top:0.75rem; border-top:1px solid #E8E4DC;">
                        <span style="font-size:0.9rem; color:#2E3A3B; font-weight:500;">合計（月額）</span>
                        <span style="font-size:1rem; color:#4A5859; font-weight:500;">¥{{ number_format($products->sum('subscription_price')) }}</span>
                    </div>
                </div>
                <div style="margin-top:1rem; padding:1rem; border:1px solid #4A5859; border-radius:4px;">
                    <p style="font-size:0.7rem; color:#4A5859; line-height:1.8;">
                        ✓ 毎月自動でお届け<br>
                        ✓ 配送日の変更・スキップが可能<br>
                        ✓ いつでも解約できます
                    </p>
                </div>
                @endif
            </div>

            {{-- Payment Form --}}
            <div class="md:col-span-3">
                <h2 style="font-family:'Noto Serif JP', serif; font-size:1rem; font-weight:400; color:#2E3A3B; margin-bottom:1.5rem; padding-bottom:0.75rem; border-bottom:1px solid #E8E4DC;">お支払い情報</h2>

                @guest
                <div style="background:#F5F5F0; border:1px solid #E8E4DC; border-radius:4px; padding:2rem; text-align:center; margin-bottom:2rem;">
                    <p style="font-size:0.85rem; color:#5A6B6C; margin-bottom:1.5rem;">お申し込みにはアカウントが必要です。</p>
                    <div style="display:flex; gap:1rem; justify-content:center; flex-wrap:wrap;">
                        <a href="{{ route('register') }}" style="background:#4A5859; color:#fff; padding:0.75rem 1.5rem; font-size:0.8rem; letter-spacing:0.1em; text-decoration:none; border-radius:2px;">新規会員登録</a>
                        <a href="{{ route('login') }}" style="border:1px solid #4A5859; color:#4A5859; padding:0.75rem 1.5rem; font-size:0.8rem; letter-spacing:0.1em; text-decoration:none; border-radius:2px;">ログイン</a>
                    </div>
                </div>
                @endguest

                @auth
                <form id="checkout-form" method="POST" action="{{ route('checkout.process') }}">
                    @csrf
                    @foreach($products as $product)
                        <input type="hidden" name="products[]" value="{{ $product->id }}">
                    @endforeach
                    <input type="hidden" name="payment_method" id="payment-method-input" value="">

                    {{-- Delivery address --}}
                    <div style="margin-bottom:2rem;">
                        <h3 style="font-size:0.85rem; font-weight:500; color:#2E3A3B; margin-bottom:1rem; letter-spacing:0.05em;">配送先住所</h3>
                        <div style="display:grid; gap:1rem;">
                            <div>
                                <label style="display:block; font-size:0.75rem; color:#8A9899; margin-bottom:0.4rem; letter-spacing:0.05em;">郵便番号</label>
                                <input type="text" name="postal_code" placeholder="000-0000" required style="width:100%; padding:0.75rem 1rem; border:1px solid #D8D4CC; border-radius:2px; font-size:0.85rem; background:#fff; outline:none; transition:border .2s;" onfocus="this.style.borderColor='#4A5859'" onblur="this.style.borderColor='#D8D4CC'">
                            </div>
                            <div>
                                <label style="display:block; font-size:0.75rem; color:#8A9899; margin-bottom:0.4rem; letter-spacing:0.05em;">都道府県・市区町村・番地</label>
                                <input type="text" name="address" placeholder="東京都渋谷区..." required style="width:100%; padding:0.75rem 1rem; border:1px solid #D8D4CC; border-radius:2px; font-size:0.85rem; background:#fff; outline:none; transition:border .2s;" onfocus="this.style.borderColor='#4A5859'" onblur="this.style.borderColor='#D8D4CC'">
                            </div>
                        </div>
                    </div>

                    {{-- Card (Stripe Elements placeholder) --}}
                    <div style="margin-bottom:2rem;">
                        <h3 style="font-size:0.85rem; font-weight:500; color:#2E3A3B; margin-bottom:1rem; letter-spacing:0.05em;">クレジットカード情報</h3>
                        <div style="border:1px solid #D8D4CC; border-radius:2px; padding:0.875rem 1rem; background:#fff; min-height:50px;" id="card-element">
                            {{-- Stripe Elements がここにマウントされます --}}
                            <p style="font-size:0.8rem; color:#B0BFBF; display:flex; align-items:center; gap:0.5rem;">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                                Stripe決済（実環境では Stripe.js が読み込まれます）
                            </p>
                        </div>
                        <p id="card-error" style="font-size:0.75rem; color:#c0392b; margin-top:0.5rem;"></p>
                    </div>

                    <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:2rem;">
                        <svg width="14" height="14" fill="none" stroke="#4A5859" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        <p style="font-size:0.75rem; color:#8A9899;">SSL暗号化通信で安全に決済されます。カード情報はStripeが管理し、当サイトに保存されません。</p>
                    </div>

                    <button type="submit" id="submit-btn" style="width:100%; background:#4A5859; color:#fff; padding:1rem; font-size:0.85rem; letter-spacing:0.15em; border:none; border-radius:2px; cursor:pointer; transition:opacity .2s;" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                        定期便に申し込む（¥{{ number_format($products->sum('subscription_price')) }}/月）
                    </button>

                    <p style="font-size:0.75rem; color:#8A9899; text-align:center; margin-top:1rem; line-height:1.8;">
                        申し込みと同時に初回配送分の請求が行われます。<br>
                        以降は毎月同日に自動引き落としとなります。
                    </p>
                </form>
                @endauth
            </div>
        </div>
    </div>
</x-app-layout>
