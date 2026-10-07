<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserSubscription;

class UserSubscriptionPolicy
{
    public function update(User $user, UserSubscription $userSubscription): bool
    {
        // 自分の契約だけ変更できる（user_id が一致するかを見る）
        return $user->id === $userSubscription->user_id;
    }

    public function delete(User $user, UserSubscription $userSubscription): bool
    {
        // 自分の契約だけ削除できる（user_id が一致するかを見る）
        return $user->id === $userSubscription->user_id;
    }
}
