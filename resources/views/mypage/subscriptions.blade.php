<x-app-layout>
    <div class="px-4 md:px-6 py-8">
        {{-- 検索バー共通コンポーネントを読み込む --}}
        <x-search-bar />
    </div>

    <section class="px-4 md:px-6 pb-8">
        <h1 class="mb-4 text-lg font-bold text-gray-800">契約状況</h1>

        {{-- 契約中サービスを1行ずつ並べ、無ければ0件の文言を出す --}}
        @forelse ($services as $row)
            <div class="flex items-center gap-4 py-4 border-b border-gray-200">
                {{-- ロゴが無いサービスは画像を出さず、名前だけ表示する --}}
                @if ($row->logo_image_url)
                    <img src="{{ $row->logo_image_url }}" alt="{{ $row->name }}" class="w-12 h-12 shrink-0 rounded-md bg-gray-200 object-cover">
                @endif

                <div class="flex-1">
                    <p class="text-sm font-bold text-gray-800">{{ $row->name }}</p>
                    <p class="mt-1 text-sm text-gray-800">月額 {{ number_format($row->price) }}円</p>
                </div>

                <p class="shrink-0 text-sm text-gray-800">視聴可能 {{ $row->watchable_count }}本</p>
            </div>
        @empty
            <p class="text-gray-800">契約中のサービスがありません</p>
        @endforelse
        
        {{-- 契約中のどれでも見放題でない、見たい作品の本数 --}}
        <p class="mt-4 text-sm text-gray-800">どのサブスクでも見られない作品：{{ $unwatchableCount }}本</p>
    </section>
</x-app-layout>
