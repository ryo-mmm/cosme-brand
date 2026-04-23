<x-app-layout>
    <x-slot name="title">LUMIÈRE BOTANIQUE</x-slot>

    {{-- Hero Section --}}
    <section style="min-height:90vh; display:flex; align-items:center; justify-content:center; position:relative; overflow:hidden; background:linear-gradient(135deg, #F5F5F0 0%, #EAE8E0 50%, #DDD8CC 100%);">
        <div style="position:absolute; inset:0; background-image:url('https://images.unsplash.com/photo-1612817288484-6f916006741a?w=1920&q=80'); background-size:cover; background-position:center; opacity:0.12;"></div>
        <div style="position:relative; text-align:center; padding:2rem; max-width:700px; margin:0 auto;">
            <p style="font-size:0.7rem; letter-spacing:0.3em; color:#8A9899; margin-bottom:2rem; text-transform:uppercase;">Organic Skincare Subscription</p>
            <h1 style="font-family:'Noto Serif JP', serif; font-size:clamp(2rem, 5vw, 3.5rem); font-weight:300; color:#2E3A3B; line-height:1.6; margin-bottom:2rem;">
                肌が、よろこぶ朝を。<br>
                <span style="font-size:0.65em; color:#4A5859;">自然が、あなたを選ぶ定期便</span>
            </h1>
            <p style="font-size:0.9rem; line-height:2; color:#5A6B6C; margin-bottom:3rem; max-width:500px; margin-left:auto; margin-right:auto;">
                プロが厳選したオーガニック成分が、<br>毎月あなたの肌へ届く。忙しい日々の中の、<br>小さな贅沢を。
            </p>
            <div style="display:flex; gap:1rem; justify-content:center; flex-wrap:wrap;">
                <a href="{{ route('diagnosis') }}" style="display:inline-block; background:#4A5859; color:#fff; padding:1rem 2.5rem; font-size:0.8rem; letter-spacing:0.15em; text-decoration:none; border-radius:2px; transition:opacity .2s;" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                    無料 肌質診断をはじめる
                </a>
                <a href="{{ route('products.index') }}" style="display:inline-block; border:1px solid #4A5859; color:#4A5859; padding:1rem 2.5rem; font-size:0.8rem; letter-spacing:0.15em; text-decoration:none; border-radius:2px; background:transparent; transition:all .2s;" onmouseover="this.style.background='#4A5859'; this.style.color='#fff'" onmouseout="this.style.background='transparent'; this.style.color='#4A5859'">
                    商品を見る
                </a>
            </div>
        </div>
        {{-- Scroll indicator --}}
        <div style="position:absolute; bottom:2rem; left:50%; transform:translateX(-50%); display:flex; flex-direction:column; align-items:center; gap:0.5rem;">
            <span style="font-size:0.6rem; letter-spacing:0.2em; color:#8A9899;">SCROLL</span>
            <div style="width:1px; height:40px; background:linear-gradient(to bottom, #8A9899, transparent);"></div>
        </div>
    </section>

    {{-- Why Us Section --}}
    <section style="padding:6rem 1.5rem; max-width:900px; margin:0 auto; text-align:center;">
        <p style="font-size:0.65rem; letter-spacing:0.3em; color:#8A9899; margin-bottom:1rem; text-transform:uppercase;">Why Lumière</p>
        <h2 style="font-family:'Noto Serif JP', serif; font-size:clamp(1.5rem, 3vw, 2.2rem); font-weight:300; color:#2E3A3B; margin-bottom:4rem;">選ばれる、3つの理由</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
            <div style="display:flex; flex-direction:column; align-items:center; gap:1.25rem;">
                <div style="width:56px; height:56px; border:1px solid #C4A882; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                    <svg width="24" height="24" fill="none" stroke="#C4A882" stroke-width="1.2" viewBox="0 0 24 24"><path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                </div>
                <h3 style="font-family:'Noto Serif JP', serif; font-size:1rem; font-weight:400; color:#2E3A3B;">AI肌質診断</h3>
                <p style="font-size:0.8rem; line-height:1.9; color:#5A6B6C;">7つの質問で、あなたの肌タイプを正確に分析。乾燥・オイリー・混合・敏感の4タイプから最適なセットを提案します。</p>
            </div>
            <div style="display:flex; flex-direction:column; align-items:center; gap:1.25rem;">
                <div style="width:56px; height:56px; border:1px solid #C4A882; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                    <svg width="24" height="24" fill="none" stroke="#C4A882" stroke-width="1.2" viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </div>
                <h3 style="font-family:'Noto Serif JP', serif; font-size:1rem; font-weight:400; color:#2E3A3B;">100%オーガニック成分</h3>
                <p style="font-size:0.8rem; line-height:1.9; color:#5A6B6C;">厳選した自然由来成分のみを使用。パラベン・合成香料・鉱物油不使用で、敏感肌にも安心してお使いいただけます。</p>
            </div>
            <div style="display:flex; flex-direction:column; align-items:center; gap:1.25rem;">
                <div style="width:56px; height:56px; border:1px solid #C4A882; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                    <svg width="24" height="24" fill="none" stroke="#C4A882" stroke-width="1.2" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <h3 style="font-family:'Noto Serif JP', serif; font-size:1rem; font-weight:400; color:#2E3A3B;">自由な配送管理</h3>
                <p style="font-size:0.8rem; line-height:1.9; color:#5A6B6C;">旅行や在庫が残っているときは1クリックでスキップ可能。縛りのない定期便で、自分のペースでスキンケアを続けられます。</p>
            </div>
        </div>
    </section>

    {{-- How It Works --}}
    <section style="background:#fff; padding:6rem 1.5rem;">
        <div style="max-width:800px; margin:0 auto; text-align:center;">
            <p style="font-size:0.65rem; letter-spacing:0.3em; color:#8A9899; margin-bottom:1rem; text-transform:uppercase;">How It Works</p>
            <h2 style="font-family:'Noto Serif JP', serif; font-size:clamp(1.5rem, 3vw, 2.2rem); font-weight:300; color:#2E3A3B; margin-bottom:4rem;">はじめるのは、たった3ステップ</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach([
                    ['num' => '01', 'title' => '肌質診断', 'desc' => '7つの質問に答えるだけ。あなたの肌タイプを診断します。'],
                    ['num' => '02', 'title' => 'セット選択', 'desc' => '診断結果に基づき、最適なコスメセットが提案されます。'],
                    ['num' => '03', 'title' => '定期お届け', 'desc' => '毎月指定日にお届け。配送日変更・スキップも自由。'],
                ] as $step)
                <div style="display:flex; flex-direction:column; align-items:center; gap:1rem; padding:2rem;">
                    <span style="font-family:'Noto Serif JP', serif; font-size:2.5rem; font-weight:300; color:#E0D8CC;">{{ $step['num'] }}</span>
                    <h3 style="font-size:1rem; font-weight:500; color:#2E3A3B;">{{ $step['title'] }}</h3>
                    <p style="font-size:0.8rem; line-height:1.9; color:#5A6B6C;">{{ $step['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Featured Products --}}
    @if($featuredProducts->count() > 0)
    <section style="padding:6rem 1.5rem; max-width:1100px; margin:0 auto;">
        <div style="text-align:center; margin-bottom:3rem;">
            <p style="font-size:0.65rem; letter-spacing:0.3em; color:#8A9899; margin-bottom:1rem; text-transform:uppercase;">Featured Products</p>
            <h2 style="font-family:'Noto Serif JP', serif; font-size:clamp(1.5rem, 3vw, 2.2rem); font-weight:300; color:#2E3A3B;">注目のラインナップ</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($featuredProducts as $product)
            <a href="{{ route('products.show', $product->slug) }}" style="text-decoration:none; display:block;">
                <div style="background:#fff; border:1px solid #E8E4DC; border-radius:4px; overflow:hidden; transition:transform .3s, box-shadow .3s;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 40px rgba(74,88,89,0.1)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                    <div style="aspect-ratio:4/3; background:#F0EDE6; display:flex; align-items:center; justify-content:center;">
                        @if($product->image)
                            <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" style="width:100%; height:100%; object-fit:cover;">
                        @else
                            <svg width="48" height="48" fill="none" stroke="#C4A882" stroke-width="0.8" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        @endif
                    </div>
                    <div style="padding:1.5rem;">
                        <p style="font-size:0.65rem; letter-spacing:0.15em; color:#8A9899; margin-bottom:0.5rem; text-transform:uppercase;">{{ $product->category }}</p>
                        <h3 style="font-family:'Noto Serif JP', serif; font-size:1rem; color:#2E3A3B; margin-bottom:0.75rem;">{{ $product->name }}</h3>
                        <div style="display:flex; align-items:center; gap:0.75rem;">
                            <span style="font-size:1rem; color:#4A5859; font-weight:500;">¥{{ number_format($product->subscription_price) }}<span style="font-size:0.7rem; font-weight:400;">/月</span></span>
                            <span style="font-size:0.75rem; color:#8A9899; text-decoration:line-through;">¥{{ number_format($product->price) }}</span>
                            <span style="font-size:0.65rem; background:#4A5859; color:#fff; padding:0.2rem 0.5rem; border-radius:2px;">{{ $product->discount_percent }}%OFF</span>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        <div style="text-align:center; margin-top:3rem;">
            <a href="{{ route('products.index') }}" style="display:inline-block; border:1px solid #4A5859; color:#4A5859; padding:0.875rem 2.5rem; font-size:0.8rem; letter-spacing:0.15em; text-decoration:none; border-radius:2px; transition:all .2s;" onmouseover="this.style.background='#4A5859'; this.style.color='#fff'" onmouseout="this.style.background='transparent'; this.style.color='#4A5859'">
                すべての商品を見る
            </a>
        </div>
    </section>
    @endif

    {{-- Diagnosis CTA --}}
    <section style="background:#4A5859; padding:6rem 1.5rem; text-align:center;">
        <p style="font-size:0.65rem; letter-spacing:0.3em; color:#8A9899; margin-bottom:1.5rem; text-transform:uppercase;">Skin Diagnosis</p>
        <h2 style="font-family:'Noto Serif JP', serif; font-size:clamp(1.5rem, 3vw, 2.5rem); font-weight:300; color:#F5F5F0; margin-bottom:1.5rem; line-height:1.6;">あなたの肌を、もっとよく知ろう。</h2>
        <p style="font-size:0.85rem; color:#B0BFBF; margin-bottom:2.5rem; line-height:1.9;">無料の肌質診断で、最適なスキンケアを見つけましょう。<br>所要時間はわずか2分です。</p>
        <a href="{{ route('diagnosis') }}" style="display:inline-block; background:#C4A882; color:#fff; padding:1rem 3rem; font-size:0.85rem; letter-spacing:0.15em; text-decoration:none; border-radius:2px; transition:opacity .2s;" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
            無料診断をはじめる →
        </a>
    </section>
</x-app-layout>
