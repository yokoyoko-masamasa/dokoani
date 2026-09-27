<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnimeTitle extends Model
{
    use HasFactory;

    // 一括代入（create）を許可する列
    protected $fillable = [
        'tmdb_id',
        'title',
        'synopsis',
        'poster_image_url',
        'popularity',
        'last_synced_at',
    ];
    
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

    // タイトルの部分一致検索。人気順に並べる（検索結果・候補で共通利用）
    public function scopeSearchByTitle(Builder $query, string $q): Builder
    {
        // % _ \ は検索の特殊文字なので、ただの文字として扱うよう無効化する
        $escaped = addcslashes($q, '%_\\');

        return $query->where('title', 'ilike', '%'.$escaped.'%')
            ->orderByDesc('popularity');
    }
}
