<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'LUMIÈRE BOTANIQUE' }} | オーガニックコスメ定期便</title>
    <meta name="description" content="{{ $description ?? '30〜40代の働く女性に贈る、上質なオーガニックコスメの定期便サービス。' }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+JP:wght@300;400;500&family=Noto+Sans+JP:wght@300;400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body style="background-color:#F5F5F0; color:#2E3A3B; font-family:'Noto Sans JP', sans-serif;">

    {{-- Navigation --}}
    <header style="background-color:#F5F5F0; border-bottom:1px solid #E0E0D8; position:sticky; top:0; z-index:50;">
        <nav class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('top') }}" class="flex flex-col" style="text-decoration:none;">
                <span style="font-family:'Noto Serif JP', serif; font-size:1.1rem; letter-spacing:0.15em; color:#4A5859; font-weight:300;">LUMIÈRE BOTANIQUE</span>
                <span style="font-size:0.6rem; letter-spacing:0.2em; color:#8A9899; margin-top:1px;">オーガニックコスメ定期便</span>
            </a>
            <div class="hidden md:flex items-center gap-8">
                <a href="{{ route('diagnosis') }}" style="font-size:0.8rem; letter-spacing:0.1em; color:#4A5859; text-decoration:none; transition:opacity .2s;" onmouseover="this.style.opacity='.6'" onmouseout="this.style.opacity='1'">肌質診断</a>
                <a href="{{ route('products.index') }}" style="font-size:0.8rem; letter-spacing:0.1em; color:#4A5859; text-decoration:none; transition:opacity .2s;" onmouseover="this.style.opacity='.6'" onmouseout="this.style.opacity='1'">商品一覧</a>
                @auth
                    <a href="{{ route('mypage') }}" style="font-size:0.8rem; letter-spacing:0.1em; color:#4A5859; text-decoration:none; transition:opacity .2s;" onmouseover="this.style.opacity='.6'" onmouseout="this.style.opacity='1'">マイページ</a>
                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit" style="font-size:0.8rem; letter-spacing:0.1em; color:#8A9899; background:none; border:none; cursor:pointer;">ログアウト</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" style="font-size:0.8rem; letter-spacing:0.1em; color:#4A5859; text-decoration:none;">ログイン</a>
                    <a href="{{ route('register') }}" style="font-size:0.8rem; letter-spacing:0.1em; color:#fff; background:#4A5859; padding:0.5rem 1.25rem; border-radius:2px; text-decoration:none; transition:opacity .2s;" onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'">新規登録</a>
                @endauth
            </div>
            {{-- Mobile menu button --}}
            <button id="mobile-menu-btn" class="md:hidden" style="background:none; border:none; cursor:pointer; color:#4A5859;">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </nav>
        {{-- Mobile menu --}}
        <div id="mobile-menu" style="display:none; border-top:1px solid #E0E0D8; background:#F5F5F0; padding:1rem 1.5rem;">
            <div class="flex flex-col gap-4">
                <a href="{{ route('diagnosis') }}" style="font-size:0.85rem; color:#4A5859; text-decoration:none;">肌質診断</a>
                <a href="{{ route('products.index') }}" style="font-size:0.85rem; color:#4A5859; text-decoration:none;">商品一覧</a>
                @auth
                    <a href="{{ route('mypage') }}" style="font-size:0.85rem; color:#4A5859; text-decoration:none;">マイページ</a>
                @else
                    <a href="{{ route('login') }}" style="font-size:0.85rem; color:#4A5859; text-decoration:none;">ログイン</a>
                    <a href="{{ route('register') }}" style="font-size:0.85rem; color:#4A5859; text-decoration:none;">新規登録</a>
                @endauth
            </div>
        </div>
    </header>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div style="background:#4A5859; color:#fff; text-align:center; padding:0.75rem; font-size:0.85rem; letter-spacing:0.05em;">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div style="background:#c0392b; color:#fff; text-align:center; padding:0.75rem; font-size:0.85rem; letter-spacing:0.05em;">
        {{ session('error') }}
    </div>
    @endif

    <main>
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer style="background:#2E3A3B; color:#B0BFBF; margin-top:6rem; padding:3rem 1.5rem;">
        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                <div>
                    <p style="font-family:'Noto Serif JP', serif; font-size:0.9rem; letter-spacing:0.15em; color:#F5F5F0; margin-bottom:0.75rem;">LUMIÈRE BOTANIQUE</p>
                    <p style="font-size:0.75rem; line-height:1.8;">30〜40代の働く女性のために、<br>自然の力で肌本来の美しさを引き出す<br>オーガニックコスメ定期便です。</p>
                </div>
                <div>
                    <p style="font-size:0.7rem; letter-spacing:0.15em; color:#F5F5F0; margin-bottom:0.75rem; text-transform:uppercase;">Links</p>
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('diagnosis') }}" style="font-size:0.75rem; color:#B0BFBF; text-decoration:none;">肌質診断</a>
                        <a href="{{ route('products.index') }}" style="font-size:0.75rem; color:#B0BFBF; text-decoration:none;">商品一覧</a>
                        <a href="#" style="font-size:0.75rem; color:#B0BFBF; text-decoration:none;">特定商取引法に基づく表記</a>
                        <a href="#" style="font-size:0.75rem; color:#B0BFBF; text-decoration:none;">プライバシーポリシー</a>
                    </div>
                </div>
                <div>
                    <p style="font-size:0.7rem; letter-spacing:0.15em; color:#F5F5F0; margin-bottom:0.75rem; text-transform:uppercase;">Contact</p>
                    <p style="font-size:0.75rem; line-height:1.8;">info@lumiere-botanique.jp<br>受付時間：平日 10:00〜17:00</p>
                </div>
            </div>
            <div style="border-top:1px solid #3E4A4B; padding-top:1.5rem; text-align:center;">
                <p style="font-size:0.65rem; letter-spacing:0.1em;">© 2024 LUMIÈRE BOTANIQUE. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
        });
    </script>
    @stack('scripts')
</body>
</html>
