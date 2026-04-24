<x-app-layout>
    <x-slot name="title">診断結果 — {{ $diagnosis->skin_type_label }}</x-slot>

    <div class="max-w-4xl mx-auto px-6 py-12">

        {{-- Breadcrumb --}}
        <nav style="font-size:0.75rem; color:#8A9899; margin-bottom:3rem; display:flex; gap:0.5rem; align-items:center;">
            <a href="{{ route('top') }}" style="color:#8A9899; text-decoration:none;">TOP</a>
            <span>›</span>
            <a href="{{ route('diagnosis') }}" style="color:#8A9899; text-decoration:none;">肌質診断</a>
            <span>›</span>
            <span style="color:#4A5859;">診断結果</span>
        </nav>

        {{-- Result Hero --}}
        <div style="text-align:center; margin-bottom:4rem;">
            <p style="font-size:0.65rem; letter-spacing:0.3em; color:#8A9899; margin-bottom:1rem; text-transform:uppercase;">Diagnosis Result</p>
            <h1 style="font-family:'Noto Serif JP', serif; font-size:clamp(1.5rem, 3vw, 2rem); font-weight:300; color:#2E3A3B; margin-bottom:1.5rem;">
                あなたの肌タイプは
            </h1>
            <div style="display:inline-block; padding:0.75rem 2.5rem; border:1px solid #4A5859; border-radius:100px; font-family:'Noto Serif JP', serif; font-size:1.5rem; font-weight:400; color:#4A5859; letter-spacing:0.1em;">
                {{ $diagnosis->skin_type_label }}
            </div>

            @php
                $descriptions = [
                    'dry'         => '水分が不足しがちな乾燥肌タイプです。保湿力の高い成分を中心に、肌のバリア機能をしっかりサポートするケアが大切です。',
                    'oily'        => '皮脂の分泌が活発なオイリー肌タイプです。毛穴の詰まりを防ぎながら、水分と油分のバランスを整えるケアが重要です。',
                    'combination' => 'Tゾーンは皮脂が多く、頬などは乾燥しやすい混合肌タイプです。部位に応じたバランスの良いケアが必要です。',
                    'sensitive'   => '外部刺激に反応しやすい敏感肌タイプです。低刺激・無添加成分を使用し、肌への負担を最小限にしたケアが求められます。',
                ];
            @endphp
            <p style="font-size:0.85rem; line-height:1.9; color:#5A6B6C; margin-top:1.5rem; max-width:500px; margin-left:auto; margin-right:auto;">
                {{ $descriptions[$diagnosis->skin_type] ?? '' }}
            </p>

            <p style="font-size:0.72rem; color:#B0BFBF; margin-top:1rem;">
                診断日: {{ $diagnosis->created_at->format('Y年m月d日') }}
            </p>
        </div>

        {{-- Recommended Products --}}
        @if($products->count() > 0)
        <div style="margin-bottom:4rem;">
            <h2 style="font-family:'Noto Serif JP', serif; font-size:1.2rem; font-weight:400; color:#2E3A3B; text-align:center; margin-bottom:2.5rem;">
                あなたにおすすめのアイテム
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($products as $product)
                <a href="{{ route('products.show', $product->slug) }}" style="text-decoration:none;">
                    <div style="background:#fff; border:1px solid #E8E4DC; border-radius:4px; overflow:hidden; transition:transform .3s, box-shadow .3s;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 24px rgba(74,88,89,0.1)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                        <div style="aspect-ratio:4/3; background:#F0EDE6; display:flex; align-items:center; justify-content:center;">
                            @if($product->image)
                                <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" style="width:100%; height:100%; object-fit:cover;">
                            @else
                                <svg width="36" height="36" fill="none" stroke="#C4A882" stroke-width="0.8" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            @endif
                        </div>
                        <div style="padding:1.25rem;">
                            <p style="font-size:0.65rem; letter-spacing:0.15em; color:#8A9899; margin-bottom:0.4rem; text-transform:uppercase;">{{ $product->category }}</p>
                            <h3 style="font-family:'Noto Serif JP', serif; font-size:0.95rem; color:#2E3A3B; margin-bottom:0.75rem; line-height:1.5;">{{ $product->name }}</h3>
                            <p style="font-size:0.95rem; color:#4A5859; font-weight:500;">¥{{ number_format($product->subscription_price) }}<span style="font-size:0.7rem; font-weight:400;">/月</span></p>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>

            <div style="text-align:center; margin-top:2.5rem; display:flex; gap:1rem; justify-content:center; flex-wrap:wrap;">
                <a href="{{ route('products.index') }}?skin_type={{ $diagnosis->skin_type }}"
                   style="display:inline-block; background:#4A5859; color:#fff; padding:0.875rem 2rem; font-size:0.8rem; letter-spacing:0.1em; text-decoration:none; border-radius:2px; transition:opacity .2s;"
                   onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                    このタイプの全商品を見る
                </a>
                <a href="{{ route('diagnosis') }}"
                   style="display:inline-block; border:1px solid #4A5859; color:#4A5859; padding:0.875rem 2rem; font-size:0.8rem; letter-spacing:0.1em; text-decoration:none; border-radius:2px; background:#fff; transition:all .2s;"
                   onmouseover="this.style.background='#4A5859'; this.style.color='#fff'" onmouseout="this.style.background='#fff'; this.style.color='#4A5859'">
                    もう一度診断する
                </a>
            </div>
        </div>
        @else
        <div style="text-align:center; padding:3rem 0; margin-bottom:4rem;">
            <p style="font-size:0.85rem; color:#8A9899; margin-bottom:1.5rem;">現在、おすすめ商品を準備中です。</p>
            <a href="{{ route('products.index') }}"
               style="display:inline-block; background:#4A5859; color:#fff; padding:0.875rem 2rem; font-size:0.8rem; letter-spacing:0.1em; text-decoration:none; border-radius:2px;">
                すべての商品を見る
            </a>
        </div>
        @endif

        {{-- CTA --}}
        <div style="background:linear-gradient(135deg, #4A5859 0%, #2E3A3B 100%); border-radius:4px; padding:3rem 2.5rem; text-align:center; color:#fff;">
            <p style="font-size:0.65rem; letter-spacing:0.2em; color:#8A9899; margin-bottom:0.75rem; text-transform:uppercase;">Start Your Routine</p>
            <h2 style="font-family:'Noto Serif JP', serif; font-size:1.3rem; font-weight:300; margin-bottom:1rem;">
                {{ $diagnosis->skin_type_label }}に合った<br>スキンケアを定期便で
            </h2>
            <p style="font-size:0.8rem; color:#B0BFBF; margin-bottom:2rem; line-height:1.8;">
                毎月自動でお届け。送料無料・いつでも解約可能。
            </p>
            <a href="{{ route('products.index') }}?skin_type={{ $diagnosis->skin_type }}"
               style="display:inline-block; background:#C4A882; color:#fff; padding:0.875rem 2.5rem; font-size:0.85rem; letter-spacing:0.1em; text-decoration:none; border-radius:2px; transition:opacity .2s;"
               onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                定期便をはじめる
            </a>
        </div>

    </div>
</x-app-layout>
