<?php

namespace App\Console\Commands;

use App\Models\AnimeAvailability;
use App\Models\AnimeTitle;
use App\Models\StreamingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class ImportNewAnimeTitles extends Command
{
    // コマンド名と引数（--pagesは省略時50ページ、F47）
    protected $signature = 'tmdb:import-new {--pages=50}';

    protected $description = 'TMDBから新規アニメと配信情報を取り込む（F47）';

    // DBに保存する配信区分（この5種以外はログのみ）
    private const KNOWN_STATUSES = ['flatrate', 'free', 'ads', 'rent', 'buy'];

    public function handle(): int
    {
        $pages = (int) $this->option('pages');
        $baseUrl = config('services.tmdb.base_url');
        $imageBaseUrl = config('services.tmdb.image_base_url');
        $token = config('services.tmdb.api_token');
        $aliases = config('services.tmdb.provider_aliases');

        for ($page = 1; $page <= $pages; $page++) {
            // discover/tvを1ページ分取得（失敗したら次のページへ）
            try {
                $response = Http::withToken($token)
                    ->timeout(10)
                    ->get("{$baseUrl}/discover/tv", [
                        'with_genres' => 16,
                        'with_origin_country' => 'JP',
                        'sort_by' => 'popularity.desc',
                        'language' => 'ja-JP',
                        'page' => $page,
                    ])
                    ->throw();

                usleep(30_000); // レート制限順守のための待機（F52）
            } catch (Throwable $e) {
                Log::warning('discover取得に失敗', [
                    'page' => $page,
                    'message' => $e->getMessage(),
                ]);

                continue;
            }

            $items = $response->json('results', []);

            foreach ($items as $item) {
                // すでに登録済みの作品はスキップ（重複取り込み防止）
                if (AnimeTitle::where('tmdb_id', $item['id'])->exists()) {
                    continue;
                }

                // 取得とDB保存をまとめて1単位として扱う（F50）
                try {
                    $this->importOneTitle($item, $baseUrl, $imageBaseUrl, $token, $aliases);
                } catch (Throwable $e) {
                    Log::warning('作品の取り込みに失敗', [
                        'tmdb_id' => $item['id'],
                        'message' => $e->getMessage(),
                    ]);

                    continue;
                }
            }
        }

        return self::SUCCESS;
    }

    /**
     * 新規作品1件の配信情報を取得し、DBに保存する
     */
    private function importOneTitle(
        array $item,
        string $baseUrl,
        string $imageBaseUrl,
        string $token,
        array $aliases,
    ): void {
        // 配信情報を取得（ここで失敗したら作品ごとスキップ）
        $response = Http::withToken($token)
            ->timeout(10)
            ->get("{$baseUrl}/tv/{$item['id']}/watch/providers")
            ->throw();

        usleep(30_000); // レート制限順守のための待機（F52）

        // JPが無い・空でも「配信0件」として扱う（失敗ではない）
        $jp = $response->json('results.JP', []);

        // 作品と配信情報の保存を1トランザクションにまとめる
        DB::transaction(function () use ($item, $jp, $imageBaseUrl, $aliases) {
            $posterUrl = $item['poster_path']
                ? $imageBaseUrl.'/w500'.$item['poster_path']
                : null;

            $anime = AnimeTitle::create([
                'tmdb_id' => $item['id'],
                'title' => $item['name'],
                'synopsis' => $item['overview'] ?: null,
                'poster_image_url' => $posterUrl,
                'popularity' => $item['popularity'],
                'last_synced_at' => now(), // TMDB由来ではなく取得時刻を記録
            ]);

            $this->saveAvailabilities($anime, $jp, $aliases);
        });
    }

    /**
     * 配信区分ごとにanime_availabilitiesを保存する
     */
    private function saveAvailabilities(AnimeTitle $anime, array $jp, array $aliases): void
    {
        foreach (self::KNOWN_STATUSES as $status) {
            foreach ($jp[$status] ?? [] as $provider) {
                // provider10はAmazon Prime Video(9)に寄せる
                $providerId = $aliases[$provider['provider_id']] ?? $provider['provider_id'];

                $service = StreamingService::firstWhere('tmdb_provider_id', $providerId);

                // 対象4社に無いproviderは無視する
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