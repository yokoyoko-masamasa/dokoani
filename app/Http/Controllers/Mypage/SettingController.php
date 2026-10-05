<?php

namespace App\Http\Controllers\Mypage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(Request $request): View
    {
        // 自分の契約をサービス名順に取る。サービスは1回で読む（N+1対策）
        $subscriptions = $request->user()->subscriptions()
            ->with('streamingService')
            ->get()
            ->sortBy(fn ($sub) => $sub->streamingService->name)
            ->values();

        return view('mypage.settings', ['subscriptions' => $subscriptions]);
    }
}
