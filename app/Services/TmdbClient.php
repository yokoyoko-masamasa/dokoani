<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class TmdbClient
{
    // 1回の取得のあとに空ける時間（30ms。約33回/秒。F52）
    private const INTERVAL_MICROSECONDS = 30_000;

    private const TIMEOUT_SECONDS = 10;

    /**
     * 日本のアニメを人気順で1ページ分取得する
     */
    public function discoverAnime(int $page): Response
    {
        return $this->get('/discover/tv', [
            'with_genres' => 16,
            'with_origin_country' => 'JP',
            'sort_by' => 'popularity.desc',
            'language' => 'ja-JP',
            'page' => $page,
        ]);
    }

    /**
     * 作品1件の配信情報を取得する
     */
    public function watchProviders(int $tmdbId): Response
    {
        return $this->get("/tv/{$tmdbId}/watch/providers");
    }

    /**
     * TMDBへの取得をすべてここで行う（F52）
     */
    private function get(string $path, array $query = []): Response
    {
        try {
            // 4xx・5xxは例外にして、スキップは呼び出し側に任せる
            return Http::withToken(config('services.tmdb.api_token'))
                ->timeout(self::TIMEOUT_SECONDS)
                ->get(config('services.tmdb.base_url').$path, $query)
                ->throw();
        } finally {
            // 成功でも失敗でも待つ（失敗が続いても上限を超えないため）
            usleep(self::INTERVAL_MICROSECONDS);
        }
    }
}
