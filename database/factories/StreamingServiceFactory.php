<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\StreamingService>
 */
class StreamingServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'logo_image_url' => fake()->imageUrl(),
            // 実データ(8,9,84,337)と衝突しないよう10000以上を使う
            'tmdb_provider_id' => fake()->unique()->numberBetween(10000, 99999),
            'service_url' => fake()->url(),
        ];
    }
}
