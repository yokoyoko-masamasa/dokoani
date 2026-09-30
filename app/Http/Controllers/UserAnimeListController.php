<?php

namespace App\Http\Controllers;

use App\Enums\ListStatus;
use App\Http\Requests\UpdateUserAnimeListRequest;
use App\Models\AnimeTitle;
use App\Models\UserAnimeList;
use Illuminate\Http\RedirectResponse;

class UserAnimeListController extends Controller
{
    public function update(UpdateUserAnimeListRequest $request, AnimeTitle $animeTitle): RedirectResponse
    {
        // DBのstatusはEnumにcastされるため、先にEnumへ変換してから比べる
        $status = $request->enum('status', ListStatus::class);

        $user = $request->user();

        $existing = UserAnimeList::where('user_id', $user->id)
            ->where('anime_title_id', $animeTitle->id)
            ->first();

        // 既に同じ状態なら、優先度も含めて何も変えない（二重クリック対策）
        if ($existing && $existing->status === $status) {
            return back();
        }

        if ($status === ListStatus::Want) {
            // このユーザーのwantの最大優先度+1を採番する（0件なら1）
            $maxPriority = UserAnimeList::where('user_id', $user->id)
                ->where('status', ListStatus::Want)
                ->max('priority');
            $priority = ($maxPriority ?? 0) + 1;
        } else {
            $priority = null;
        }

        // 一意制約(user_id, anime_title_id)と組み合わせて作成・更新を1回で行う
        UserAnimeList::updateOrCreate(
            ['user_id' => $user->id, 'anime_title_id' => $animeTitle->id],
            ['status' => $status, 'priority' => $priority]
        );

        return back();
    }
}
