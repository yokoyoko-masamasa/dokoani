@props(['anime', 'linked' => false])

{{-- 同じサービスのロゴは1つにまとめる（flatrate と rent の重複対策） --}}
@php($logos = $anime->availabilities->unique('streaming_service_id'))
<div class="mt-2 flex flex-wrap gap-1">
    {{-- 配信が無ければ1行だけ出し、あればロゴを並べる --}}
    @forelse ($logos as $availability)
        {{-- 検索結果だけ、ロゴをサービスのトップへのリンクにする --}}
        @if ($linked)
            {{-- 別タブで開く。rel は開いた先から元の画面を操作させない対策 --}}
            <a href="{{ $availability->streamingService->service_url }}" target="_blank" rel="noopener noreferrer">
                <img src="{{ $availability->streamingService->logo_image_url }}" alt="{{ $availability->streamingService->name }}" class="w-8 h-8 rounded">
            </a>
        @else
            <img src="{{ $availability->streamingService->logo_image_url }}" alt="{{ $availability->streamingService->name }}" class="w-8 h-8 rounded">
        @endif
    @empty
        <span class="text-xs text-gray-500">配信サービスなし</span>
    @endforelse
</div>
