<?php

namespace App\Http\Controllers\Mypage;

use App\Enums\ListStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WantListController extends Controller
{
    public function index(Request $request): View
    {
        // 自分の want だけを優先度の昇順で取る。ロゴまで1回で読む（N+1対策）
        $lists = $request->user()->userAnimeLists()
            ->where('status', ListStatus::Want)
            ->orderBy('priority')
            ->with(['animeTitle.availabilities.streamingService'])
            ->get();

        return view('mypage.want', ['lists' => $lists]);
    }
}
