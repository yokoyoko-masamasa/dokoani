<?php

namespace App\Http\Controllers;

use App\Models\AnimeTitle;
use Illuminate\View\View;

class AnimeTitleController extends Controller
{
    // 仮実装。配信ロゴなどの本実装は後日行う
    public function show(AnimeTitle $animeTitle): View
    {
        return view('anime.show', ['animeTitle' => $animeTitle]);
    }
}
