@props(['anime'])

{{-- 同じサービスのロゴは1つにまとめる（flatrate と rent の重複対策） --}}
@php($logos = $anime->availabilities->unique('streaming_service_id'))
<div class="mt-2 flex flex-wrap gap-1">
    {{-- 配信が無ければ1行だけ出し、あればロゴを並べる --}}
    @forelse ($logos as $availability)
        <img src="{{ $availability->streamingService->logo_image_url }}" alt="{{ $availability->streamingService->name }}" class="w-8 h-8 rounded">
    @empty
        <span class="text-xs text-gray-500">配信サービスなし</span>
    @endforelse
</div>
