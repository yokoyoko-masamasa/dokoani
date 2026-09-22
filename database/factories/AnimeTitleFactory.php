<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\AnimeTitle>
 */
class AnimeTitleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tmdb_id' => fake()->unique()->numberBetween(1, 999999),
            'title' => fake()->sentence(3),
            'poster_image_url' => fake()->imageUrl(),
            'synopsis' => fake()->paragraph(),
            'last_synced_at' => now(),
            'popularity' => fake()->randomFloat(3, 0, 999.999),
        ];
    }
}
