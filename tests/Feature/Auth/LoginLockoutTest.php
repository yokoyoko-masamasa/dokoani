<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginLockoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_is_locked_for_15_minutes_after_5_failures(): void
    {
        $user = User::factory()->create();

        // 5回目の失敗までは、通常の失敗メッセージが返る
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => $user->email, 'password' => 'wrong-password'])
                ->assertSessionHasErrors(['email' => trans('auth.failed')]);
        }

        // ロック中は正しいパスワードでも入れず、分で案内される
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors(['email' => trans('auth.throttle', ['minutes' => 15])]);
        $this->assertGuest();

        // 15分進めるとロックが解け、正しいパスワードで入れる
        $this->travel(15)->minutes();
        $this->post('/login', ['email' => $user->email, 'password' => 'password']);
        $this->assertAuthenticated();
    }
}
