<?php

namespace Tests\Feature;

use App\Enums\ListStatus;
use App\Models\AnimeTitle;
use App\Models\User;
use App\Models\UserAnimeList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAnimeListTest extends TestCase
{
    use RefreshDatabase;

    public function test_adding_to_want_list_sets_priority_to_max_plus_one(): void
    {
        $user = User::factory()->create();
        $animeTitle = AnimeTitle::factory()->create();

        UserAnimeList::factory()->for($user)->create(['priority' => 1]);
        UserAnimeList::factory()->for($user)->create(['priority' => 2]);

        // 他ユーザーのwantはpriority計算に含めない
        $otherUser = User::factory()->create();
        UserAnimeList::factory()->for($otherUser)->create(['priority' => 10]);

        // PUTで見たいに追加する
        $response = $this->actingAs($user)
            ->put(route('list.update', $animeTitle), ['status' => 'want']);

        $response->assertRedirect();
        $this->assertDatabaseHas('user_anime_lists', [
            'user_id' => $user->id,
            'anime_title_id' => $animeTitle->id,
            'status' => ListStatus::Want->value,
            'priority' => 3,
        ]);
    }

    public function test_marking_as_watched_sets_priority_to_null(): void
    {
        $user = User::factory()->create();
        $animeTitle = AnimeTitle::factory()->create();

        UserAnimeList::factory()->for($user)->for($animeTitle)->create([
            'status' => ListStatus::Want,
            'priority' => 1,
        ]);

        // PUTで視聴済みに変更する
        $response = $this->actingAs($user)
            ->put(route('list.update', $animeTitle), ['status' => 'watched']);

        $response->assertRedirect();
        $this->assertDatabaseHas('user_anime_lists', [
            'user_id' => $user->id,
            'anime_title_id' => $animeTitle->id,
            'status' => ListStatus::Watched->value,
            'priority' => null,
        ]);
    }

    public function test_reverting_watched_to_want_sets_priority_to_max_plus_one(): void
    {
        $user = User::factory()->create();
        $animeTitle = AnimeTitle::factory()->create();

        UserAnimeList::factory()->for($user)->create(['priority' => 1]);
        UserAnimeList::factory()->for($user)->create(['priority' => 5]);
        UserAnimeList::factory()->for($user)->for($animeTitle)->watched()->create();

        // PUTで視聴済みから見たいへ差し戻す
        $response = $this->actingAs($user)
            ->put(route('list.update', $animeTitle), ['status' => 'want']);

        $response->assertRedirect();
        $this->assertDatabaseHas('user_anime_lists', [
            'user_id' => $user->id,
            'anime_title_id' => $animeTitle->id,
            'status' => ListStatus::Want->value,
            'priority' => 6,
        ]);
    }

    public function test_adding_the_same_title_twice_keeps_one_row_and_priority_unchanged(): void
    {
        $user = User::factory()->create();
        $animeTitle = AnimeTitle::factory()->create();

        // 1回目のPUTで見たいに追加する
        $this->actingAs($user)->put(route('list.update', $animeTitle), ['status' => 'want']);

        $first = UserAnimeList::where('user_id', $user->id)
            ->where('anime_title_id', $animeTitle->id)
            ->firstOrFail();

        // 同じstatusで2回目のPUTを送る（何もしないはず）
        $response = $this->actingAs($user)
            ->put(route('list.update', $animeTitle), ['status' => 'want']);

        $response->assertRedirect();
        $this->assertSame(1, UserAnimeList::where('user_id', $user->id)
            ->where('anime_title_id', $animeTitle->id)
            ->count());
        $this->assertSame($first->priority, $first->fresh()->priority);
    }
}
