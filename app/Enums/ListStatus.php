<?php

namespace App\Enums;

// 見たい/視聴済みの2値だけを許可する型
enum ListStatus: string
{
    case Want = 'want';       // 見たい
    case Watched = 'watched'; // 視聴済み
}
