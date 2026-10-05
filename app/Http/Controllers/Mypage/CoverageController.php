<?php

namespace App\Http\Controllers\Mypage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CoverageController extends Controller
{
    public function index(Request $request): View
    {
        // 絞り込みは全て ON 句に置く。WHERE だと0本の行が消える
        $sql = <<<'SQL'
            SELECT ss.id, ss.name, ss.logo_image_url, us.price,
                   COUNT(DISTINCT ual.anime_title_id) AS watchable_count
              FROM user_subscriptions us
              JOIN streaming_services ss ON ss.id = us.streaming_service_id
              LEFT JOIN anime_availabilities aa
                     ON aa.streaming_service_id = ss.id
                    AND aa.availability_status = 'flatrate'
              LEFT JOIN user_anime_lists ual
                     ON ual.anime_title_id = aa.anime_title_id
                    AND ual.user_id = us.user_id
                    AND ual.status = 'want'
             WHERE us.user_id = ?
             GROUP BY ss.id, ss.name, ss.logo_image_url, us.price
             ORDER BY ss.name
            SQL;

        $services = DB::select($sql, [$request->user()->id]);

        // 契約中のどれにも見放題が無い、見たい作品を数える
        $sqlUnwatchable = <<<'SQL'
            SELECT COUNT(*)
              FROM user_anime_lists ual
             WHERE ual.user_id = ?
               AND ual.status = 'want'
               AND NOT EXISTS (
                   SELECT 1
                     FROM anime_availabilities aa
                     JOIN user_subscriptions us
                       ON us.streaming_service_id = aa.streaming_service_id
                      AND us.user_id = ual.user_id
                    WHERE aa.anime_title_id = ual.anime_title_id
                      AND aa.availability_status = 'flatrate'
               )
            SQL;

        $unwatchableCount = DB::scalar($sqlUnwatchable, [$request->user()->id]);

        return view('mypage.subscriptions', [
            'services' => $services,
            'unwatchableCount' => $unwatchableCount,
        ]);
    }
}
