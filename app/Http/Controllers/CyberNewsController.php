<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\CyberNews;

class CyberNewsController extends Controller
{
    public function show(CyberNews $news)
    {
        $news->image = $news->image ? asset('storage/' . $news->image) : null;
        return Inertia::render('News/Show', [
            'news' => $news,
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
        ]);
    }
}
