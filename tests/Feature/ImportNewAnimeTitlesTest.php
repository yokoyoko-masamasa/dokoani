<?php

namespace Tests\Feature;

use App\Models\AnimeAvailability;
use App\Models\AnimeTitle;
use App\Models\StreamingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ImportNewAnimeTitlesTest extends TestCase
{
    use RefreshDatabase;

    public function test_一件の取得失敗があっても他の作品は保存される(): void
    {
        // Netflix役の配信サービスを1件用意
        StreamingService::factory()->create([
            'tmdb_provider_id' => 8,
        ]);

        Http::fake([
            // discover/tv：成功させる作品(1111)と失敗させる作品(9999)を返す
            '*/discover/tv*' => Http::response([
                'results' => [
                    ['id' => 1111, 'name' => '成功する作品', 'overview' => 'あらすじ', 'poster_path' => '/a.jpg', 'popularity' => 10.0],
                    ['id' => 9999, 'name' => '失敗する作品', 'overview' => 'あらすじ', 'poster_path' => '/b.jpg', 'popularity' => 5.0],
                ],
            ], 200),

            // 具体的なパターンを先に書く：9999だけ watch/providers が失敗する
            '*/tv/9999/watch/providers*' => Http::response([], 500),

            // それ以外（1111）は成功する
            '*/tv/*/watch/providers*' => Http::response([
                'results' => [
                    'JP' => [
                        'flatrate' => [
                            ['provider_id' => 8],
                        ],
                    ],
                ],
            ], 200),
        ]);

        Artisan::call('tmdb:import-new', ['--pages' => 1]);

        $this->assertTrue(AnimeTitle::where('tmdb_id', 1111)->exists());
        $this->assertFalse(AnimeTitle::where('tmdb_id', 9999)->exists());
    }

    public function test_discoverの1ページが失敗しても次のページは処理される(): void
    {
        StreamingService::factory()->create([
            'tmdb_provider_id' => 8,
        ]);

        Http::fake([
            // 1ページ目は失敗
            '*discover/tv*page=1*' => Http::response([], 500),

            // 2ページ目は成功し、作品2222を返す
            '*discover/tv*page=2*' => Http::response([
                'results' => [
                    ['id' => 2222, 'name' => '2ページ目の作品', 'overview' => null, 'poster_path' => null, 'popularity' => 1.0],
                ],
            ], 200),

            '*/tv/*/watch/providers*' => Http::response([
                'results' => ['JP' => []],
            ], 200),
        ]);

        Artisan::call('tmdb:import-new', ['--pages' => 2]);

        $this->assertTrue(AnimeTitle::where('tmdb_id', 2222)->exists());
    }

    public function test_provider10のrentがprovider9の行として1行にまとまる(): void
    {
        $amazon = StreamingService::factory()->create([
            'tmdb_provider_id' => 9,
        ]);

        Http::fake([
            '*/discover/tv*' => Http::response([
                'results' => [
                    ['id' => 3333, 'name' => 'レンタル作品', 'overview' => null, 'poster_path' => null, 'popularity' => 1.0],
                ],
            ], 200),

            // provider 9 と provider 10 が両方とも rent を返してくるケースを再現
            '*/tv/3333/watch/providers*' => Http::response([
                'results' => [
                    'JP' => [
                        'rent' => [
                            ['provider_id' => 9],
                            ['provider_id' => 10],
                        ],
                    ],
                ],
            ], 200),
        ]);

        Artisan::call('tmdb:import-new', ['--pages' => 1]);

        $anime = AnimeTitle::where('tmdb_id', 3333)->firstOrFail();

        $this->assertSame(
            1,
            AnimeAvailability::where('anime_title_id', $anime->id)
                ->where('streaming_service_id', $amazon->id)
                ->where('availability_status', 'rent')
                ->count()
        );
    }
}