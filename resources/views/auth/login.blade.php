<x-app-layout>
    <x-slot name="title">ログイン</x-slot>

    <div style="min-height:80vh; display:flex; align-items:center; justify-content:center; padding:3rem 1.5rem;">
        <div style="width:100%; max-width:460px;">

            {{-- Brand header --}}
            <div style="text-align:center; margin-bottom:3rem;">
                <a href="{{ route('top') }}" style="text-decoration:none;">
                    <p style="font-family:'Noto Serif JP', serif; font-size:1.2rem; letter-spacing:0.2em; color:#4A5859; font-weight:300;">LUMIÈRE BOTANIQUE</p>
                    <p style="font-size:0.6rem; letter-spacing:0.2em; color:#8A9899; margin-top:0.3rem;">オーガニックコスメ定期便</p>
                </a>
                <div style="width:1px; height:32px; background:linear-gradient(to bottom, #D8D4CC, transparent); margin:1.5rem auto 0;"></div>
                <h1 style="font-family:'Noto Serif JP', serif; font-size:1.4rem; font-weight:300; color:#2E3A3B; margin-top:1.5rem;">ログイン</h1>
            </div>

            {{-- Card --}}
            <div style="background:#fff; border:1px solid #E8E4DC; border-radius:4px; padding:2.5rem;">

                {{-- Session status --}}
                @if (session('status'))
                    <div style="background:#F0EDE6; border-left:3px solid #4A5859; padding:0.875rem 1rem; font-size:0.8rem; color:#4A5859; margin-bottom:1.5rem; border-radius:0 2px 2px 0;">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    {{-- Email --}}
                    <div style="margin-bottom:1.5rem;">
                        <label for="email" style="display:block; font-size:0.75rem; letter-spacing:0.1em; color:#8A9899; margin-bottom:0.5rem;">メールアドレス</label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            style="width:100%; padding:0.875rem 1rem; border:1px solid {{ $errors->has('email') ? '#c0392b' : '#D8D4CC' }}; border-radius:2px; font-size:0.9rem; color:#2E3A3B; background:#fff; outline:none; transition:border-color .2s; box-sizing:border-box;"
                            onfocus="this.style.borderColor='#4A5859'"
                            onblur="this.style.borderColor='{{ $errors->has('email') ? '#c0392b' : '#D8D4CC' }}'"
                        >
                        @error('email')
                            <p style="font-size:0.75rem; color:#c0392b; margin-top:0.4rem;">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div style="margin-bottom:1.5rem;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem;">
                            <label for="password" style="font-size:0.75rem; letter-spacing:0.1em; color:#8A9899;">パスワード</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" style="font-size:0.72rem; color:#8A9899; text-decoration:none; transition:color .2s;" onmouseover="this.style.color='#4A5859'" onmouseout="this.style.color='#8A9899'">
                                    パスワードをお忘れですか？
                                </a>
                            @endif
                        </div>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            style="width:100%; padding:0.875rem 1rem; border:1px solid {{ $errors->has('password') ? '#c0392b' : '#D8D4CC' }}; border-radius:2px; font-size:0.9rem; color:#2E3A3B; background:#fff; outline:none; transition:border-color .2s; box-sizing:border-box;"
                            onfocus="this.style.borderColor='#4A5859'"
                            onblur="this.style.borderColor='{{ $errors->has('password') ? '#c0392b' : '#D8D4CC' }}'"
                        >
                        @error('password')
                            <p style="font-size:0.75rem; color:#c0392b; margin-top:0.4rem;">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Remember me --}}
                    <div style="margin-bottom:2rem;">
                        <label style="display:flex; align-items:center; gap:0.6rem; cursor:pointer;">
                            <input
                                id="remember_me"
                                type="checkbox"
                                name="remember"
                                style="width:16px; height:16px; accent-color:#4A5859; cursor:pointer;"
                            >
                            <span style="font-size:0.8rem; color:#5A6B6C;">ログイン状態を保持する</span>
                        </label>
                    </div>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        style="width:100%; background:#4A5859; color:#fff; padding:1rem; font-size:0.85rem; letter-spacing:0.15em; border:none; border-radius:2px; cursor:pointer; transition:opacity .2s; font-family:'Noto Sans JP', sans-serif;"
                        onmouseover="this.style.opacity='.85'"
                        onmouseout="this.style.opacity='1'"
                    >
                        ログイン
                    </button>
                </form>
            </div>

            {{-- Register link --}}
            <p style="text-align:center; margin-top:1.5rem; font-size:0.8rem; color:#8A9899;">
                アカウントをお持ちでない方は
                <a href="{{ route('register') }}" style="color:#4A5859; text-decoration:none; border-bottom:1px solid #C4A882; padding-bottom:1px; transition:opacity .2s;" onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">新規会員登録</a>
            </p>
        </div>
    </div>
</x-app-layout>
