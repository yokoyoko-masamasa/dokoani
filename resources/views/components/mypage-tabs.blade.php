<nav class="px-4 md:px-6 flex border-b border-gray-200 text-sm">
    {{-- 表示中のタブだけ、濃い文字色と下線で強調する --}}
    <a href="{{ route('mypage.want') }}" class="flex-1 py-3 text-center {{ request()->routeIs('mypage.want') ? 'font-bold text-gray-800 border-b-2 border-gray-800' : 'text-gray-500' }}">見たいリスト</a>
    <a href="{{ route('mypage.watched') }}" class="flex-1 py-3 text-center {{ request()->routeIs('mypage.watched') ? 'font-bold text-gray-800 border-b-2 border-gray-800' : 'text-gray-500' }}">視聴済み</a>
    <a href="{{ route('mypage.subscriptions') }}" class="flex-1 py-3 text-center {{ request()->routeIs('mypage.subscriptions') ? 'font-bold text-gray-800 border-b-2 border-gray-800' : 'text-gray-500' }}">契約状況</a>
    <a href="{{ route('mypage.settings') }}" class="flex-1 py-3 text-center {{ request()->routeIs('mypage.settings') ? 'font-bold text-gray-800 border-b-2 border-gray-800' : 'text-gray-500' }}">設定</a>
</nav>
