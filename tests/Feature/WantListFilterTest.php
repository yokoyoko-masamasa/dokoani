<?php

namespace Tests\Feature;

use App\Models\AnimeAvailability;
use App\Models\AnimeTitle;
use App\Models\StreamingService;
use App\Models\User;
use App\Models\UserAnimeList;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WantListFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_filters_want_list_by_selected_subscribed_services(): void
    {
        $user = User::factory()->create();
        [$a, $b, $c] = StreamingService::factory()->count(3)->create();

        // A・B だけ契約し、C は契約しない
        UserSubscription::factory()->for($user)->for($a)->create();
        UserSubscription::factory()->for($user)->for($b)->create();

        // 作品1: A で見放題 / 作品2: A でレンタルだけ / 作品3: B で見放題 / 作品4: C で見放題
        $animes = AnimeTitle::factory()->count(4)->create();
        $rows = [[$a, 'flatrate'], [$a, 'rent'], [$b, 'flatrate'], [$c, 'flatrate']];
        foreach ($animes as $i => $anime) {
            UserAnimeList::factory()->for($user)->for($anime)->create(['priority' => $i + 1]);
            AnimeAvailability::factory()->for($anime)->for($rows[$i][0])->create([
                'availability_status' => $rows[$i][1],
            ]);
        }

        // A を選ぶと、A で見放題の作品1だけ（レンタルだけの作品2は出ない）
        $response = $this->actingAs($user)->get(route('mypage.want', ['services' => [$a->id]]));
        $response->assertOk();
        $this->assertSame([$animes[0]->id], $response->viewData('lists')->pluck('anime_title_id')->all());

        // A と B を選ぶと、どれか1つで見放題の作品1・作品3
        $response = $this->actingAs($user)->get(route('mypage.want', ['services' => [$a->id, $b->id]]));
        $this->assertSame([$animes[0]->id, $animes[2]->id], $response->viewData('lists')->pluck('anime_title_id')->all());

        // 契約外の C を渡しても無視され、絞り込みなし（4作品すべて）
        $response = $this->actingAs($user)->get(route('mypage.want', ['services' => [$c->id]]));
        $response->assertOk();
        $this->assertSame([], $response->viewData('selectedIds'));
        $this->assertSame($animes->pluck('id')->all(), $response->viewData('lists')->pluck('anime_title_id')->all());
    }
}
