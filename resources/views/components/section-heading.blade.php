{{-- 左線つきの h2 見出し。余白などは呼び出し側の class で足す --}}
<h2 {{ $attributes->merge(['class' => 'border-l-4 border-accent pl-3 text-lg font-bold text-gray-800']) }}>{{ $slot }}</h2>
