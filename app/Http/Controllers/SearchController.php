<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Models\AnimeTitle;
use Illuminate\Http\JsonResponse;
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

        // 部分一致で人気順に20件ずつ取る。ロゴ用の関連は先読みして N+1 を防ぐ
        $animeTitles = AnimeTitle::with(['availabilities.streamingService'])
            ->searchByTitle($q->value())
            ->paginate(20)
            ->withQueryString();

        return view('search.index', ['animeTitles' => $animeTitles]);
    }

    public function suggest(SearchRequest $request): JsonResponse
    {
        $q = $request->string('q')->trim();

        // 検索語が空ならDBに問い合わせず、空配列を返す
        if ($q->isEmpty()) {
            return response()->json([]);
        }

        // 部分一致で人気順に最大5件、id とタイトルだけ返す
        $animeTitles = AnimeTitle::searchByTitle($q->value())
            ->limit(5)
            ->get(['id', 'title']);

        return response()->json($animeTitles);
    }
}
