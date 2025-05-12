<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\CyberNews;
use App\Models\Component;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'promotions' => Promotion::all(),
            'cyberNews' => CyberNews::all(),
            'components' => Component::all(),
        ]);
    }

    public function storePromotion(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'image' => 'required|image',
        ]);

        $path = $request->file('image')->store('promotions', 'public');

        Promotion::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $path,
        ]);

        return redirect()->back()->with('success', 'Акция добавлена!');
    }

    public function storeCyberNews(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'image' => 'required|image',
        ]);

        $path = $request->file('image')->store('news', 'public');

        CyberNews::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $path,
        ]);

        return redirect()->back()->with('success', 'Новость добавлена!');
    }

    public function storeComponent(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'image' => 'required|image',
        ]);

        $path = $request->file('image')->store('components', 'public');

        Component::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $path,
        ]);

        return redirect()->back()->with('success', 'Комплектующая добавлена!');
    }
}
