<?php

use App\Http\Controllers\AnimeTitleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Mypage\CoverageController;
use App\Http\Controllers\Mypage\SettingController;
use App\Http\Controllers\Mypage\WantListController;
use App\Http\Controllers\Mypage\WatchedListController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserAnimeListController;
use App\Http\Controllers\UserSubscriptionController;
use Illuminate\Support\Facades\Route;

// トップ画面。人気アニメ6件と使い方を表示する
Route::get('/', [HomeController::class, 'index'])->name('home');

// 検索結果一覧。q が空ならトップへ戻し、あれば部分一致で20件ずつ表示する
Route::get('/search', [SearchController::class, 'index'])->name('search.index');

// 検索候補。JSONで最大5件返す。読み取りのみなのでCSRFトークン不要
Route::get('/search/suggest', [SearchController::class, 'suggest'])->name('search.suggest');

// アニメ詳細画面（仮表示）
Route::get('/anime/{animeTitle}', [AnimeTitleController::class, 'show'])->name('anime.show');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// メール未認証のユーザーはverifiedで弾く
Route::middleware(['auth', 'verified'])->prefix('mypage')->group(function () {
    // 見たいリスト画面。自分のwantだけを優先度順に表示する
    Route::get('/want', [WantListController::class, 'index'])->name('mypage.want');
    // 視聴済みリスト画面。自分のwatchedだけを更新の新しい順に表示する
    Route::get('/watched', [WatchedListController::class, 'index'])->name('mypage.watched');
    // 契約状況画面。契約中サービスごとの料金と視聴可能本数を表示する
    Route::get('/subscriptions', [CoverageController::class, 'index'])->name('mypage.subscriptions');
    // 設定画面。契約中サブスクの一覧と、追加・料金変更・削除への入口を表示する
    Route::get('/settings', [SettingController::class, 'index'])->name('mypage.settings');
    // サブスク追加フォームを表示する。未契約のサービスだけを選べる
    Route::get('/settings/subscriptions/create', [UserSubscriptionController::class, 'create'])->name('subscriptions.create');
    // サブスクを1件保存する。保存後は設定画面へ戻る
    Route::post('/settings/subscriptions', [UserSubscriptionController::class, 'store'])->name('subscriptions.store');
    // 見たい/視聴済みの切り替えPUT（状態と行の有無で処理を分ける）
    Route::put('/list/{animeTitle}', [UserAnimeListController::class, 'update'])->name('list.update');
    // 見たい/視聴済みの行を削除するDELETE（行が無くても何もしない）
    Route::delete('/list/{animeTitle}', [UserAnimeListController::class, 'destroy'])->name('list.destroy');
});

require __DIR__.'/auth.php';
