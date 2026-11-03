{{-- 画面名があれば「画面名 | アプリ名」、無ければアプリ名だけ出す --}}
@isset($title)
    <title>{{ $title }} | {{ config('app.name', 'Laravel') }}</title>
@else
    <title>{{ config('app.name', 'Laravel') }}</title>
@endisset
