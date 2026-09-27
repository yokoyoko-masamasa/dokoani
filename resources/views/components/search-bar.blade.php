<form method="GET" action="{{ route('search.index') }}" class="relative flex gap-2">
    <input
        type="text"
        id="search-input"
        name="q"
        value="{{ request('q') }}"
        placeholder="作品名で検索"
        autocomplete="off"
        {{-- JS側から候補取得先と詳細画面URLのひな形を読み取る --}}
        data-suggest-url="{{ route('search.suggest') }}"
        data-show-url="{{ route('anime.show', ['animeTitle' => '__ID__']) }}"
        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
    >
    <button
        type="submit"
        class="px-4 py-2 rounded-md bg-gray-800 text-white hover:bg-gray-700"
    >
        検索
    </button>

    {{-- 検索候補のドロップダウン。JSが表示内容を書き込む --}}
    <ul id="search-suggest-list" class="hidden absolute top-full left-0 right-0 mt-1 bg-white border border-gray-300 rounded-md shadow-sm z-10"></ul>
</form>
