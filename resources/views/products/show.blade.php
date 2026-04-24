<x-app-layout>
    <x-slot name="title">{{ $product->name }}</x-slot>

    <div class="max-w-6xl mx-auto px-6 py-12">

        {{-- Breadcrumb --}}
        <nav style="font-size:0.75rem; color:#8A9899; margin-bottom:3rem; display:flex; gap:0.5rem; align-items:center;">
            <a href="{{ route('top') }}" style="color:#8A9899; text-decoration:none;">TOP</a>
            <span>›</span>
            <a href="{{ route('products.index') }}" style="color:#8A9899; text-decoration:none;">商品一覧</a>
            <span>›</span>
            <span style="color:#4A5859;">{{ $product->name }}</span>
        </nav>

        {{-- Product Detail --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-16">
            {{-- Image --}}
            <div>
                <div style="background:#F0EDE6; border-radius:4px; aspect-ratio:1; display:flex; align-items:center; justify-content:center; border:1px solid #E8E4DC;">
                    @if($product->image)
                        <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" style="width:100%; height:100%; object-fit:cover; border-radius:4px;">
                    @else
                        <svg width="80" height="80" fill="none" stroke="#C4A882" stroke-width="0.6" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    @endif
                </div>
            </div>

            {{-- Info --}}
            <div>
                <p style="font-size:0.65rem; letter-spacing:0.2em; color:#8A9899; margin-bottom:0.75rem; text-transform:uppercase;">{{ $product->category }} {{ $product->volume_ml ? '| '.$product->volume_ml.'ml' : '' }}</p>
                <h1 style="font-family:'Noto Serif JP', serif; font-size:1.75rem; font-weight:300; color:#2E3A3B; margin-bottom:1.5rem; line-height:1.5;">{{ $product->name }}</h1>

                {{-- Skin types --}}
                <div style="display:flex; gap:0.5rem; margin-bottom:2rem; flex-wrap:wrap;">
                    @foreach($product->skin_types as $type)
                    <span style="font-size:0.7rem; border:1px solid #4A5859; color:#4A5859; padding:0.3rem 0.75rem; border-radius:100px;">{{ match($type) { 'dry'=>'乾燥肌', 'oily'=>'オイリー肌', 'combination'=>'混合肌', 'sensitive'=>'敏感肌', default=>$type } }}</span>
                    @endforeach
                </div>

                {{-- Purchase Type Toggle --}}
                <div style="margin-bottom:1.5rem;" x-data="{ type: 'subscription' }">

                    {{-- Tab switcher --}}
                    <div style="display:grid; grid-template-columns:1fr 1fr; border:1px solid #D8D4CC; border-radius:4px; overflow:hidden; margin-bottom:1.25rem;">
                        <button
                            @click="type = 'subscription'"
                            :style="type === 'subscription'
                                ? 'background:#4A5859; color:#fff;'
                                : 'background:#fff; color:#8A9899;'"
                            style="padding:0.9rem 1rem; font-size:0.85rem; letter-spacing:0.08em; line-height:1; border:none; cursor:pointer; transition:background .15s, color .15s; font-family:inherit; text-align:center; display:block; width:100%;"
                        >定期便</button>
                        <button
                            @click="type = 'single'"
                            :style="type === 'single'
                                ? 'background:#4A5859; color:#fff;'
                                : 'background:#fff; color:#8A9899;'"
                            style="padding:0.9rem 1rem; font-size:0.85rem; letter-spacing:0.08em; line-height:1; border:none; border-left:1px solid #D8D4CC; cursor:pointer; transition:background .15s, color .15s; font-family:inherit; text-align:center; display:block; width:100%;"
                        >単品購入</button>
                    </div>

                    {{-- Subscription panel --}}
                    <div x-show="type === 'subscription'" x-cloak>
                        <div style="background:#4A5859; border-radius:4px; padding:1.25rem 1.5rem; margin-bottom:1.25rem;">
                            <div style="display:flex; align-items:center; justify-content:space-between; gap:1rem;">
                                <div>
                                    <p style="font-size:0.6rem; letter-spacing:0.15em; color:#B0BFBF; margin-bottom:0.25rem;">定期便価格</p>
                                    <p style="font-size:1.75rem; color:#fff; font-weight:500; line-height:1.1;">¥{{ number_format($product->subscription_price) }}<span style="font-size:0.75rem; font-weight:400; margin-left:0.25rem;">/月</span></p>
                                    <p style="font-size:0.68rem; color:#B0BFBF; margin-top:0.25rem;">税込・送料無料</p>
                                </div>
                                <div>
                                    <span style="font-size:0.72rem; background:#C4A882; color:#fff; padding:0.3rem 0.75rem; border-radius:2px; display:block; text-align:center; margin-bottom:0.25rem; white-space:nowrap;">{{ $product->discount_percent }}%OFF</span>
                                    <p style="font-size:0.72rem; color:#8A9899; text-decoration:line-through; text-align:center;">¥{{ number_format($product->price) }}</p>
                                </div>
                            </div>
                        </div>
                        <ul style="list-style:none; padding:0; margin:0 0 1.25rem; display:flex; flex-direction:column; gap:0.5rem;">
                            @foreach(['毎月自動でお届け', '配送日の変更・スキップが自由', '送料無料', 'いつでも解約可能'] as $benefit)
                            <li style="font-size:0.78rem; color:#5A6B6C; display:flex; align-items:center; gap:0.5rem;">
                                <svg width="13" height="13" fill="none" stroke="#4A5859" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                {{ $benefit }}
                            </li>
                            @endforeach
                        </ul>
                        <a href="{{ route('checkout') }}?products[]={{ $product->id }}&type=subscription"
                           style="display:block; text-align:center; background:#4A5859; color:#fff; padding:0.9rem 2rem; font-size:0.85rem; letter-spacing:0.15em; text-decoration:none; border-radius:2px;"
                           onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                            定期便に申し込む（{{ $product->discount_percent }}%OFF）
                        </a>
                    </div>

                    {{-- Single purchase panel --}}
                    <div x-show="type === 'single'" x-cloak>
                        <div style="background:#F5F5F0; border:1px solid #E8E4DC; border-radius:4px; padding:1.25rem 1.5rem; margin-bottom:1.25rem;">
                            <div style="display:flex; align-items:center; justify-content:space-between; gap:1rem;">
                                <div>
                                    <p style="font-size:0.6rem; letter-spacing:0.15em; color:#8A9899; margin-bottom:0.25rem;">単品価格</p>
                                    <p style="font-size:1.75rem; color:#2E3A3B; font-weight:500; line-height:1.1;">¥{{ number_format($product->price) }}</p>
                                    <p style="font-size:0.68rem; color:#8A9899; margin-top:0.25rem;">税込・送料別（¥550）</p>
                                </div>
                            </div>
                        </div>
                        <ul style="list-style:none; padding:0; margin:0 0 1.25rem; display:flex; flex-direction:column; gap:0.5rem;">
                            @foreach(['1回のみのお届け', '購入後すぐに発送', '繰り返しの請求なし'] as $benefit)
                            <li style="font-size:0.78rem; color:#5A6B6C; display:flex; align-items:center; gap:0.5rem;">
                                <svg width="13" height="13" fill="none" stroke="#4A5859" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                {{ $benefit }}
                            </li>
                            @endforeach
                        </ul>
                        <a href="{{ route('checkout') }}?products[]={{ $product->id }}&type=single"
                           style="display:block; text-align:center; border:2px solid #4A5859; color:#4A5859; padding:0.9rem 2rem; font-size:0.85rem; letter-spacing:0.15em; text-decoration:none; border-radius:2px; background:#fff;"
                           onmouseover="this.style.background='#4A5859'; this.style.color='#fff'" onmouseout="this.style.background='#fff'; this.style.color='#4A5859'">
                            単品で購入する（¥{{ number_format($product->price) }}）
                        </a>
                    </div>

                    {{-- Diagnosis link --}}
                    <a href="{{ route('diagnosis') }}" style="display:block; text-align:center; border:1px solid #D8D4CC; color:#8A9899; padding:0.75rem 2rem; font-size:0.78rem; letter-spacing:0.1em; text-decoration:none; border-radius:2px; margin-top:1rem; transition:all .2s;" onmouseover="this.style.borderColor='#4A5859'; this.style.color='#4A5859'" onmouseout="this.style.borderColor='#D8D4CC'; this.style.color='#8A9899'">
                        肌質診断でセットを選ぶ
                    </a>
                </div>
            </div>
        </div>

        {{-- Ingredients --}}
        @if($product->ingredients)
        <div style="margin-top:4rem; padding-top:4rem; border-top:1px solid #E8E4DC;">
            <h2 style="font-family:'Noto Serif JP', serif; font-size:1.2rem; font-weight:400; color:#2E3A3B; margin-bottom:1.5rem;">全成分</h2>
            <p style="font-size:0.8rem; line-height:2; color:#5A6B6C;">{{ $product->ingredients }}</p>
        </div>
        @endif

        {{-- Related Products --}}
        @if($related->count() > 0)
        <div style="margin-top:5rem; padding-top:4rem; border-top:1px solid #E8E4DC;">
            <h2 style="font-family:'Noto Serif JP', serif; font-size:1.2rem; font-weight:400; color:#2E3A3B; margin-bottom:2.5rem; text-align:center;">同じ肌タイプにおすすめ</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($related as $rel)
                <a href="{{ route('products.show', $rel->slug) }}" style="text-decoration:none;">
                    <div style="background:#fff; border:1px solid #E8E4DC; border-radius:4px; overflow:hidden; transition:transform .3s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="aspect-ratio:4/3; background:#F0EDE6; display:flex; align-items:center; justify-content:center;">
                            <svg width="36" height="36" fill="none" stroke="#C4A882" stroke-width="0.8" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                        <div style="padding:1.25rem;">
                            <p style="font-size:0.65rem; color:#8A9899; letter-spacing:0.1em; margin-bottom:0.4rem;">{{ $rel->category }}</p>
                            <h3 style="font-family:'Noto Serif JP', serif; font-size:0.9rem; color:#2E3A3B; margin-bottom:0.5rem;">{{ $rel->name }}</h3>
                            <p style="font-size:0.9rem; color:#4A5859;">¥{{ number_format($rel->subscription_price) }}<span style="font-size:0.65rem;">/月</span></p>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
