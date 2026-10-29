<nav class="bg-white border-b border-gray-100">
    <div class="flex items-center justify-between h-16 px-4 md:px-6">
        <a href="{{ route('home') }}" class="text-lg font-bold text-gray-800 whitespace-nowrap">ドコアニ</a>

        <div class="flex items-center gap-4 text-sm whitespace-nowrap">
            {{-- ログイン中かどうかで、右側のリンクを切り替える --}}
            @auth
                {{-- 各リンクのアイコンは、文字の左に置く --}}
                <a href="{{ route('mypage.want') }}" class="inline-flex items-center gap-2 text-gray-700 hover:text-gray-900">
                    <x-heroicon-o-user-circle class="w-5 h-5 shrink-0" aria-hidden="true" />
                    マイページ
                </a>

                {{-- ログアウトは POST で送る。@csrf は不正な送信を防ぐ合言葉 --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 text-gray-700 hover:text-gray-900">
                        <x-heroicon-o-arrow-left-start-on-rectangle class="w-5 h-5 shrink-0" aria-hidden="true" />
                        ログアウト
                    </button>
                </form>
            @else
                {{-- 未ログイン用のリンクにも、同じ形でアイコンを付ける --}}
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-gray-700 hover:text-gray-900">
                    <x-heroicon-o-arrow-right-end-on-rectangle class="w-5 h-5 shrink-0" aria-hidden="true" />
                    ログイン
                </a>
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 text-gray-700 hover:text-gray-900">
                    <x-heroicon-o-user-plus class="w-5 h-5 shrink-0" aria-hidden="true" />
                    会員登録
                </a>
            @endauth
        </div>
    </div>
</nav>
