<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSubscription extends Model
{
    use HasFactory;

    // 一括代入（create）を許可する列
    protected $fillable = [
        'user_id',
        'streaming_service_id',
        'price',
    ];

    // この契約情報は1人のユーザーに属する
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // この契約情報は1つの配信サービスに属する
    public function streamingService(): BelongsTo
    {
        return $this->belongsTo(StreamingService::class);
    }
}
