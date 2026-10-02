<x-app-layout>
    <div class="px-4 md:px-6 py-8">
        {{-- 検索バー共通コンポーネントを読み込む --}}
        <x-search-bar />
    </div>

    <section class="px-4 md:px-6 pb-8">
        <h1 class="text-lg font-bold text-gray-800">{{ $animeTitle->title }}</h1>

        {{-- メール認証済みのログインユーザーだけにボタンを出す --}}
        @auth
            @if (auth()->user()->hasVerifiedEmail())
                @php
                    // 追加済みなら黒、未追加なら白（枠線は同じ太さでそろえる）
                    $wantClass = $listStatus === \App\Enums\ListStatus::Want
                        ? 'bg-black text-white border border-black'
                        : 'bg-white text-gray-800 border border-gray-300';
                    $watchedClass = $listStatus === \App\Enums\ListStatus::Watched
                        ? 'bg-black text-white border border-black'
                        : 'bg-white text-gray-800 border border-gray-300';
                @endphp

                <div class="mt-4 flex gap-2">
                    {{-- 見たい: 追加済みなら削除(DELETE)、それ以外は want で更新(PUT) --}}
                    @if ($listStatus === \App\Enums\ListStatus::Want)
                        <form method="POST" action="{{ route('list.destroy', $animeTitle) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 rounded-md text-sm {{ $wantClass }}">見たい</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('list.update', $animeTitle) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="want">
                            <button type="submit" class="px-4 py-2 rounded-md text-sm {{ $wantClass }}">見たい</button>
                        </form>
                    @endif

                    {{-- 視聴済み: 追加済みなら削除(DELETE)、それ以外は watched で更新(PUT) --}}
                    @if ($listStatus === \App\Enums\ListStatus::Watched)
                        <form method="POST" action="{{ route('list.destroy', $animeTitle) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 rounded-md text-sm {{ $watchedClass }}">視聴済み</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('list.update', $animeTitle) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="watched">
                            <button type="submit" class="px-4 py-2 rounded-md text-sm {{ $watchedClass }}">視聴済み</button>
                        </form>
                    @endif
                </div>
            @endif
        @endauth

        <div class="mt-4">
            {{-- ポスター画像が無い作品は「No Image」と表示する --}}
            @if ($animeTitle->poster_image_url)
                <img src="{{ $animeTitle->poster_image_url }}" alt="{{ $animeTitle->title }}" class="w-48 aspect-[2/3] object-cover rounded-md bg-gray-200">
            @else
                <div class="w-48 aspect-[2/3] rounded-md bg-gray-200 flex items-center justify-center text-gray-500">
                    No Image
                </div>
            @endif
        </div>
    </section>

    <section class="px-4 md:px-6 pb-8">
        <h2 class="text-base font-bold text-gray-800">配信サービス</h2>

        {{-- 配信サービスが1件も無い場合はメッセージだけ表示する --}}
        @if ($logos->isEmpty())
            <p class="mt-2 text-sm text-gray-500">配信サービスなし</p>
        @else
            <div class="mt-2 flex flex-wrap gap-4">
                @foreach ($logos as $logo)
                    {{-- 別タブで開く。target="_blank"には安全対策としてrel属性を必ず付ける --}}
                    <a href="{{ $logo['service']->service_url }}" target="_blank" rel="noopener noreferrer">
                        <img
                            src="{{ $logo['service']->logo_image_url }}"
                            alt="{{ $logo['service']->name }}"
                            class="w-16 h-16 object-contain rounded border-4 {{ $logo['border_class'] }}"
                        >
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    <section class="px-4 md:px-6 pb-8">
        <h2 class="text-base font-bold text-gray-800">あらすじ</h2>

        {{-- あらすじが無い作品は文言で代用する --}}
        @if ($animeTitle->synopsis)
            <p class="mt-2 text-sm text-gray-800">{{ $animeTitle->synopsis }}</p>
        @else
            <p class="mt-2 text-sm text-gray-500">あらすじ情報がありません</p>
        @endif
    </section>
</x-app-layout>
