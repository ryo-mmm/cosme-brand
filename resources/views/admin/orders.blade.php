<x-app-layout>
    <x-slot name="title">注文一覧</x-slot>

    <div class="max-w-6xl mx-auto" style="padding: 3rem 1.5rem;">
        <div style="margin-bottom:2rem;">
            <a href="{{ route('admin.dashboard') }}" style="font-size:0.75rem; color:#8A9899; text-decoration:none;">← ダッシュボード</a>
            <h1 style="font-family:'Noto Serif JP', serif; font-size:1.5rem; font-weight:300; color:#2E3A3B; margin-top:0.5rem;">注文一覧</h1>
        </div>

        {{-- Filter Form --}}
        <form method="GET" action="{{ route('admin.orders') }}"
              style="background:#fff; border:1px solid #E8E4DC; border-radius:4px; padding:1.25rem 1.5rem; margin-bottom:1.5rem; display:flex; flex-wrap:wrap; gap:1rem; align-items:flex-end;">
            <div style="flex:1; min-width:200px;">
                <label style="display:block; font-size:0.72rem; color:#8A9899; margin-bottom:0.3rem; letter-spacing:0.05em;">メール / 氏名で検索</label>
                <input type="text" name="q" value="{{ $search }}" placeholder="例: user@example.com"
                       style="width:100%; padding:0.6rem 0.875rem; border:1px solid #D8D4CC; border-radius:2px; font-size:0.82rem; background:#fff; box-sizing:border-box;">
            </div>
            <div>
                <label style="display:block; font-size:0.72rem; color:#8A9899; margin-bottom:0.3rem; letter-spacing:0.05em;">期間（開始）</label>
                <input type="date" name="date_from"
                       value="{{ $dateFrom?->format('Y-m-d') }}"
                       style="padding:0.6rem 0.875rem; border:1px solid #D8D4CC; border-radius:2px; font-size:0.82rem; background:#fff;">
            </div>
            <div>
                <label style="display:block; font-size:0.72rem; color:#8A9899; margin-bottom:0.3rem; letter-spacing:0.05em;">期間（終了）</label>
                <input type="date" name="date_to"
                       value="{{ $dateTo?->format('Y-m-d') }}"
                       style="padding:0.6rem 0.875rem; border:1px solid #D8D4CC; border-radius:2px; font-size:0.82rem; background:#fff;">
            </div>
            <div style="display:flex; gap:0.5rem;">
                <button type="submit"
                        style="background:#4A5859; color:#fff; border:none; padding:0.6rem 1.25rem; font-size:0.8rem; letter-spacing:0.08em; border-radius:2px; cursor:pointer; font-family:inherit;">
                    絞り込む
                </button>
                @if($search || $dateFrom || $dateTo)
                <a href="{{ route('admin.orders') }}"
                   style="border:1px solid #D8D4CC; color:#8A9899; padding:0.6rem 1rem; font-size:0.8rem; border-radius:2px; text-decoration:none; white-space:nowrap;">
                    クリア
                </a>
                @endif
            </div>
        </form>

        {{-- Result count --}}
        @if($search || $dateFrom || $dateTo)
        <p style="font-size:0.78rem; color:#8A9899; margin-bottom:1rem;">
            {{ $orders->total() }} 件の注文が見つかりました
            @if($search)（検索: {{ $search }}）@endif
            @if($dateFrom)（{{ $dateFrom->format('Y/m/d') }}〜{{ $dateTo?->format('Y/m/d') ?? '現在' }}）@endif
        </p>
        @endif

        @if($orders->isEmpty())
        <p style="font-size:0.85rem; color:#8A9899; text-align:center; padding:3rem;">注文データがありません。</p>
        @else
        <div style="background:#fff; border:1px solid #E8E4DC; border-radius:4px; overflow:hidden;">
            <table style="width:100%; font-size:0.82rem; border-collapse:collapse;">
                <thead>
                    <tr style="background:#F5F5F0; border-bottom:1px solid #E8E4DC;">
                        <th style="text-align:left; padding:0.875rem 1rem; color:#8A9899; font-weight:400;">日時</th>
                        <th style="text-align:left; padding:0.875rem 1rem; color:#8A9899; font-weight:400;">ユーザー</th>
                        <th style="text-align:left; padding:0.875rem 1rem; color:#8A9899; font-weight:400;">内容</th>
                        <th style="text-align:left; padding:0.875rem 1rem; color:#8A9899; font-weight:400;">決済ID</th>
                        <th style="text-align:right; padding:0.875rem 1rem; color:#8A9899; font-weight:400;">金額</th>
                        <th style="text-align:center; padding:0.875rem 1rem; color:#8A9899; font-weight:400;">状態</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr style="border-bottom:1px solid #F5F5F0;">
                        <td style="padding:0.875rem 1rem; color:#8A9899; white-space:nowrap;">
                            {{ $order->created_at->format('Y/m/d H:i') }}
                        </td>
                        <td style="padding:0.875rem 1rem;">
                            @if($order->user)
                                <p style="color:#2E3A3B;">{{ $order->user->name }}</p>
                                <p style="font-size:0.72rem; color:#8A9899;">{{ $order->user->email }}</p>
                            @else
                                <p style="font-size:0.72rem; color:#B0BFBF;">削除済みユーザー</p>
                            @endif
                        </td>
                        <td style="padding:0.875rem 1rem; color:#4A5859;">
                            {{ $order->description ?? '定期便' }}
                        </td>
                        <td style="padding:0.875rem 1rem;">
                            <span style="font-size:0.7rem; color:#B0BFBF; font-family:monospace;">{{ $order->stripe_charge_id }}</span>
                        </td>
                        <td style="padding:0.875rem 1rem; text-align:right; color:#2E3A3B; font-weight:500;">
                            ¥{{ number_format($order->amount / 100) }}
                        </td>
                        <td style="padding:0.875rem 1rem; text-align:center;">
                            @if($order->isRefunded())
                                <span style="font-size:0.65rem; letter-spacing:0.05em; color:#8A9899; background:#F0F0F0; padding:0.2rem 0.6rem; border-radius:2px;">返金済み</span>
                            @else
                                <span style="font-size:0.65rem; letter-spacing:0.05em; color:#4A5859; background:#E8F0E8; padding:0.2rem 0.6rem; border-radius:2px;">決済完了</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($orders->hasPages())
        <div style="margin-top:1rem;">
            {{ $orders->links() }}
        </div>
        @endif

        <p style="font-size:0.72rem; color:#B0BFBF; margin-top:0.75rem; text-align:right;">
            全 {{ $orders->total() }} 件 / ページ {{ $orders->currentPage() }} / {{ $orders->lastPage() }}
        </p>
        @endif
    </div>
</x-app-layout>
