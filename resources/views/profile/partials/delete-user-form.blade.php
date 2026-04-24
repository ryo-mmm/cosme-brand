<section>
    <header style="margin-bottom:1.5rem; padding-bottom:1rem; border-bottom:1px solid #E8E4DC;">
        <h2 style="font-family:'Noto Serif JP', serif; font-size:1rem; font-weight:400; color:#2E3A3B;">
            アカウント削除
        </h2>
        <p style="font-size:0.78rem; color:#8A9899; margin-top:0.4rem; line-height:1.7;">
            アカウントを削除すると、すべてのデータが完全に消去されます。削除前に必要なデータをお手元に保存してください。
        </p>
    </header>

    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        style="background:#fff; border:1px solid #c0392b; color:#c0392b; padding:0.75rem 1.75rem; font-size:0.8rem; letter-spacing:0.1em; border-radius:2px; cursor:pointer; transition:all .2s; font-family:'Noto Sans JP', sans-serif;"
        onmouseover="this.style.background='#c0392b'; this.style.color='#fff'" onmouseout="this.style.background='#fff'; this.style.color='#c0392b'">
        アカウントを削除する
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" style="padding:2rem;">
            @csrf
            @method('delete')

            <h2 style="font-family:'Noto Serif JP', serif; font-size:1.1rem; font-weight:400; color:#2E3A3B; margin-bottom:0.75rem;">
                本当にアカウントを削除しますか？
            </h2>

            <p style="font-size:0.8rem; color:#8A9899; line-height:1.8; margin-bottom:1.5rem;">
                アカウントを削除すると、すべてのデータが完全に消去されます。<br>
                削除を確定するには、現在のパスワードを入力してください。
            </p>

            <div style="margin-bottom:1.5rem;">
                <label for="password" style="display:block; font-size:0.75rem; color:#8A9899; margin-bottom:0.4rem;">パスワード</label>
                <input id="password" name="password" type="password"
                    placeholder="パスワードを入力"
                    style="width:75%; padding:0.75rem 1rem; border:1px solid #D8D4CC; border-radius:2px; font-size:0.85rem; background:#fff; outline:none; transition:border .2s; box-sizing:border-box; font-family:'Noto Sans JP', sans-serif;"
                    onfocus="this.style.borderColor='#4A5859'" onblur="this.style.borderColor='#D8D4CC'">
                @error('password', 'userDeletion')
                    <p style="font-size:0.75rem; color:#c0392b; margin-top:0.4rem;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display:flex; justify-content:flex-end; gap:0.75rem;">
                <button type="button" x-on:click="$dispatch('close')"
                    style="background:#fff; border:1px solid #D8D4CC; color:#4A5859; padding:0.75rem 1.5rem; font-size:0.8rem; letter-spacing:0.05em; border-radius:2px; cursor:pointer; font-family:'Noto Sans JP', sans-serif;"
                    onmouseover="this.style.borderColor='#4A5859'" onmouseout="this.style.borderColor='#D8D4CC'">
                    キャンセル
                </button>
                <button type="submit"
                    style="background:#c0392b; border:1px solid #c0392b; color:#fff; padding:0.75rem 1.5rem; font-size:0.8rem; letter-spacing:0.05em; border-radius:2px; cursor:pointer; transition:opacity .2s; font-family:'Noto Sans JP', sans-serif;"
                    onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                    削除する
                </button>
            </div>
        </form>
    </x-modal>
</section>
