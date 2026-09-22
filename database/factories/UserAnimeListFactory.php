<?php

namespace Database\Factories;

use App\Enums\ListStatus;
use App\Models\AnimeTitle;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\UserAnimeList>
 */
class UserAnimeListFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'anime_title_id' => AnimeTitle::factory(),
            // ListStatusにキャストされるため文字列'want'ではなくenumを使う
            'status' => ListStatus::Want,
            'priority' => fake()->numberBetween(1, 100),
        ];
    }

    /**
     * Indicate that the anime is in the watched list.
     */
    public function watched(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ListStatus::Watched,
            // 視聴済みは優先度を持たないため常にNULL（SPEC 7-1）
            'priority' => null,
        ]);
    }
}
