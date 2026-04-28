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
<body x-data="{ drawerOpen: false }" style="background-color:#F5F5F0; color:#2E3A3B; font-family:'Noto Sans JP', sans-serif;">

    {{-- Drawer overlay --}}
    <div x-show="drawerOpen"
         x-cloak
         @click="drawerOpen = false"
         style="position:fixed; inset:0; background:rgba(0,0,0,0.35); z-index:100; transition:opacity .25s;"
         x-transition:enter="transition ease-out duration-250"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>

    {{-- Drawer --}}
    <div x-show="drawerOpen"
         x-cloak
         style="position:fixed; top:0; left:0; height:100%; width:260px; background:#F5F5F0; z-index:101; box-shadow:4px 0 20px rgba(0,0,0,0.1);"
         x-transition:enter="transition ease-out duration-250"
         x-transition:enter-start="transform -translate-x-full"
         x-transition:enter-end="transform translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="transform translate-x-0"
         x-transition:leave-end="transform -translate-x-full">

        {{-- Drawer header --}}
        <div style="display:flex; align-items:center; justify-content:space-between; padding:1.25rem 1.5rem; border-bottom:1px solid #E0E0D8;">
            <span style="font-family:'Noto Serif JP', serif; font-size:0.85rem; letter-spacing:0.15em; color:#4A5859;">MENU</span>
            <button @click="drawerOpen = false" style="background:none; border:none; cursor:pointer; color:#8A9899; padding:0.25rem;">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Drawer links --}}
        <div style="padding:1.5rem; display:flex; flex-direction:column; gap:0;">
            <a href="{{ route('diagnosis') }}" style="font-size:0.85rem; letter-spacing:0.05em; color:#4A5859; text-decoration:none; padding:1rem 0; border-bottom:1px solid #F0EDE6; display:block;">肌質診断</a>
            <a href="{{ route('products.index') }}" style="font-size:0.85rem; letter-spacing:0.05em; color:#4A5859; text-decoration:none; padding:1rem 0; border-bottom:1px solid #F0EDE6; display:block;">商品一覧</a>
            @auth
                <a href="{{ route('mypage') }}" style="font-size:0.85rem; letter-spacing:0.05em; color:#4A5859; text-decoration:none; padding:1rem 0; border-bottom:1px solid #F0EDE6; display:block;">マイページ</a>
                <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                    @csrf
                    <button type="submit" style="font-size:0.85rem; letter-spacing:0.05em; color:#8A9899; background:none; border:none; cursor:pointer; padding:1rem 0; display:block; width:100%; text-align:left;">ログアウト</button>
                </form>
            @else
                <a href="{{ route('login') }}" style="font-size:0.85rem; letter-spacing:0.05em; color:#4A5859; text-decoration:none; padding:1rem 0; border-bottom:1px solid #F0EDE6; display:block;">ログイン</a>
                <a href="{{ route('register') }}" style="font-size:0.85rem; letter-spacing:0.05em; color:#4A5859; text-decoration:none; padding:1rem 0; display:block;">新規登録</a>
            @endauth
        </div>
    </div>

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
            {{-- Mobile hamburger button --}}
            <button @click="drawerOpen = true" class="md:hidden" style="background:none; border:none; cursor:pointer; color:#4A5859;">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </nav>
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
    <footer style="background:#2E3A3B; color:#B0BFBF; padding:3rem 1.5rem;">
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
                        <a href="{{ route('legal.tokushoho') }}" style="font-size:0.75rem; color:#B0BFBF; text-decoration:none;">特定商取引法に基づく表記</a>
                        <a href="{{ route('legal.privacy') }}" style="font-size:0.75rem; color:#B0BFBF; text-decoration:none;">プライバシーポリシー</a>
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

    @stack('scripts')
</body>
</html>
