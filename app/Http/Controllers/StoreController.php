<?php

namespace App\Http\Controllers;

use App\Models\StorePurchase;
use App\Models\UserPoint;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StoreController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $points = $user->points ? $user->points->points : 0;

        $items = [
            ['name' => 'Аренда 30 минут', 'points' => 300],
            ['name' => 'Аренда 1 час', 'points' => 600],
        ];

        return Inertia::render('Store', [
            'points' => $points,
            'items' => $items,
        ]);
    }

    public function purchase(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'item_name' => 'required|string',
            'points' => 'required|integer',
        ]);

        $itemName = $request->input('item_name');
        $pointsRequired = $request->input('points');

        $userPoints = $user->points ?: UserPoint::create(['user_id' => $user->id, 'points' => 0]);

        if ($userPoints->points < $pointsRequired) {
            return redirect()->back()->with('error', 'Недостаточно баллов для покупки!');
        }

        // Deduct points
        $userPoints->decrement('points', $pointsRequired);

        // Record the purchase
        StorePurchase::create([
            'user_id' => $user->id,
            'item_name' => $itemName,
            'points_spent' => $pointsRequired,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', "Покупка успешна! $itemName будет доступен после подтверждения администратором.");
    }
}
