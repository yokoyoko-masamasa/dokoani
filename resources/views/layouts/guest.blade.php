<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- <head> の共通部分（タイトル）を読み込む --}}
        @include('layouts.head-meta')

        <!-- Fonts -->

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col items-center bg-gray-100">
            {{-- ロゴとカードを余った高さに入れ、フッターを最下部へ押し出す --}}
            <div class="w-full flex-1 flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
                <div>
                    {{-- 文字ロゴの部品を読み込む（トップへのリンク） --}}
                    <x-application-logo class="text-2xl" />
                </div>

                <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                    {{ $slot }}
                </div>
            </div>

            {{-- フッターは flex-1 の枠の外に置く（画面の最下部） --}}
            @include('layouts.footer')
        </div>
    </body>
</html>
