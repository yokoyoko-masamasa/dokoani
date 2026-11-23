<x-app-layout>
    <x-slot:title>アカウント設定</x-slot:title>

    <section class="px-4 md:px-6 py-8">
        {{-- 設定画面へ戻るリンク --}}
        <a href="{{ route('mypage.settings') }}" class="inline-flex items-center min-h-6 text-sm text-gray-800">← 設定に戻る</a>

        <h1 class="mt-4 mb-6 text-2xl font-bold text-gray-800">アカウント設定</h1>

        {{-- パスワード変更フォームの部分ビューを読み込む --}}
        <div class="pb-8 border-b border-gray-300">
            @include('profile.partials.update-password-form')
        </div>

        {{-- 退会フォームの部分ビューを読み込む --}}
        <div class="mt-8">
            @include('profile.partials.delete-user-form')
        </div>
    </section>
</x-app-layout>
