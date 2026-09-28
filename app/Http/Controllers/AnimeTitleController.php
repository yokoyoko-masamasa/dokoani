<?php

namespace App\Http\Controllers;

use App\Models\AnimeTitle;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnimeTitleController extends Controller
{
    // 色名(green/orange/gray) → 完成したTailwindクラス名の対応表
    // Blade側で組み立てず、ここで完成した文字列にしておく
    private const BORDER_CLASSES = [
        'green'  => 'border-green-500',
        'orange' => 'border-orange-500',
        'gray'   => 'border-gray-300',
    ];

    public function show(Request $request, AnimeTitle $animeTitle): View
    {
        // 配信情報とサービス情報を先にまとめて取得する（N+1対策）
        $animeTitle->load(['availabilities.streamingService']);

        // ログイン中ユーザーが契約しているサービスIDの一覧（未ログインなら空配列）
        $subscribed = $request->user()?->subscriptions->pluck('streaming_service_id')->all() ?? [];

        // 配信情報をサービスIDごとにまとめ、1サービス1件のデータに変換する
        $logos = $animeTitle->availabilities
            ->groupBy('streaming_service_id')
            ->map(function ($rows) use ($subscribed) {
                // このグループの代表として、先頭行からサービス情報を取り出す
                $service = $rows->first()->streamingService;

                // このサービスが持つ配信形態一覧（例: [flatrate, rent]）
                $statuses = $rows->pluck('availability_status');

                // 契約していなければ問答無用でグレー
                // 契約していれば、flatrateがあれば緑、無ければrent/buyでオレンジ、それも無ければグレー
                $color = ! in_array($service->id, $subscribed, true)
                    ? 'gray'
                    : ($statuses->contains('flatrate')
                        ? 'green'
                        : ($statuses->intersect(['rent', 'buy'])->isNotEmpty()
                            ? 'orange'
                            : 'gray'));

                return [
                    'service'      => $service,
                    'border_class' => self::BORDER_CLASSES[$color],
                ];
            })
            ->values(); // グループ化で付いたキー(サービスID)を消し、0,1,2...の連番に戻す

        return view('anime.show', [
            'animeTitle' => $animeTitle,
            'logos'      => $logos,
        ]);
    }
}
