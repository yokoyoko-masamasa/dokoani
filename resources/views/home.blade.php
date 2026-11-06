<x-app-layout>
    <section class="px-4 md:px-6 py-8">
        <h1 class="text-2xl font-bold text-gray-800">そのアニメ、契約中のサブスクで見られる？</h1>

        <p class="mt-2 max-w-2xl text-base text-gray-800">検索すると、配信中のサービスがわかります。契約中のサブスクを登録すると、見たいリストのうち見られる本数もわかります。</p>

        {{-- 未ログインのときだけ、会員登録とログインのボタンを出す --}}
        @guest
            <div class="mt-6 flex gap-2">
                {{-- 会員登録画面へ移る。主要ボタンなので塗りにする --}}
                <a href="{{ route('register') }}" class="flex items-center justify-center min-h-11 px-4 py-2 rounded-md text-sm bg-primary-dark text-white border border-primary-dark">会員登録</a>
                {{-- ログイン画面へ移る。副ボタンなので枠線にする --}}
                <a href="{{ route('login') }}" class="flex items-center justify-center min-h-11 px-4 py-2 rounded-md text-sm bg-white text-gray-800 border border-gray-300">ログイン</a>
            </div>
        @endguest
    </section>

    <div class="px-4 md:px-6 pb-8">
        {{-- 検索バー共通コンポーネントを読み込む --}}
        <x-search-bar />
    </div>

    <section class="px-4 md:px-6 pb-8">
        <x-section-heading class="mb-4">人気のアニメ</x-section-heading>

        {{-- 作品があればカードを並べ、無ければ文言だけ出す --}}
        @forelse ($popularTitles as $title)
            @if ($loop->first)
                <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
            @endif

            <div>
                {{-- ポスター画像が無い作品は、灰色の枠で代用する --}}
                @if ($title->poster_image_url)
                    <img src="{{ $title->poster_image_url }}" alt="{{ $title->title }}" class="w-full aspect-[2/3] object-cover rounded-md bg-gray-200">
                @else
                    <div class="w-full aspect-[2/3] rounded-md bg-gray-200"></div>
                @endif
                <p class="mt-2 text-sm text-gray-800">{{ $title->title }}</p>
            </div>

            @if ($loop->last)
                </div>
            @endif
        @empty
            <p class="text-gray-600">表示できる作品がありません</p>
        @endforelse
    </section>

    <section class="px-4 md:px-6 pb-8">
        <x-section-heading class="mb-4">使い方</x-section-heading>
        <ol class="list-decimal list-inside space-y-2 text-gray-800">
            <li>アニメ名を検索する</li>
            <li>配信サービスと配信状況を確認する</li>
            <li>見たいリストに追加して管理する</li>
        </ol>
    </section>
</x-app-layout>
