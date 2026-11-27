<x-app-layout>
    <x-slot:title>見たいリスト</x-slot:title>

    <div class="px-4 md:px-6 py-8">
        {{-- 検索バー共通コンポーネントを読み込む --}}
        <x-search-bar />
    </div>

    {{-- マイページのタブ共通コンポーネントを読み込む --}}
    <x-mypage-tabs />

    <section class="px-4 md:px-6 py-8">
        <h1 class="mb-4 text-2xl font-bold text-gray-800">見たいリスト</h1>

        <p class="mb-2 text-sm text-gray-600">契約中サービスで絞り込み</p>
        {{-- 契約が0件なら設定への案内を出し、あれば絞り込みボタンを並べる --}}
        @if ($services->isEmpty())
            <div class="mb-4">
                <p class="text-sm text-gray-800">契約中のサービスが登録されていません。</p>
                {{-- 設定タブ（サブスクの追加の入口）へのリンク --}}
                <a href="{{ route('mypage.settings') }}" class="inline-flex items-center min-h-6 text-sm text-gray-800 underline">設定でサブスクを追加する</a>
            </div>
        @else
            @php
                $chipBase = 'inline-flex items-center min-h-8 px-3 rounded-full text-sm hover:brightness-90 focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2';
                $chipOn = 'bg-primary-dark text-white border border-primary-dark';
                $chipOff = 'bg-white text-gray-800 border border-gray-300';
            @endphp
            <div class="mb-4 flex flex-wrap gap-2">
                {{-- 「すべて」は選択を全部外す。何も選んでいなければ選択中の色 --}}
                <a href="{{ route('mypage.want') }}" class="{{ $chipBase }} {{ $selectedIds ? $chipOff : $chipOn }}">すべて</a>
                @foreach ($services as $service)
                    @php
                        $isOn = in_array($service->id, $selectedIds, true);
                        // 押したサービスだけ、選択と解除を入れ替えた ID の一覧を作る
                        $next = $isOn
                            ? array_values(array_diff($selectedIds, [$service->id]))
                            : [...$selectedIds, $service->id];
                    @endphp
                    {{-- 切り替え後の services[] を付けた見たいリストの URL へ --}}
                    <a href="{{ route('mypage.want', ['services' => $next]) }}" class="{{ $chipBase }} {{ $isOn ? $chipOn : $chipOff }}">{{ $service->name }}</a>
                @endforeach
            </div>
        @endif

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

                    {{-- 配信ロゴ共通コンポーネントを読み込む --}}
                    <x-availability-logos :anime="$anime" />

                    <div class="mt-3 flex gap-2">
                        {{-- 視聴済みへ変更するPUT。@csrf は不正な送信を防ぐ合言葉 --}}
                        <form method="POST" action="{{ route('list.update', $anime) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="watched">
                            <button type="submit" class="px-3 py-1 rounded-md text-sm bg-white text-gray-800 border border-gray-300">視聴済みにする</button>
                        </form>

                        {{-- リストから外すDELETE --}}
                        <form method="POST" action="{{ route('list.destroy', $anime) }}" onsubmit="return confirm('この作品を見たいリストから削除しますか？')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1 rounded-md text-sm bg-white text-red-600 border border-red-300">削除</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            {{-- 絞り込み中なら条件に合う作品が無い旨、そうでなければ0件の文言 --}}
            @if ($selectedIds)
                <p class="text-gray-800">選んだサービスで見放題の作品がありません</p>
            @else
                <p class="text-gray-800">見たいリストに作品がありません</p>
            @endif
        @endforelse
    </section>
</x-app-layout>
