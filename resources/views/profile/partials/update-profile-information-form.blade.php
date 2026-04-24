<section>
    <header style="margin-bottom:1.5rem; padding-bottom:1rem; border-bottom:1px solid #E8E4DC;">
        <h2 style="font-family:'Noto Serif JP', serif; font-size:1rem; font-weight:400; color:#2E3A3B;">
            プロフィール情報
        </h2>
        <p style="font-size:0.78rem; color:#8A9899; margin-top:0.4rem; line-height:1.7;">
            名前とメールアドレスを変更できます。
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" style="display:flex; flex-direction:column; gap:1.25rem;">
        @csrf
        @method('patch')

        <div>
            <label for="name" style="display:block; font-size:0.75rem; color:#8A9899; margin-bottom:0.4rem;">お名前</label>
            <input id="name" name="name" type="text" required autofocus autocomplete="name"
                value="{{ old('name', $user->name) }}"
                style="width:100%; padding:0.75rem 1rem; border:1px solid #D8D4CC; border-radius:2px; font-size:0.85rem; background:#fff; outline:none; transition:border .2s; box-sizing:border-box; font-family:'Noto Sans JP', sans-serif;"
                onfocus="this.style.borderColor='#4A5859'" onblur="this.style.borderColor='#D8D4CC'">
            @error('name')
                <p style="font-size:0.75rem; color:#c0392b; margin-top:0.4rem;">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" style="display:block; font-size:0.75rem; color:#8A9899; margin-bottom:0.4rem;">メールアドレス</label>
            <input id="email" name="email" type="email" required autocomplete="username"
                value="{{ old('email', $user->email) }}"
                style="width:100%; padding:0.75rem 1rem; border:1px solid #D8D4CC; border-radius:2px; font-size:0.85rem; background:#fff; outline:none; transition:border .2s; box-sizing:border-box; font-family:'Noto Sans JP', sans-serif;"
                onfocus="this.style.borderColor='#4A5859'" onblur="this.style.borderColor='#D8D4CC'">
            @error('email')
                <p style="font-size:0.75rem; color:#c0392b; margin-top:0.4rem;">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div style="margin-top:0.75rem; padding:0.75rem 1rem; background:#FFF8F0; border:1px solid #E8D4B8; border-radius:2px;">
                    <p style="font-size:0.78rem; color:#8A6A3A;">
                        メールアドレスが未確認です。
                        <button form="send-verification"
                            style="background:none; border:none; cursor:pointer; font-size:0.78rem; color:#C4A882; text-decoration:underline; padding:0; font-family:'Noto Sans JP', sans-serif;">
                            確認メールを再送する
                        </button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p style="font-size:0.75rem; color:#4A5859; margin-top:0.4rem;">確認メールを送信しました。</p>
                    @endif
                </div>
            @endif
        </div>

        <div style="display:flex; align-items:center; gap:1rem; padding-top:0.5rem;">
            <button type="submit"
                style="background:#4A5859; color:#fff; padding:0.75rem 1.75rem; font-size:0.8rem; letter-spacing:0.1em; border:none; border-radius:2px; cursor:pointer; transition:opacity .2s; font-family:'Noto Sans JP', sans-serif;"
                onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                保存する
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                   style="font-size:0.8rem; color:#4A5859;">
                    保存しました。
                </p>
            @endif
        </div>
    </form>
</section>
