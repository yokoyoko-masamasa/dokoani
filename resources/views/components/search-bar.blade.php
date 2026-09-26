<form method="GET" action="{{ route('search.index') }}" class="flex gap-2">
    <input
        type="text"
        name="q"
        value="{{ request('q') }}"
        placeholder="作品名で検索"
        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
    >
    <button
        type="submit"
        class="px-4 py-2 rounded-md bg-gray-800 text-white hover:bg-gray-700"
    >
        検索
    </button>
</form>
