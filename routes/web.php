<?php

use App\Http\Controllers\AnimeTitleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserAnimeListController;
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
    // 見たい/視聴済みの切り替えPUT（状態と行の有無で処理を分ける）
    Route::put('/list/{animeTitle}', [UserAnimeListController::class, 'update'])->name('list.update');
    // 見たい/視聴済みの行を削除するDELETE（行が無くても何もしない）
    Route::delete('/list/{animeTitle}', [UserAnimeListController::class, 'destroy'])->name('list.destroy');
});

require __DIR__.'/auth.php';
