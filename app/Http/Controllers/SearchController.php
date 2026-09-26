<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Models\AnimeTitle;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(SearchRequest $request): View|RedirectResponse
    {
        $q = $request->string('q')->trim();

        // 検索語が空なら、全件ヒットや空の結果ページを避けてトップへ戻す
        if ($q->isEmpty()) {
            return redirect()->route('home');
        }

        // % _ \ は検索の特殊文字なので、ただの文字として扱うよう無効化する
        $escaped = addcslashes($q, '%_\\');

        // 部分一致で人気順に20件ずつ取る。ロゴ用の関連は先読みして N+1 を防ぐ
        $animeTitles = AnimeTitle::with(['availabilities.streamingService'])
            ->where('title', 'ilike', '%'.$escaped.'%')
            ->orderByDesc('popularity')
            ->paginate(20)
            ->withQueryString();

        return view('search.index', ['animeTitles' => $animeTitles]);
    }
}
