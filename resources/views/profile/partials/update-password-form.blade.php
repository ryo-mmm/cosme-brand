<section>
    <header style="margin-bottom:1.5rem; padding-bottom:1rem; border-bottom:1px solid #E8E4DC;">
        <h2 style="font-family:'Noto Serif JP', serif; font-size:1rem; font-weight:400; color:#2E3A3B;">
            パスワード変更
        </h2>
        <p style="font-size:0.78rem; color:#8A9899; margin-top:0.4rem; line-height:1.7;">
            セキュリティのため、推測されにくい長いパスワードをお使いください。
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" style="display:flex; flex-direction:column; gap:1.25rem;">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" style="display:block; font-size:0.75rem; color:#8A9899; margin-bottom:0.4rem;">現在のパスワード</label>
            <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password"
                style="width:100%; padding:0.75rem 1rem; border:1px solid #D8D4CC; border-radius:2px; font-size:0.85rem; background:#fff; outline:none; transition:border .2s; box-sizing:border-box; font-family:'Noto Sans JP', sans-serif;"
                onfocus="this.style.borderColor='#4A5859'" onblur="this.style.borderColor='#D8D4CC'">
            @error('current_password', 'updatePassword')
                <p style="font-size:0.75rem; color:#c0392b; margin-top:0.4rem;">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="update_password_password" style="display:block; font-size:0.75rem; color:#8A9899; margin-bottom:0.4rem;">新しいパスワード</label>
            <input id="update_password_password" name="password" type="password" autocomplete="new-password"
                style="width:100%; padding:0.75rem 1rem; border:1px solid #D8D4CC; border-radius:2px; font-size:0.85rem; background:#fff; outline:none; transition:border .2s; box-sizing:border-box; font-family:'Noto Sans JP', sans-serif;"
                onfocus="this.style.borderColor='#4A5859'" onblur="this.style.borderColor='#D8D4CC'">
            @error('password', 'updatePassword')
                <p style="font-size:0.75rem; color:#c0392b; margin-top:0.4rem;">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="update_password_password_confirmation" style="display:block; font-size:0.75rem; color:#8A9899; margin-bottom:0.4rem;">新しいパスワード（確認）</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                style="width:100%; padding:0.75rem 1rem; border:1px solid #D8D4CC; border-radius:2px; font-size:0.85rem; background:#fff; outline:none; transition:border .2s; box-sizing:border-box; font-family:'Noto Sans JP', sans-serif;"
                onfocus="this.style.borderColor='#4A5859'" onblur="this.style.borderColor='#D8D4CC'">
            @error('password_confirmation', 'updatePassword')
                <p style="font-size:0.75rem; color:#c0392b; margin-top:0.4rem;">{{ $message }}</p>
            @enderror
        </div>

        <div style="display:flex; align-items:center; gap:1rem; padding-top:0.5rem;">
            <button type="submit"
                style="background:#4A5859; color:#fff; padding:0.75rem 1.75rem; font-size:0.8rem; letter-spacing:0.1em; border:none; border-radius:2px; cursor:pointer; transition:opacity .2s; font-family:'Noto Sans JP', sans-serif;"
                onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                パスワードを変更する
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                   style="font-size:0.8rem; color:#4A5859;">
                    パスワードを変更しました。
                </p>
            @endif
        </div>
    </form>
</section>
