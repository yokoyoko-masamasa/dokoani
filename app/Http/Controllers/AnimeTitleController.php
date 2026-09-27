<?php

namespace App\Http\Controllers;

use App\Models\AnimeTitle;
use Illuminate\View\View;

class AnimeTitleController extends Controller
{
    public function show(AnimeTitle $animeTitle): View
    {
        return view('anime.show', ['animeTitle' => $animeTitle]);
    }
}
