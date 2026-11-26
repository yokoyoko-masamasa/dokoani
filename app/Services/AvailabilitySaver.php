<?php

namespace App\Services;

use App\Models\AnimeAvailability;
use App\Models\AnimeTitle;
use App\Models\StreamingService;
use Illuminate\Support\Facades\Log;

class AvailabilitySaver
{
    // DBに保存する配信区分（この5種以外はログのみ）
    private const KNOWN_STATUSES = ['flatrate', 'free', 'ads', 'rent', 'buy'];

    /**
     * 配信区分ごとにanime_availabilitiesを保存する（F47・F48で共通）
     */
    public function save(AnimeTitle $anime, ?array $jp): void
    {
        // JPがnullでも「配信0件」として扱う（失敗ではない）
        $jp ??= [];

        $aliases = config('services.tmdb.provider_aliases');

        foreach (self::KNOWN_STATUSES as $status) {
            foreach ($jp[$status] ?? [] as $provider) {
                // provider10はAmazon Prime Video(9)に寄せる
                $providerId = $aliases[$provider['provider_id']] ?? $provider['provider_id'];

                $service = StreamingService::firstWhere('tmdb_provider_id', $providerId);

                // 対象6社に無いproviderは無視する
                if (! $service) {
                    continue;
                }

                // 既にあれば新規作成しない（一意制約違反を防ぐ）
                AnimeAvailability::firstOrCreate([
                    'anime_title_id' => $anime->id,
                    'streaming_service_id' => $service->id,
                    'availability_status' => $status,
                ]);
            }
        }

        // 想定外の配信区分が来ていないか記録する（保存はしない）
        $unknownKeys = array_diff(
            array_keys($jp),
            ['link', ...self::KNOWN_STATUSES],
        );

        foreach ($unknownKeys as $unknownKey) {
            Log::info('未知の配信区分を検出', [
                'key' => $unknownKey,
                'tmdb_id' => $anime->tmdb_id,
            ]);
        }
    }
}
