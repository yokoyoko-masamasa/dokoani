<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

// トップ画面。人気アニメ6件と使い方を表示する
Route::get('/', [HomeController::class, 'index'])->name('home');

// 検索結果一覧。q が空ならトップへ戻し、あれば部分一致で20件ずつ表示する
Route::get('/search', [SearchController::class, 'index'])->name('search.index');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
