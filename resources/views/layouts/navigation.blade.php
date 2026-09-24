<nav class="bg-white border-b border-gray-100">
    <div class="flex items-center justify-between h-16 px-4 md:px-6">
        <a href="{{ route('home') }}" class="text-lg font-bold text-gray-800 whitespace-nowrap">ドコアニ</a>

        <div class="flex items-center gap-4 text-sm whitespace-nowrap">
            {{-- ログイン中かどうかで、右側のリンクを切り替える --}}
            @auth
                {{-- 仮の URL。ルートができたら route() に置き換える --}}
                <a href="{{ url('/mypage/want') }}" class="text-gray-700 hover:text-gray-900">マイページ</a>

                {{-- ログアウトは POST で送る。@csrf は不正な送信を防ぐ合言葉 --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-700 hover:text-gray-900">ログアウト</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900">ログイン</a>
                <a href="{{ route('register') }}" class="text-gray-700 hover:text-gray-900">会員登録</a>
            @endauth
        </div>
    </div>
</nav>
