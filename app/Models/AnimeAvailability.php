<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnimeAvailability extends Model
{
    use HasFactory;

    // 一括代入（create）を許可する列
    protected $fillable = [
        'anime_title_id',
        'streaming_service_id',
        'availability_status',
    ];

    // この配信状況は1つの作品に属する
    public function animeTitle(): BelongsTo
    {
        return $this->belongsTo(AnimeTitle::class);
    }

    // この配信状況は1つの配信サービスに属する
    public function streamingService(): BelongsTo
    {
        return $this->belongsTo(StreamingService::class);
    }
}
