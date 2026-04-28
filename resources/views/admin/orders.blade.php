<x-app-layout>
    <x-slot name="title">注文一覧</x-slot>

    <div class="max-w-6xl mx-auto" style="padding: 3rem 1.5rem;">
        <div style="margin-bottom:2.5rem;">
            <a href="{{ route('admin.dashboard') }}" style="font-size:0.75rem; color:#8A9899; text-decoration:none;">← ダッシュボード</a>
            <h1 style="font-family:'Noto Serif JP', serif; font-size:1.5rem; font-weight:300; color:#2E3A3B; margin-top:0.5rem;">注文一覧</h1>
        </div>

        @if($charges->isEmpty())
        <p style="font-size:0.85rem; color:#8A9899; text-align:center; padding:3rem;">注文データがありません。</p>
        @else
        <div style="background:#fff; border:1px solid #E8E4DC; border-radius:4px; overflow:hidden;">
            <table style="width:100%; font-size:0.82rem; border-collapse:collapse;">
                <thead>
                    <tr style="background:#F5F5F0; border-bottom:1px solid #E8E4DC;">
                        <th style="text-align:left; padding:0.875rem 1rem; color:#8A9899; font-weight:400;">日時</th>
                        <th style="text-align:left; padding:0.875rem 1rem; color:#8A9899; font-weight:400;">ユーザー</th>
                        <th style="text-align:left; padding:0.875rem 1rem; color:#8A9899; font-weight:400;">内容</th>
                        <th style="text-align:right; padding:0.875rem 1rem; color:#8A9899; font-weight:400;">金額</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($charges as $item)
                    <tr style="border-bottom:1px solid #F5F5F0;">
                        <td style="padding:0.875rem 1rem; color:#8A9899; white-space:nowrap;">
                            {{ $item['created_at']->format('Y/m/d H:i') }}
                        </td>
                        <td style="padding:0.875rem 1rem;">
                            <p style="color:#2E3A3B;">{{ $item['user']->name }}</p>
                            <p style="font-size:0.72rem; color:#8A9899;">{{ $item['user']->email }}</p>
                        </td>
                        <td style="padding:0.875rem 1rem; color:#4A5859;">{{ $item['description'] }}</td>
                        <td style="padding:0.875rem 1rem; text-align:right; color:#2E3A3B; font-weight:500;">
                            ¥{{ number_format($item['amount'] / 100) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</x-app-layout>
