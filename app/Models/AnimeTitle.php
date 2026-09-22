<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnimeTitle extends Model
{
    use HasFactory;

    // この作品の配信状況を複数持つ
    public function availabilities(): HasMany
    {
        return $this->hasMany(AnimeAvailability::class);
    }

    // 見たい/視聴済みリストを複数持つ
    public function userAnimeLists(): HasMany
    {
        return $this->hasMany(UserAnimeList::class);
    }
}
