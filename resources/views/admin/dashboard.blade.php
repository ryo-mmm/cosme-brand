<x-app-layout>
    <x-slot name="title">管理画面</x-slot>

    <div class="max-w-6xl mx-auto" style="padding: 3rem 1.5rem;">
        <div style="margin-bottom:2.5rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
            <div>
                <p style="font-size:0.65rem; letter-spacing:0.3em; color:#8A9899; margin-bottom:0.4rem; text-transform:uppercase;">Admin</p>
                <h1 style="font-family:'Noto Serif JP', serif; font-size:1.5rem; font-weight:300; color:#2E3A3B;">ダッシュボード</h1>
            </div>
            <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
                <a href="{{ route('admin.products') }}" style="font-size:0.78rem; padding:0.6rem 1.25rem; border:1px solid #4A5859; color:#4A5859; text-decoration:none; border-radius:2px;">商品管理</a>
                <a href="{{ route('admin.users') }}" style="font-size:0.78rem; padding:0.6rem 1.25rem; border:1px solid #4A5859; color:#4A5859; text-decoration:none; border-radius:2px;">ユーザー管理</a>
                <a href="{{ route('admin.orders') }}" style="font-size:0.78rem; padding:0.6rem 1.25rem; border:1px solid #4A5859; color:#4A5859; text-decoration:none; border-radius:2px;">注文一覧</a>
            </div>
        </div>

        @if(session('success'))
        <div style="background:#E8F0E8; border:1px solid #4A5859; border-radius:2px; padding:0.875rem 1rem; margin-bottom:1.5rem; font-size:0.82rem; color:#2E3A3B;">
            {{ session('success') }}
        </div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            @foreach([
                ['label' => '総会員数', 'value' => number_format($stats['total_users']) . ' 名'],
                ['label' => '定期便契約中', 'value' => number_format($stats['subscribed']) . ' 名'],
                ['label' => '総商品数', 'value' => number_format($stats['total_products']) . ' 点'],
                ['label' => '公開中商品', 'value' => number_format($stats['active_products']) . ' 点'],
            ] as $stat)
            <div style="background:#fff; border:1px solid #E8E4DC; border-radius:4px; padding:1.5rem; text-align:center;">
                <p style="font-size:0.65rem; letter-spacing:0.1em; color:#8A9899; margin-bottom:0.5rem; text-transform:uppercase;">{{ $stat['label'] }}</p>
                <p style="font-family:'Noto Serif JP', serif; font-size:1.75rem; font-weight:300; color:#2E3A3B;">{{ $stat['value'] }}</p>
            </div>
            @endforeach
        </div>

        {{-- Recent Users --}}
        <div style="background:#fff; border:1px solid #E8E4DC; border-radius:4px; padding:2rem;">
            <h2 style="font-family:'Noto Serif JP', serif; font-size:1rem; font-weight:400; color:#2E3A3B; margin-bottom:1.5rem; padding-bottom:0.75rem; border-bottom:1px solid #E8E4DC;">最近の登録ユーザー</h2>
            <table style="width:100%; font-size:0.82rem; border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid #E8E4DC;">
                        <th style="text-align:left; padding:0.6rem 0.75rem; color:#8A9899; font-weight:400;">名前</th>
                        <th style="text-align:left; padding:0.6rem 0.75rem; color:#8A9899; font-weight:400;">メール</th>
                        <th style="text-align:left; padding:0.6rem 0.75rem; color:#8A9899; font-weight:400;">登録日</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentUsers as $user)
                    <tr style="border-bottom:1px solid #F5F5F0;">
                        <td style="padding:0.75rem;">{{ $user->name }}</td>
                        <td style="padding:0.75rem; color:#4A5859;">{{ $user->email }}</td>
                        <td style="padding:0.75rem; color:#8A9899;">{{ $user->created_at->format('Y/m/d') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
