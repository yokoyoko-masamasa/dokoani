<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// TMDBから新規アニメタイトルを取り込むバッチ（F47）
// 毎日04:00（日本時間）に自動実行する
// withoutOverlapping(): 前回の実行が終わっていなければ、今回の起動をスキップする（二重起動防止）
Schedule::command('tmdb:import-new')
    ->dailyAt('04:00')
    ->timezone('Asia/Tokyo')
    ->withoutOverlapping();

// 既存作品の配信情報を取り直すバッチ（F48）
// 毎週月曜03:00（日本時間）に自動実行する
// withoutOverlapping() が防ぐのは同じコマンド同士の二重起動だけ
Schedule::command('tmdb:update-availabilities')
    ->mondays()
    ->at('03:00')
    ->timezone('Asia/Tokyo')
    ->withoutOverlapping();
