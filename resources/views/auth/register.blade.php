<x-app-layout>
    <x-slot name="title">会員登録</x-slot>

    <div style="min-height:80vh; display:flex; align-items:center; justify-content:center; padding:3rem 1.5rem;">
        <div style="width:100%; max-width:460px;">

            {{-- Brand header --}}
            <div style="text-align:center; margin-bottom:3rem;">
                <a href="{{ route('top') }}" style="text-decoration:none;">
                    <p style="font-family:'Noto Serif JP', serif; font-size:1.2rem; letter-spacing:0.2em; color:#4A5859; font-weight:300;">LUMIÈRE BOTANIQUE</p>
                    <p style="font-size:0.6rem; letter-spacing:0.2em; color:#8A9899; margin-top:0.3rem;">オーガニックコスメ定期便</p>
                </a>
                <div style="width:1px; height:32px; background:linear-gradient(to bottom, #D8D4CC, transparent); margin:1.5rem auto 0;"></div>
                <h1 style="font-family:'Noto Serif JP', serif; font-size:1.4rem; font-weight:300; color:#2E3A3B; margin-top:1.5rem;">新規会員登録</h1>
                <p style="font-size:0.78rem; color:#8A9899; margin-top:0.75rem; line-height:1.7;">登録無料・解約いつでも可能</p>
            </div>

            {{-- Card --}}
            <div style="background:#fff; border:1px solid #E8E4DC; border-radius:4px; padding:2.5rem;">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    {{-- Name --}}
                    <div style="margin-bottom:1.5rem;">
                        <label for="name" style="display:block; font-size:0.75rem; letter-spacing:0.1em; color:#8A9899; margin-bottom:0.5rem;">お名前</label>
                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="山田 花子"
                            style="width:100%; padding:0.875rem 1rem; border:1px solid {{ $errors->has('name') ? '#c0392b' : '#D8D4CC' }}; border-radius:2px; font-size:0.9rem; color:#2E3A3B; background:#fff; outline:none; transition:border-color .2s; box-sizing:border-box;"
                            onfocus="this.style.borderColor='#4A5859'"
                            onblur="this.style.borderColor='{{ $errors->has('name') ? '#c0392b' : '#D8D4CC' }}'"
                        >
                        @error('name')
                            <p style="font-size:0.75rem; color:#c0392b; margin-top:0.4rem;">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div style="margin-bottom:1.5rem;">
                        <label for="email" style="display:block; font-size:0.75rem; letter-spacing:0.1em; color:#8A9899; margin-bottom:0.5rem;">メールアドレス</label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="username"
                            placeholder="example@email.com"
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
                        <label for="password" style="display:block; font-size:0.75rem; letter-spacing:0.1em; color:#8A9899; margin-bottom:0.5rem;">パスワード <span style="font-size:0.68rem; color:#B0BFBF;">（8文字以上）</span></label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            style="width:100%; padding:0.875rem 1rem; border:1px solid {{ $errors->has('password') ? '#c0392b' : '#D8D4CC' }}; border-radius:2px; font-size:0.9rem; color:#2E3A3B; background:#fff; outline:none; transition:border-color .2s; box-sizing:border-box;"
                            onfocus="this.style.borderColor='#4A5859'"
                            onblur="this.style.borderColor='{{ $errors->has('password') ? '#c0392b' : '#D8D4CC' }}'"
                        >
                        @error('password')
                            <p style="font-size:0.75rem; color:#c0392b; margin-top:0.4rem;">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div style="margin-bottom:2rem;">
                        <label for="password_confirmation" style="display:block; font-size:0.75rem; letter-spacing:0.1em; color:#8A9899; margin-bottom:0.5rem;">パスワード（確認）</label>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            style="width:100%; padding:0.875rem 1rem; border:1px solid {{ $errors->has('password_confirmation') ? '#c0392b' : '#D8D4CC' }}; border-radius:2px; font-size:0.9rem; color:#2E3A3B; background:#fff; outline:none; transition:border-color .2s; box-sizing:border-box;"
                            onfocus="this.style.borderColor='#4A5859'"
                            onblur="this.style.borderColor='{{ $errors->has('password_confirmation') ? '#c0392b' : '#D8D4CC' }}'"
                        >
                        @error('password_confirmation')
                            <p style="font-size:0.75rem; color:#c0392b; margin-top:0.4rem;">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Terms --}}
                    <div style="margin-bottom:2rem; padding:1rem; background:#F5F5F0; border-radius:2px;">
                        <p style="font-size:0.75rem; color:#8A9899; line-height:1.8; text-align:center;">
                            登録することで
                            <a href="#" style="color:#4A5859; text-decoration:none; border-bottom:1px solid #C4A882;">利用規約</a>
                            および
                            <a href="#" style="color:#4A5859; text-decoration:none; border-bottom:1px solid #C4A882;">プライバシーポリシー</a>
                            に同意したものとみなされます。
                        </p>
                    </div>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        style="width:100%; background:#4A5859; color:#fff; padding:1rem; font-size:0.85rem; letter-spacing:0.15em; border:none; border-radius:2px; cursor:pointer; transition:opacity .2s; font-family:'Noto Sans JP', sans-serif;"
                        onmouseover="this.style.opacity='.85'"
                        onmouseout="this.style.opacity='1'"
                    >
                        会員登録する
                    </button>
                </form>
            </div>

            {{-- Login link --}}
            <p style="text-align:center; margin-top:1.5rem; font-size:0.8rem; color:#8A9899;">
                すでにアカウントをお持ちの方は
                <a href="{{ route('login') }}" style="color:#4A5859; text-decoration:none; border-bottom:1px solid #C4A882; padding-bottom:1px; transition:opacity .2s;" onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">ログイン</a>
            </p>
        </div>
    </div>
</x-app-layout>
