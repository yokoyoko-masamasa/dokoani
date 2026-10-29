<nav class="px-4 md:px-6 flex border-b border-gray-200 text-sm">
    {{-- 表示中のタブだけ、濃い文字色と下線で強調する --}}
    {{-- 各タブのアイコンは文字の上に縦に並べる --}}
    <a href="{{ route('mypage.want') }}" class="flex-1 flex flex-col items-center gap-1 py-3 text-center {{ request()->routeIs('mypage.want') ? 'font-bold text-gray-800 border-b-2 border-gray-800' : 'text-gray-500' }}">
        <x-heroicon-o-bookmark class="w-6 h-6 shrink-0" aria-hidden="true" />
        見たいリスト
    </a>
    <a href="{{ route('mypage.watched') }}" class="flex-1 flex flex-col items-center gap-1 py-3 text-center {{ request()->routeIs('mypage.watched') ? 'font-bold text-gray-800 border-b-2 border-gray-800' : 'text-gray-500' }}">
        <x-heroicon-o-check-circle class="w-6 h-6 shrink-0" aria-hidden="true" />
        視聴済み
    </a>
    <a href="{{ route('mypage.subscriptions') }}" class="flex-1 flex flex-col items-center gap-1 py-3 text-center {{ request()->routeIs('mypage.subscriptions') ? 'font-bold text-gray-800 border-b-2 border-gray-800' : 'text-gray-500' }}">
        <x-heroicon-o-credit-card class="w-6 h-6 shrink-0" aria-hidden="true" />
        契約状況
    </a>
    <a href="{{ route('mypage.settings') }}" class="flex-1 flex flex-col items-center gap-1 py-3 text-center {{ request()->routeIs('mypage.settings') ? 'font-bold text-gray-800 border-b-2 border-gray-800' : 'text-gray-500' }}">
        <x-heroicon-o-cog-6-tooth class="w-6 h-6 shrink-0" aria-hidden="true" />
        設定
    </a>
</nav>
