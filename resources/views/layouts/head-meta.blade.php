@php
    // 画面名があれば「画面名 | アプリ名」、無ければアプリ名だけ作る
    // title と og:title で同じ文字列を使う。エスケープ済みの印を付ける
    $appName = config('app.name', 'Laravel');
    $fullTitle = new \Illuminate\Support\HtmlString(
        isset($title) ? e($title).' | '.e($appName) : e($appName)
    );
@endphp
<title>{{ $fullTitle }}</title>
<meta name="description" content="見たいアニメが、契約中のサブスクで今すぐ見られるかを検索できます。">
<meta property="og:title" content="{{ $fullTitle }}">
<meta property="og:description" content="見たいアニメが、契約中のサブスクで今すぐ見られるかを検索できます。">
<meta property="og:type" content="website">
{{-- タブ用アイコン（png と ico） --}}
<link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
