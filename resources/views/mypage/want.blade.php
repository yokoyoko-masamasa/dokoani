<x-app-layout>
    <div class="px-4 md:px-6 py-8">
        {{-- 検索バー共通コンポーネントを読み込む --}}
        <x-search-bar />
    </div>

    <section class="px-4 md:px-6 pb-8">
        <h1 class="mb-4 text-lg font-bold text-gray-800">見たいリスト</h1>

        {{-- 作品があれば1行ずつ並べ、無ければ0件の文言を出す --}}
        @forelse ($lists as $list)
            @php($anime = $list->animeTitle)
            <div class="flex gap-4 py-4 border-b border-gray-200">
                {{-- ポスターはクリックでアニメ詳細画面（anime.show）へ遷移する --}}
                <a href="{{ route('anime.show', $anime) }}" class="shrink-0">
                    {{-- ポスター画像が無い作品は「No Image」と表示する --}}
                    @if ($anime->poster_image_url)
                        <img src="{{ $anime->poster_image_url }}" alt="{{ $anime->title }}" class="w-20 aspect-[2/3] object-cover rounded-md bg-gray-200">
                    @else
                        <div class="w-20 aspect-[2/3] rounded-md bg-gray-200 flex items-center justify-center text-xs text-gray-500">
                            No Image
                        </div>
                    @endif
                </a>

                <div class="flex-1">
                    <a href="{{ route('anime.show', $anime) }}" class="text-sm font-bold text-gray-800">{{ $anime->title }}</a>

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

                    <div class="mt-3 flex gap-2">
                        {{-- 視聴済みへ変更するPUT。@csrf は不正な送信を防ぐ合言葉 --}}
                        <form method="POST" action="{{ route('list.update', $anime) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="watched">
                            <button type="submit" class="px-3 py-1 rounded-md text-sm bg-white text-gray-800 border border-gray-300">視聴済みにする</button>
                        </form>

                        {{-- リストから外すDELETE --}}
                        <form method="POST" action="{{ route('list.destroy', $anime) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1 rounded-md text-sm bg-white text-gray-800 border border-gray-300">削除</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-gray-800">見たいリストに作品がありません</p>
        @endforelse
    </section>
</x-app-layout>
