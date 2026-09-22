<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StreamingService extends Model
{
    use HasFactory;

    // このサービスの配信状況を複数持つ
    public function availabilities(): HasMany
    {
        return $this->hasMany(AnimeAvailability::class);
    }

    // このサービスを契約しているユーザーを複数持つ
    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }
}
