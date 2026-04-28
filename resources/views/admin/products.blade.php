<x-app-layout>
    <x-slot name="title">商品管理</x-slot>

    <div class="max-w-6xl mx-auto" style="padding: 3rem 1.5rem;">
        <div style="margin-bottom:2.5rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
            <div>
                <a href="{{ route('admin.dashboard') }}" style="font-size:0.75rem; color:#8A9899; text-decoration:none;">← ダッシュボード</a>
                <h1 style="font-family:'Noto Serif JP', serif; font-size:1.5rem; font-weight:300; color:#2E3A3B; margin-top:0.5rem;">商品管理</h1>
            </div>
        </div>

        @if(session('success'))
        <div style="background:#E8F0E8; border:1px solid #4A5859; border-radius:2px; padding:0.875rem 1rem; margin-bottom:1.5rem; font-size:0.82rem; color:#2E3A3B;">
            {{ session('success') }}
        </div>
        @endif

        <div style="background:#fff; border:1px solid #E8E4DC; border-radius:4px; overflow:hidden;">
            <table style="width:100%; font-size:0.82rem; border-collapse:collapse;">
                <thead>
                    <tr style="background:#F5F5F0; border-bottom:1px solid #E8E4DC;">
                        <th style="text-align:left; padding:0.875rem 1rem; color:#8A9899; font-weight:400;">商品名</th>
                        <th style="text-align:right; padding:0.875rem 1rem; color:#8A9899; font-weight:400;">通常価格</th>
                        <th style="text-align:right; padding:0.875rem 1rem; color:#8A9899; font-weight:400;">定期価格</th>
                        <th style="text-align:right; padding:0.875rem 1rem; color:#8A9899; font-weight:400;">在庫</th>
                        <th style="text-align:center; padding:0.875rem 1rem; color:#8A9899; font-weight:400;">状態</th>
                        <th style="text-align:center; padding:0.875rem 1rem; color:#8A9899; font-weight:400;">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr style="border-bottom:1px solid #F5F5F0;">
                        <td style="padding:0.875rem 1rem;">
                            <p style="color:#2E3A3B; font-weight:500;">{{ $product->name }}</p>
                            <p style="font-size:0.72rem; color:#8A9899; margin-top:0.2rem;">{{ $product->category }}</p>
                        </td>
                        <td style="padding:0.875rem 1rem; text-align:right;">¥{{ number_format($product->price) }}</td>
                        <td style="padding:0.875rem 1rem; text-align:right;">¥{{ number_format($product->subscription_price) }}</td>
                        <td style="padding:0.875rem 1rem; text-align:right;">
                            <span style="{{ $product->stock <= 5 ? 'color:#c0392b; font-weight:500;' : 'color:#2E3A3B;' }}">
                                {{ $product->stock }}
                            </span>
                        </td>
                        <td style="padding:0.875rem 1rem; text-align:center;">
                            <form method="POST" action="{{ route('admin.products.toggle', $product) }}" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    style="font-size:0.7rem; padding:0.3rem 0.75rem; border-radius:100px; border:1px solid; cursor:pointer; font-family:inherit;
                                    {{ $product->is_active ? 'background:#E8F0E8; border-color:#4A5859; color:#4A5859;' : 'background:#F5F5F0; border-color:#B0BFBF; color:#8A9899;' }}">
                                    {{ $product->is_active ? '公開中' : '非公開' }}
                                </button>
                            </form>
                        </td>
                        <td style="padding:0.875rem 1rem; text-align:center;">
                            <a href="{{ route('admin.products.edit', $product) }}"
                               style="font-size:0.75rem; color:#4A5859; text-decoration:none; border-bottom:1px solid #C4A882;">編集</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
