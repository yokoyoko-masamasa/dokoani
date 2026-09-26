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

            <div>
                {{-- ポスター画像が無い作品は、灰色の枠で代用する --}}
                @if ($anime->poster_image_url)
                    <img src="{{ $anime->poster_image_url }}" alt="{{ $anime->title }}" class="w-full aspect-[2/3] object-cover rounded-md bg-gray-200">
                @else
                    <div class="w-full aspect-[2/3] rounded-md bg-gray-200"></div>
                @endif
                <p class="mt-2 text-sm text-gray-800">{{ $anime->title }}</p>

                {{-- 同じサービスのロゴは1つにまとめる（flatrate と rent の重複対策） --}}
                @php($logos = $anime->availabilities->unique('streaming_service_id'))
                <div class="mt-2 flex flex-wrap gap-1">
                    {{-- 配信が無ければ1行だけ出し、あればロゴを並べる --}}
                    @forelse ($logos as $availability)
                        <img src="{{ $availability->streamingService->logo_image_url }}" alt="{{ $availability->streamingService->name }}" class="w-8 h-8 rounded">
                    @empty
                        <span class="text-xs text-gray-500">配信サービスなし</span>
                    @endforelse
                </div>
            </div>

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
