<x-app-layout>
    <x-slot name="title">アカウント設定</x-slot>

    <div class="max-w-4xl mx-auto px-6 py-12">

        <div style="margin-bottom:3rem;">
            <p style="font-size:0.65rem; letter-spacing:0.3em; color:#8A9899; margin-bottom:0.5rem; text-transform:uppercase;">Account Settings</p>
            <h1 style="font-family:'Noto Serif JP', serif; font-size:1.75rem; font-weight:300; color:#2E3A3B;">アカウント設定</h1>
        </div>

        <div style="display:flex; flex-direction:column; gap:1.5rem;">
            <div style="background:#fff; border:1px solid #E8E4DC; border-radius:4px; padding:2rem 2.5rem;">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div style="background:#fff; border:1px solid #E8E4DC; border-radius:4px; padding:2rem 2.5rem;">
                @include('profile.partials.update-password-form')
            </div>

            <div style="background:#fff; border:1px solid #E8E4DC; border-radius:4px; padding:2rem 2.5rem;">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
