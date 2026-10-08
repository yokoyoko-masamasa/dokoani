<x-app-layout>
    <section class="px-4 md:px-6 py-8">
        {{-- 設定画面へ戻るリンク --}}
        <a href="{{ route('mypage.settings') }}" class="text-sm text-gray-800">← 設定に戻る</a>

        {{-- 退会フォームの部分ビューを読み込む --}}
        @include('profile.partials.delete-user-form')
    </section>
</x-app-layout>
