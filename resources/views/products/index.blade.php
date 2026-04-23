<x-app-layout>
    <x-slot name="title">商品一覧</x-slot>

    {{-- Header --}}
    <section style="background:#fff; padding:4rem 1.5rem 3rem; text-align:center; border-bottom:1px solid #E8E4DC;">
        <p style="font-size:0.65rem; letter-spacing:0.3em; color:#8A9899; margin-bottom:1rem; text-transform:uppercase;">Products</p>
        <h1 style="font-family:'Noto Serif JP', serif; font-size:clamp(1.5rem, 3vw, 2.2rem); font-weight:300; color:#2E3A3B; margin-bottom:1rem;">オーガニックコスメ一覧</h1>
        <p style="font-size:0.85rem; color:#5A6B6C; line-height:1.9;">定期便なら最大20%OFF。買い忘れ不要で毎月届きます。</p>
    </section>

    <div class="max-w-6xl mx-auto px-6 py-12">

        {{-- Subscription benefit banner --}}
        <div style="background:#4A5859; color:#fff; border-radius:4px; padding:1.25rem 2rem; margin-bottom:3rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
            <div>
                <p style="font-size:0.7rem; letter-spacing:0.15em; margin-bottom:0.3rem; opacity:0.8;">定期便の特典</p>
                <p style="font-size:0.9rem; font-weight:500;">通常価格より最大20%OFF ・ 送料無料 ・ いつでもスキップ可能</p>
            </div>
            <a href="{{ route('diagnosis') }}" style="font-size:0.75rem; letter-spacing:0.1em; color:#4A5859; background:#F5F5F0; padding:0.6rem 1.5rem; border-radius:2px; text-decoration:none; white-space:nowrap;">肌質診断で最適セットを見つける →</a>
        </div>

        {{-- Filter --}}
        <form method="GET" action="{{ route('products.index') }}" style="display:flex; gap:1rem; margin-bottom:3rem; flex-wrap:wrap;">
            <select name="skin_type" style="padding:0.5rem 1rem; border:1px solid #D8D4CC; background:#fff; font-size:0.8rem; color:#4A5859; border-radius:2px; cursor:pointer;" onchange="this.form.submit()">
                <option value="">すべての肌タイプ</option>
                <option value="dry" {{ request('skin_type') === 'dry' ? 'selected' : '' }}>乾燥肌</option>
                <option value="oily" {{ request('skin_type') === 'oily' ? 'selected' : '' }}>オイリー肌</option>
                <option value="combination" {{ request('skin_type') === 'combination' ? 'selected' : '' }}>混合肌</option>
                <option value="sensitive" {{ request('skin_type') === 'sensitive' ? 'selected' : '' }}>敏感肌</option>
            </select>
            <select name="category" style="padding:0.5rem 1rem; border:1px solid #D8D4CC; background:#fff; font-size:0.8rem; color:#4A5859; border-radius:2px; cursor:pointer;" onchange="this.form.submit()">
                <option value="">すべてのカテゴリ</option>
                <option value="cleanser" {{ request('category') === 'cleanser' ? 'selected' : '' }}>洗顔料</option>
                <option value="toner" {{ request('category') === 'toner' ? 'selected' : '' }}>化粧水</option>
                <option value="serum" {{ request('category') === 'serum' ? 'selected' : '' }}>美容液</option>
                <option value="moisturizer" {{ request('category') === 'moisturizer' ? 'selected' : '' }}>保湿クリーム</option>
                <option value="eye" {{ request('category') === 'eye' ? 'selected' : '' }}>アイケア</option>
            </select>
            @if(request()->hasAny(['skin_type', 'category']))
                <a href="{{ route('products.index') }}" style="font-size:0.75rem; color:#8A9899; text-decoration:none; display:flex; align-items:center;">× クリア</a>
            @endif
        </form>

        {{-- Product Grid --}}
        @if($products->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($products as $product)
            <a href="{{ route('products.show', $product->slug) }}" style="text-decoration:none; display:block;">
                <article style="background:#fff; border:1px solid #E8E4DC; border-radius:4px; overflow:hidden; height:100%; transition:transform .3s, box-shadow .3s;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 40px rgba(74,88,89,0.1)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                    <div style="aspect-ratio:4/3; background:#F0EDE6; display:flex; align-items:center; justify-content:center; position:relative;">
                        @if($product->image)
                            <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" style="width:100%; height:100%; object-fit:cover;">
                        @else
                            <svg width="48" height="48" fill="none" stroke="#C4A882" stroke-width="0.8" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        @endif
                        <span style="position:absolute; top:1rem; right:1rem; background:#4A5859; color:#fff; font-size:0.65rem; letter-spacing:0.1em; padding:0.25rem 0.6rem; border-radius:2px;">定期{{ $product->discount_percent }}%OFF</span>
                    </div>
                    <div style="padding:1.5rem;">
                        <p style="font-size:0.65rem; letter-spacing:0.15em; color:#8A9899; margin-bottom:0.5rem; text-transform:uppercase;">{{ $product->category }}</p>
                        <h2 style="font-family:'Noto Serif JP', serif; font-size:1rem; color:#2E3A3B; margin-bottom:0.5rem; line-height:1.5;">{{ $product->name }}</h2>
                        <p style="font-size:0.8rem; color:#5A6B6C; line-height:1.7; margin-bottom:1rem;">{{ Str::limit($product->description, 60) }}</p>
                        <div style="display:flex; align-items:baseline; gap:0.75rem; flex-wrap:wrap;">
                            <span style="font-size:1.1rem; color:#4A5859; font-weight:500;">¥{{ number_format($product->subscription_price) }}<span style="font-size:0.7rem; font-weight:400;">/月</span></span>
                            <span style="font-size:0.75rem; color:#B0BFBF; text-decoration:line-through;">通常¥{{ number_format($product->price) }}</span>
                        </div>
                        <div style="display:flex; gap:0.5rem; margin-top:0.75rem; flex-wrap:wrap;">
                            @foreach($product->skin_types as $type)
                            <span style="font-size:0.65rem; border:1px solid #D8D4CC; color:#8A9899; padding:0.15rem 0.5rem; border-radius:100px;">{{ match($type) { 'dry'=>'乾燥肌', 'oily'=>'オイリー肌', 'combination'=>'混合肌', 'sensitive'=>'敏感肌', default=>$type } }}</span>
                            @endforeach
                        </div>
                    </div>
                </article>
            </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div style="margin-top:3rem;">
            {{ $products->appends(request()->query())->links() }}
        </div>

        @else
        <div style="text-align:center; padding:6rem 0;">
            <p style="font-size:0.9rem; color:#8A9899;">条件に合う商品が見つかりませんでした。</p>
            <a href="{{ route('products.index') }}" style="display:inline-block; margin-top:1.5rem; font-size:0.8rem; color:#4A5859; text-decoration:underline;">すべての商品を見る</a>
        </div>
        @endif
    </div>
</x-app-layout>
