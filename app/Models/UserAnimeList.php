<?php

namespace App\Models;

use App\Enums\ListStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAnimeList extends Model
{
    use HasFactory;

    // このリスト行は1人のユーザーに属する
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // このリスト行は1つの作品に属する
    public function animeTitle(): BelongsTo
    {
        return $this->belongsTo(AnimeTitle::class);
    }

    // status を文字列でなくListStatus型として扱う
    protected function casts(): array
    {
        return [
            'status' => ListStatus::class,
        ];
    }
}
