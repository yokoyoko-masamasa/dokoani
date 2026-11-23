<section>
    <header>
        {{-- 左線つきの見出し部品を読み込む --}}
        <x-section-heading>{{ __('Update Password') }}</x-section-heading>

        <p class="mt-1 text-sm text-gray-800">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    {{-- パスワード変更はPUTで送る。@csrf は不正な送信を防ぐ合言葉 --}}
    <form method="post" action="{{ route('password.update') }}" class="mt-6">
        @csrf
        @method('put')

        <div>
            {{-- ラベル・入力欄・エラー表示の部品を読み込む --}}
            {{-- エラーは updatePassword 名で受け、退会側と混ぜない --}}
            <x-input-label for="update_password_current_password" :value="__('Current Password')" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="update_password_password" :value="__('New Password')" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6 flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            {{-- 変更に成功した直後だけ、完了メッセージを2秒出す --}}
            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-800"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
