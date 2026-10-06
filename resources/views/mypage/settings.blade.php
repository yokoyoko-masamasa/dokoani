<x-app-layout>
    <div class="px-4 md:px-6 py-8">
        {{-- 検索バー共通コンポーネントを読み込む --}}
        <x-search-bar />
    </div>

    {{-- マイページのタブ共通コンポーネントを読み込む --}}
    <x-mypage-tabs />

    <section class="px-4 md:px-6 py-8">
        <h1 class="mb-4 text-lg font-bold text-gray-800">マイサブスク設定</h1>

        {{-- 契約を1件ずつ並べ、無ければ0件の文言を出す --}}
        @forelse ($subscriptions as $sub)
            <div class="flex items-center justify-between gap-4 py-4 border-b border-gray-200">
                <div>
                    <p class="text-sm font-bold text-gray-800">{{ $sub->streamingService->name }}</p>
                    <p class="mt-1 text-sm text-gray-800">月額 {{ number_format($sub->price) }}円</p>
                </div>

                <div class="flex gap-2">
                    {{-- 料金変更画面へのリンク（仮の URL） --}}
                    <a href="{{ url('/mypage/settings/subscriptions/'.$sub->id.'/edit') }}" class="px-3 py-1 rounded-md text-sm bg-white text-gray-800 border border-gray-300">料金変更</a>

                    {{-- 契約を削除するDELETE（仮の URL） --}}
                    <form method="POST" action="{{ url('/mypage/settings/subscriptions/'.$sub->id) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-1 rounded-md text-sm bg-white text-gray-800 border border-gray-300">削除</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-gray-800">契約中のサービスがありません</p>
        @endforelse

        {{-- サービス追加画面へのリンク --}}
        <a href="{{ route('subscriptions.create') }}" class="mt-4 block px-3 py-2 rounded-md text-center text-sm bg-white text-gray-800 border border-gray-300">＋ サービスを追加</a>

        <h2 class="mt-8 mb-4 text-lg font-bold text-gray-800">アカウント設定</h2>

        {{-- 退会の入口。Breeze 標準のアカウント画面へ移動する --}}
        <a href="{{ route('profile.edit') }}" class="text-sm text-gray-800 underline">退会する</a>
    </section>
</x-app-layout>
