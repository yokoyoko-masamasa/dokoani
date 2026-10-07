<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserSubscriptionRequest;
use App\Http\Requests\UpdateUserSubscriptionRequest;
use App\Models\StreamingService;
use App\Models\UserSubscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserSubscriptionController extends Controller
{
    public function create(Request $request): View
    {
        // 自分が契約していないサービスだけを名前順に取る
        $services = StreamingService::whereDoesntHave('subscriptions', function ($query) use ($request) {
            // 他人の契約で除外されないよう、自分の契約だけを見る
            $query->where('user_id', $request->user()->id);
        })->orderBy('name')->get();

        return view('mypage.subscription-form', ['services' => $services]);
    }

    public function store(StoreUserSubscriptionRequest $request): RedirectResponse
    {
        // ログイン中のユーザーの契約として保存する（user_id は自動で入る）
        $request->user()->subscriptions()->create($request->validated());

        return redirect()->route('mypage.settings');
    }

    public function edit(UserSubscription $userSubscription): View
    {
        // 自分の契約かどうかはルートの can で確認済み。サービス名は表示だけ
        return view('mypage.subscription-form', ['userSubscription' => $userSubscription]);
    }

    public function update(UpdateUserSubscriptionRequest $request, UserSubscription $userSubscription): RedirectResponse
    {
        // 月額料金だけを更新する。サービスは変えられない
        $userSubscription->update($request->validated());

        return redirect()->route('mypage.settings');
    }

    public function destroy(UserSubscription $userSubscription): RedirectResponse
    {
        // 契約を1件削除する。自分の契約かどうかはルートの can で確認済み
        $userSubscription->delete();

        return redirect()->route('mypage.settings');
    }
}
