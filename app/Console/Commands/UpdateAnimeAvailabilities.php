<?php

namespace App\Console\Commands;

use App\Models\AnimeTitle;
use App\Services\AvailabilitySaver;
use App\Services\TmdbClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class UpdateAnimeAvailabilities extends Command
{
    protected $signature = 'tmdb:update-availabilities';

    protected $description = '登録済みアニメの配信情報を取り直す（F48）';

    public function handle(TmdbClient $tmdb, AvailabilitySaver $saver): int
    {
        // 古い順の一覧を最初に1回だけ取る（処理中に順番が崩れない）
        $animes = AnimeTitle::orderBy('last_synced_at')
            ->orderBy('id')
            ->get(['id', 'tmdb_id']);

        foreach ($animes as $anime) {
            // 取得と更新を1単位とし、失敗したら何も変えずに次へ
            try {
                $this->updateOneTitle($anime, $tmdb, $saver);
            } catch (Throwable $e) {
                Log::warning('配信情報の更新に失敗', [
                    'tmdb_id' => $anime->tmdb_id,
                    'message' => $e->getMessage(),
                ]);

                continue;
            }
        }

        return self::SUCCESS;
    }

    /**
     * 作品1件の配信情報を取り直し、消して入れ直す
     */
    private function updateOneTitle(
        AnimeTitle $anime,
        TmdbClient $tmdb,
        AvailabilitySaver $saver,
    ): void {
        // 取得はトランザクションの外で先に行う（失敗なら何も変えない）
        $response = $tmdb->watchProviders($anime->tmdb_id);

        // JPが無い・nullなら保存側で「配信0件」として扱う
        $jp = $response->json('results.JP');

        // 消す・入れる・最終更新日の更新を1トランザクションにまとめる
        DB::transaction(function () use ($anime, $jp, $saver) {
            $anime->availabilities()->delete();

            $saver->save($anime, $jp);

            $anime->update(['last_synced_at' => now()]);
        });
    }
}
