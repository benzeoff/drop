<?php

namespace App\Http\Controllers;

use App\Models\Component;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

class ComponentController extends Controller
{
    public function show(Component $component)
    {
        $component->image = $component->image ? asset('storage/' . $component->image) : null;
        return Inertia::render('Components/Show', [
            'component' => $component,
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
        ]);
    }
}
