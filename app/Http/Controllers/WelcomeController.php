<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use App\Models\CyberNews;
use App\Models\Component;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class WelcomeController extends Controller
{
    public function __invoke()
    {
        $components = Component::all()->map(function ($component) {
            $component->image = $component->image ? asset('storage/' . $component->image) : null;
            return $component;
        })->groupBy('zone');

        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
            'promotions' => Promotion::all()->map(function ($promotion) {
                $promotion->image = $promotion->image ? asset('storage/' . $promotion->image) : null;
                return $promotion;
            }),
            'cyberNews' => CyberNews::all()->map(function ($news) {
                $news->image = $news->image ? asset('storage/' . $news->image) : null;
                $news->formattedDate = $news->created_at->format('d.m.Y H:i');
                return $news;
            }),
            'componentsByZone' => $components,
        ]);
    }
}
