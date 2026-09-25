<x-app-layout>
    <h1 class="px-4 md:px-6 py-8 text-2xl font-bold text-gray-800">ドコアニ</h1>

    <div class="px-4 md:px-6 pb-8">
        {{-- 検索バー共通コンポーネントを読み込む --}}
        <x-search-bar />
    </div>

    <section class="px-4 md:px-6 pb-8">
        <h2 class="mb-4 text-lg font-bold text-gray-800">人気のアニメ</h2>

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
        <h2 class="mb-4 text-lg font-bold text-gray-800">使い方</h2>
        <ol class="list-decimal list-inside space-y-2 text-gray-800">
            <li>アニメ名を検索する</li>
            <li>配信サービスと配信状況を確認する</li>
            <li>見たいリストに追加して管理する</li>
        </ol>
    </section>
</x-app-layout>
