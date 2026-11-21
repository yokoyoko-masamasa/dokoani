<?php

namespace App\Console\Commands;

use App\Models\AnimeTitle;
use App\Services\AvailabilitySaver;
use App\Services\TmdbClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ImportNewAnimeTitles extends Command
{
    // コマンド名と引数（--pagesは省略時50ページ、F47）
    protected $signature = 'tmdb:import-new {--pages=50}';

    protected $description = 'TMDBから新規アニメと配信情報を取り込む（F47）';

    public function handle(TmdbClient $tmdb, AvailabilitySaver $saver): int
    {
        $pages = (int) $this->option('pages');
        $imageBaseUrl = config('services.tmdb.image_base_url');
        // 除外する番号を数値にそろえる（文字で書いても一致させる）
        $excludedIds = array_map('intval', config('services.tmdb.excluded_tmdb_ids', []));

        for ($page = 1; $page <= $pages; $page++) {
            // discover/tvを1ページ分取得（失敗したら次のページへ）
            try {
                $response = $tmdb->discoverAnime($page);
            } catch (Throwable $e) {
                Log::warning('discover取得に失敗', [
                    'page' => $page,
                    'message' => $e->getMessage(),
                ]);

                continue;
            }

            $items = $response->json('results', []);

            foreach ($items as $item) {
                // 除外する作品は取り込まない（watch/providers も呼ばない）
                if (in_array((int) $item['id'], $excludedIds, true)) {
                    continue;
                }

                // 登録済みの作品は人気度だけ最新にする（最終更新日は変えない）
                $existing = AnimeTitle::firstWhere('tmdb_id', $item['id']);
                if ($existing) {
                    // 人気度が無い・nullなら上書きしない（列はNULL不可のため）
                    if (isset($item['popularity'])) {
                        $existing->update(['popularity' => $item['popularity']]);
                    }

                    continue;
                }

                // 取得とDB保存をまとめて1単位として扱う（F50）
                try {
                    $this->importOneTitle($item, $tmdb, $imageBaseUrl, $saver);
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
        TmdbClient $tmdb,
        string $imageBaseUrl,
        AvailabilitySaver $saver,
    ): void {
        // 配信情報を取得（ここで失敗したら作品ごとスキップ）
        $response = $tmdb->watchProviders($item['id']);

        // JPが無い・空でも「配信0件」として扱う（失敗ではない）
        $jp = $response->json('results.JP', []);

        // 作品と配信情報の保存を1トランザクションにまとめる
        DB::transaction(function () use ($item, $jp, $imageBaseUrl, $saver) {
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

            // 配信情報の保存は共通の処理で行う（F48と同じ）
            $saver->save($anime, $jp);
        });
    }
}
