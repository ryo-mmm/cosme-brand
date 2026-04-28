<x-app-layout>
    <x-slot name="title">ユーザー管理</x-slot>

    <div class="max-w-6xl mx-auto" style="padding: 3rem 1.5rem;">
        <div style="margin-bottom:2.5rem;">
            <a href="{{ route('admin.dashboard') }}" style="font-size:0.75rem; color:#8A9899; text-decoration:none;">← ダッシュボード</a>
            <h1 style="font-family:'Noto Serif JP', serif; font-size:1.5rem; font-weight:300; color:#2E3A3B; margin-top:0.5rem;">
                ユーザー管理（{{ $users->total() }} 名）
            </h1>
        </div>

        <div style="background:#fff; border:1px solid #E8E4DC; border-radius:4px; overflow:hidden;">
            <table style="width:100%; font-size:0.82rem; border-collapse:collapse;">
                <thead>
                    <tr style="background:#F5F5F0; border-bottom:1px solid #E8E4DC;">
                        <th style="text-align:left; padding:0.875rem 1rem; color:#8A9899; font-weight:400;">名前</th>
                        <th style="text-align:left; padding:0.875rem 1rem; color:#8A9899; font-weight:400;">メール</th>
                        <th style="text-align:center; padding:0.875rem 1rem; color:#8A9899; font-weight:400;">定期便</th>
                        <th style="text-align:left; padding:0.875rem 1rem; color:#8A9899; font-weight:400;">登録日</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    @php $sub = $user->subscriptions->first(); @endphp
                    <tr style="border-bottom:1px solid #F5F5F0;">
                        <td style="padding:0.875rem 1rem; color:#2E3A3B;">{{ $user->name }}</td>
                        <td style="padding:0.875rem 1rem; color:#4A5859;">{{ $user->email }}</td>
                        <td style="padding:0.875rem 1rem; text-align:center;">
                            @if($sub)
                                <span style="font-size:0.7rem; padding:0.25rem 0.65rem; border-radius:100px; border:1px solid;
                                    {{ $sub->stripe_status === 'active' ? 'background:#E8F0E8; border-color:#4A5859; color:#4A5859;' : 'background:#F5F5F0; border-color:#B0BFBF; color:#8A9899;' }}">
                                    {{ $sub->stripe_status === 'active' ? '契約中' : $sub->stripe_status }}
                                </span>
                            @else
                                <span style="font-size:0.7rem; color:#B0BFBF;">—</span>
                            @endif
                        </td>
                        <td style="padding:0.875rem 1rem; color:#8A9899;">{{ $user->created_at->format('Y/m/d') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top:1.5rem;">
            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>
