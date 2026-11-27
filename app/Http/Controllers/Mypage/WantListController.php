<?php

namespace App\Http\Controllers\Mypage;

use App\Enums\ListStatus;
use App\Http\Controllers\Controller;
use App\Models\StreamingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WantListController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // 契約中のサービスを名前順に取る（絞り込みボタンの並び）
        $services = StreamingService::whereIn('id', $user->subscriptions()->select('streaming_service_id'))
            ->orderBy('name')
            ->get();

        // URL の services[] のうち契約中の ID だけ選ぶ。他の値は無視する
        $input = $request->query('services', []);
        $selectedIds = is_array($input)
            ? $services->pluck('id')->filter(fn ($id) => in_array((string) $id, $input, true))->values()->all()
            : [];

        // 自分の want だけを優先度の昇順で取る。ロゴまで1回で読む（N+1対策）
        $lists = $user->userAnimeLists()
            ->where('status', ListStatus::Want)
            // 選んだサービスのどれか1つで見放題の作品だけに絞る
            ->when($selectedIds, fn ($query) => $query->whereHas('animeTitle.availabilities', fn ($q) => $q
                ->where('availability_status', 'flatrate')
                ->whereIn('streaming_service_id', $selectedIds)))
            ->orderBy('priority')
            ->with(['animeTitle.availabilities.streamingService'])
            ->get();

        return view('mypage.want', [
            'lists' => $lists,
            'services' => $services,
            'selectedIds' => $selectedIds,
        ]);
    }
}
