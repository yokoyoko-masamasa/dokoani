<?php

namespace Tests\Feature;

use App\Models\AnimeAvailability;
use App\Models\AnimeTitle;
use App\Models\StreamingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class UpdateAnimeAvailabilitiesTest extends TestCase
{
    use RefreshDatabase;

    public function test_消えた配信は行が消え新しい配信は行が入る(): void
    {
        $netflix = StreamingService::factory()->create([
            'tmdb_provider_id' => 8,
        ]);
        $amazon = StreamingService::factory()->create([
            'tmdb_provider_id' => 9,
        ]);

        // 取り直す前は Netflix の見放題だけがある
        $anime = AnimeTitle::factory()->create(['tmdb_id' => 4444]);
        AnimeAvailability::factory()->create([
            'anime_title_id' => $anime->id,
            'streaming_service_id' => $netflix->id,
            'availability_status' => 'flatrate',
        ]);

        Http::fake([
            // 取り直した結果は Amazon のレンタルだけ
            '*/tv/4444/watch/providers*' => Http::response([
                'results' => [
                    'JP' => [
                        'rent' => [
                            ['provider_id' => 9],
                        ],
                    ],
                ],
            ], 200),
        ]);

        Artisan::call('tmdb:update-availabilities');

        $this->assertFalse(
            AnimeAvailability::where('anime_title_id', $anime->id)
                ->where('streaming_service_id', $netflix->id)
                ->exists()
        );
        $this->assertTrue(
            AnimeAvailability::where('anime_title_id', $anime->id)
                ->where('streaming_service_id', $amazon->id)
                ->where('availability_status', 'rent')
                ->exists()
        );
        $this->assertSame(1, AnimeAvailability::where('anime_title_id', $anime->id)->count());
    }

    public function test_取り直すとlast_synced_atが更新される(): void
    {
        $netflix = StreamingService::factory()->create([
            'tmdb_provider_id' => 8,
        ]);

        $anime = AnimeTitle::factory()->create([
            'tmdb_id' => 5555,
            'last_synced_at' => '2026-01-01 00:00:00',
        ]);
        AnimeAvailability::factory()->create([
            'anime_title_id' => $anime->id,
            'streaming_service_id' => $netflix->id,
            'availability_status' => 'flatrate',
        ]);

        // 実行中の now() を固定し、更新後の日時と比べられるようにする
        $this->freezeTime();

        Http::fake([
            // JP が null でも失敗ではなく「配信0件」として扱われる
            '*/tv/5555/watch/providers*' => Http::response([
                'results' => ['JP' => null],
            ], 200),
        ]);

        Artisan::call('tmdb:update-availabilities');

        $this->assertSame(now()->toDateTimeString(), $anime->fresh()->last_synced_at);
        $this->assertSame(0, AnimeAvailability::where('anime_title_id', $anime->id)->count());
    }

    public function test_取得に失敗した作品は行もlast_synced_atも変わらず次の作品は更新される(): void
    {
        $netflix = StreamingService::factory()->create([
            'tmdb_provider_id' => 8,
        ]);

        // 2件とも取り直す前は Netflix の見放題だけがある
        $failed = AnimeTitle::factory()->create([
            'tmdb_id' => 6666,
            'last_synced_at' => '2026-01-01 00:00:00',
        ]);
        $succeeded = AnimeTitle::factory()->create([
            'tmdb_id' => 7777,
            'last_synced_at' => '2026-01-01 00:00:00',
        ]);
        foreach ([$failed, $succeeded] as $anime) {
            AnimeAvailability::factory()->create([
                'anime_title_id' => $anime->id,
                'streaming_service_id' => $netflix->id,
                'availability_status' => 'flatrate',
            ]);
        }

        // 実行中の now() を固定し、更新後の日時と比べられるようにする
        $this->freezeTime();

        // ログの呼び出しを記録し、あとで確かめられるようにする
        Log::spy();

        Http::fake([
            // 6666 は TMDB のエラー（500）で取得に失敗する
            '*/tv/6666/watch/providers*' => Http::response([], 500),
            // 7777 は成功し、Netflix のレンタルだけになる
            '*/tv/7777/watch/providers*' => Http::response([
                'results' => [
                    'JP' => [
                        'rent' => [
                            ['provider_id' => 8],
                        ],
                    ],
                ],
            ], 200),
        ]);

        Artisan::call('tmdb:update-availabilities');

        // 失敗した作品は、行も last_synced_at も変わらない
        $this->assertSame('2026-01-01 00:00:00', $failed->fresh()->last_synced_at);
        $this->assertTrue(
            AnimeAvailability::where('anime_title_id', $failed->id)
                ->where('streaming_service_id', $netflix->id)
                ->where('availability_status', 'flatrate')
                ->exists()
        );
        $this->assertSame(1, AnimeAvailability::where('anime_title_id', $failed->id)->count());

        // 成功した作品は、行が入れ直され last_synced_at が更新される
        $this->assertSame(now()->toDateTimeString(), $succeeded->fresh()->last_synced_at);
        $this->assertTrue(
            AnimeAvailability::where('anime_title_id', $succeeded->id)
                ->where('availability_status', 'rent')
                ->exists()
        );
        $this->assertSame(1, AnimeAvailability::where('anime_title_id', $succeeded->id)->count());

        // 失敗した作品の tmdb_id で、警告が1回だけ出る
        Log::shouldHaveReceived('warning')
            ->once()
            ->with('配信情報の更新に失敗', Mockery::on(
                fn (array $context) => $context['tmdb_id'] === 6666
            ));
    }
}
