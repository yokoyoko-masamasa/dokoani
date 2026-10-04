<x-app-layout>
    <div class="px-4 md:px-6 py-8">
        {{-- 検索バー共通コンポーネントを読み込む --}}
        <x-search-bar />
    </div>

    <section class="px-4 md:px-6 pb-8">
        <h1 class="mb-4 text-lg font-bold text-gray-800">検索結果 {{ $animeTitles->total() }}件</h1>

        {{-- 作品があればカードを並べ、無ければ0件の文言を出す --}}
        @forelse ($animeTitles as $anime)
            @if ($loop->first)
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            @endif

            {{-- クリックでアニメ詳細画面（anime.show）へ遷移する --}}
            <a href="{{ route('anime.show', ['animeTitle' => $anime]) }}" class="block">
                {{-- ポスター画像が無い作品は、灰色の枠で代用する --}}
                @if ($anime->poster_image_url)
                    <img src="{{ $anime->poster_image_url }}" alt="{{ $anime->title }}" class="w-full aspect-[2/3] object-cover rounded-md bg-gray-200">
                @else
                    <div class="w-full aspect-[2/3] rounded-md bg-gray-200"></div>
                @endif
                <p class="mt-2 text-sm text-gray-800">{{ $anime->title }}</p>

                {{-- 配信ロゴ共通コンポーネントを読み込む --}}
                <x-availability-logos :anime="$anime" />
            </a>

            @if ($loop->last)
                </div>
            @endif
        @empty
            <p class="text-gray-800">情報がありません</p>
            <p class="mt-2 text-sm text-gray-600">条件に一致する作品が見つかりませんでした。別のキーワードでお試しください。</p>
        @endforelse

        {{-- ページ送りのリンク。検索語は withQueryString で引き継ぐ --}}
        <div class="mt-6">
            {{ $animeTitles->links() }}
        </div>
    </section>
</x-app-layout>
