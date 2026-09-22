<?php

namespace Database\Factories;

use App\Models\AnimeTitle;
use App\Models\StreamingService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\AnimeAvailability>
 */
class AnimeAvailabilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 外部キーがNOT NULLのため親レコードも自動生成する
            'anime_title_id' => AnimeTitle::factory(),
            'streaming_service_id' => StreamingService::factory(),
            'availability_status' => fake()->randomElement(['flatrate', 'free', 'ads', 'rent', 'buy']),
        ];
    }
}
