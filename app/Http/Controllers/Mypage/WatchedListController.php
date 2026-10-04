<?php

namespace App\Http\Controllers\Mypage;

use App\Enums\ListStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WatchedListController extends Controller
{
    public function index(Request $request): View
    {
        // 自分の watched だけを更新の新しい順に取る。ロゴまで1回で読む（N+1対策）
        $lists = $request->user()->userAnimeLists()
            ->where('status', ListStatus::Watched)
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->with(['animeTitle.availabilities.streamingService'])
            ->get();

        return view('mypage.watched', ['lists' => $lists]);
    }
}
