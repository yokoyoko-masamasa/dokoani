<x-guest-layout>
    <x-slot:title>パスワードの再設定</x-slot:title>

    <div class="mb-4 text-sm text-gray-600">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    {{-- 送信できたときの緑の文字。部品を読み込む --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    {{-- 再設定メールの申請は POST で送る。@csrf は不正な送信を防ぐ合言葉 --}}
    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            {{-- ラベル・入力欄・エラー表示の部品を読み込む --}}
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
