<?php

namespace Tests\Feature;

use App\Models\AnimeAvailability;
use App\Models\AnimeTitle;
use App\Models\StreamingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
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
}
