<?php

namespace Database\Factories;

use App\Models\StreamingService;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\UserSubscription>
 */
class UserSubscriptionFactory extends Factory
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
            'user_id' => User::factory(),
            'streaming_service_id' => StreamingService::factory(),
            'price' => fake()->numberBetween(1, 3000),
        ];
    }
}
