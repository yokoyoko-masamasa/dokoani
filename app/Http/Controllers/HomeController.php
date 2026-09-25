<?php

namespace App\Http\Controllers;

use App\Models\AnimeTitle;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        // 自DBの人気度が高い順に6件だけ取る（TMDBには問い合わせない）
        $popularTitles = AnimeTitle::orderByDesc('popularity')->limit(6)->get();

        return view('home', ['popularTitles' => $popularTitles]);
    }
}
