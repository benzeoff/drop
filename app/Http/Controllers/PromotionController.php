<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

class PromotionController extends Controller
{
    public function show(Promotion $promotion)
    {
        $promotion->image = $promotion->image ? asset('storage/' . $promotion->image) : null;
        return Inertia::render('Promotions/Show', [
            'promotion' => $promotion,
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
        ]);
    }
}
