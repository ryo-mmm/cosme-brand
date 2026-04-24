<x-app-layout>
    <x-slot name="title">パスワードリセット</x-slot>

    <div style="min-height:80vh; display:flex; align-items:center; justify-content:center; padding:3rem 1.5rem;">
        <div style="width:100%; max-width:460px;">

            <div style="text-align:center; margin-bottom:3rem;">
                <a href="{{ route('top') }}" style="text-decoration:none;">
                    <p style="font-family:'Noto Serif JP', serif; font-size:1.2rem; letter-spacing:0.2em; color:#4A5859; font-weight:300;">LUMIÈRE BOTANIQUE</p>
                </a>
                <div style="width:1px; height:32px; background:linear-gradient(to bottom, #D8D4CC, transparent); margin:1.5rem auto 0;"></div>
                <h1 style="font-family:'Noto Serif JP', serif; font-size:1.4rem; font-weight:300; color:#2E3A3B; margin-top:1.5rem;">パスワードのリセット</h1>
            </div>

            <div style="background:#fff; border:1px solid #E8E4DC; border-radius:4px; padding:2.5rem;">
                <p style="font-size:0.82rem; color:#5A6B6C; line-height:1.9; margin-bottom:2rem;">
                    登録済みのメールアドレスを入力してください。<br>パスワードリセット用のリンクをお送りします。
                </p>

                @if (session('status'))
                    <div style="background:#F0EDE6; border-left:3px solid #4A5859; padding:0.875rem 1rem; font-size:0.8rem; color:#4A5859; margin-bottom:1.5rem; border-radius:0 2px 2px 0;">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div style="margin-bottom:1.75rem;">
                        <label for="email" style="display:block; font-size:0.75rem; letter-spacing:0.1em; color:#8A9899; margin-bottom:0.5rem;">メールアドレス</label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            style="width:100%; padding:0.875rem 1rem; border:1px solid {{ $errors->has('email') ? '#c0392b' : '#D8D4CC' }}; border-radius:2px; font-size:0.9rem; color:#2E3A3B; background:#fff; outline:none; transition:border-color .2s; box-sizing:border-box;"
                            onfocus="this.style.borderColor='#4A5859'"
                            onblur="this.style.borderColor='{{ $errors->has('email') ? '#c0392b' : '#D8D4CC' }}'"
                        >
                        @error('email')
                            <p style="font-size:0.75rem; color:#c0392b; margin-top:0.4rem;">{{ $message }}</p>
                        @enderror
                    </div>
                    <button
                        type="submit"
                        style="width:100%; background:#4A5859; color:#fff; padding:1rem; font-size:0.85rem; letter-spacing:0.15em; border:none; border-radius:2px; cursor:pointer; transition:opacity .2s; font-family:'Noto Sans JP', sans-serif;"
                        onmouseover="this.style.opacity='.85'"
                        onmouseout="this.style.opacity='1'"
                    >
                        リセットリンクを送信
                    </button>
                </form>
            </div>

            <p style="text-align:center; margin-top:1.5rem; font-size:0.8rem; color:#8A9899;">
                <a href="{{ route('login') }}" style="color:#4A5859; text-decoration:none; border-bottom:1px solid #C4A882; padding-bottom:1px;">ログインに戻る</a>
            </p>
        </div>
    </div>
</x-app-layout>
