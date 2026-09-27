<x-app-layout>
    <div class="px-4 md:px-6 py-8">
        {{-- 検索バー共通コンポーネントを読み込む --}}
        <x-search-bar />
    </div>

    <section class="px-4 md:px-6 pb-8">
        <h1 class="text-lg font-bold text-gray-800">{{ $animeTitle->title }}</h1>

        <div class="mt-4 flex flex-col md:flex-row gap-6">
            {{-- ポスター画像が無い作品は「No Image」と表示する --}}
            @if ($animeTitle->poster_image_url)
                <img src="{{ $animeTitle->poster_image_url }}" alt="{{ $animeTitle->title }}" class="w-48 aspect-[2/3] object-cover rounded-md bg-gray-200">
            @else
                <div class="w-48 aspect-[2/3] rounded-md bg-gray-200 flex items-center justify-center text-gray-500">
                    No Image
                </div>
            @endif

            <div>
                {{-- あらすじが無い作品は文言で代用する --}}
                @if ($animeTitle->synopsis)
                    <p class="text-sm text-gray-800">{{ $animeTitle->synopsis }}</p>
                @else
                    <p class="text-sm text-gray-500">あらすじ情報がありません</p>
                @endif
            </div>
        </div>
    </section>
</x-app-layout>
