@props(['title', 'message', 'home' => true])

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $title }} | {{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <div class="max-w-7xl mx-auto min-h-screen flex flex-col">
                <header class="bg-white border-b border-gray-100">
                    <div class="flex items-center h-16 px-4 md:px-6">
                        <a href="/" class="text-lg font-bold text-gray-800 whitespace-nowrap">ドコアニ</a>
                    </div>
                </header>

                <main class="flex-1">
                    <section class="px-4 md:px-6 py-8">
                        <h1 class="text-lg font-bold text-gray-800">{{ $title }}</h1>

                        <p class="mt-2 text-sm text-gray-800">{{ $message }}</p>

                        @if ($home)
                            <a href="/" class="mt-6 block w-full px-3 py-2 rounded-md text-center text-sm bg-white text-gray-800 border border-gray-300">トップへ戻る</a>
                        @endif
                    </section>
                </main>

                @include('layouts.footer')
            </div>
        </div>
    </body>
</html>
