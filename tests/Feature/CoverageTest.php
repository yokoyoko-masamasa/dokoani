<?php

namespace Tests\Feature;

use App\Models\AnimeAvailability;
use App\Models\AnimeTitle;
use App\Models\StreamingService;
use App\Models\User;
use App\Models\UserAnimeList;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CoverageTest extends TestCase
{
    use RefreshDatabase;

    public function test_counts_only_flatrate_want_titles_per_service(): void
    {
        $user = User::factory()->create();
        $services = StreamingService::factory()->count(3)->create();

        foreach ($services as $service) {
            UserSubscription::factory()->for($user)->for($service)->create();
        }

        // 3作品を見たいに入れ、うち2作品だけ1社目で見放題にする
        $animes = AnimeTitle::factory()->count(3)->create();
        foreach ($animes as $anime) {
            UserAnimeList::factory()->for($user)->for($anime)->create();
        }
        foreach ([$animes[0], $animes[1]] as $anime) {
            AnimeAvailability::factory()->for($anime)->for($services[0])->create([
                'availability_status' => 'flatrate',
            ]);
        }

        // 準備のSQLを除くため、画面を開く直前からログを取る
        DB::enableQueryLog();
        $response = $this->actingAs($user)->get(route('mypage.subscriptions'));
        $queries = collect(DB::getQueryLog());

        $response->assertOk();
        $rows = collect($response->viewData('services'))->keyBy('id');
        $this->assertCount(3, $rows);
        $this->assertSame(2, $rows[$services[0]->id]->watchable_count);

        // 契約3件でも、SQLは見られる数と見られない数の計2本
        $reads = $queries->filter(fn ($q) => str_contains($q['query'], 'anime_availabilities'));
        $this->assertCount(2, $reads);
    }

    public function test_service_with_zero_watchable_titles_is_still_listed(): void
    {
        $user = User::factory()->create();
        $withTitles = StreamingService::factory()->create();
        $withoutTitles = StreamingService::factory()->create();

        UserSubscription::factory()->for($user)->for($withTitles)->create();
        UserSubscription::factory()->for($user)->for($withoutTitles)->create();

        $anime = AnimeTitle::factory()->create();
        UserAnimeList::factory()->for($user)->for($anime)->create();
        AnimeAvailability::factory()->for($anime)->for($withTitles)->create([
            'availability_status' => 'flatrate',
        ]);

        $response = $this->actingAs($user)->get(route('mypage.subscriptions'));

        // 配信0本のサービスも行として残り、本数0で返る
        $rows = collect($response->viewData('services'))->keyBy('id');
        $this->assertCount(2, $rows);
        $this->assertSame(1, $rows[$withTitles->id]->watchable_count);
        $this->assertSame(0, $rows[$withoutTitles->id]->watchable_count);
    }

    public function test_rent_only_title_is_not_counted(): void
    {
        $user = User::factory()->create();
        $service = StreamingService::factory()->create();
        UserSubscription::factory()->for($user)->for($service)->create();

        $anime = AnimeTitle::factory()->create();
        UserAnimeList::factory()->for($user)->for($anime)->create();
        AnimeAvailability::factory()->for($anime)->for($service)->create([
            'availability_status' => 'rent',
        ]);

        $response = $this->actingAs($user)->get(route('mypage.subscriptions'));

        // rent だけの作品は見放題ではないので数えない
        $rows = collect($response->viewData('services'))->keyBy('id');
        $this->assertSame(0, $rows[$service->id]->watchable_count);
    }

    public function test_all_want_titles_are_counted_when_no_subscriptions(): void
    {
        $user = User::factory()->create();

        // 契約は作らない。配信だけある未契約のサービスを用意する
        $service = StreamingService::factory()->create();

        $animes = AnimeTitle::factory()->count(2)->create();
        foreach ($animes as $anime) {
            UserAnimeList::factory()->for($user)->for($anime)->create([
                'status' => 'want',
            ]);
            AnimeAvailability::factory()->for($anime)->for($service)->create([
                'availability_status' => 'flatrate',
            ]);
        }

        $response = $this->actingAs($user)->get(route('mypage.subscriptions'));

        // 契約0件なら、見たいの全件が見られない作品になる
        $this->assertSame(2, $response->viewData('unwatchableCount'));
    }
}
