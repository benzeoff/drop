<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\ComponentController;
use App\Http\Controllers\CyberNewsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\NotificationController;
use App\Models\Computer;
use App\Models\Pricing;
use App\Models\Zone;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [WelcomeController::class, '__invoke'])->name('welcome');

Route::get('/dashboard', function () {
    $user = auth()->user();
    if (!$user) {
        return redirect()->route('login')->with('error', 'Пожалуйста, войдите в систему.');
    }
    $user->load(['bookings' => function ($query) {
        $query->with('resource');
    }]);
    return Inertia::render('Dashboard', [
        'bookings' => $user->bookings,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Маршруты для nav страницы Welcome
Route::get('/about', function () {
    return Inertia::render('About', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('about');

Route::get('/booking', [BookingController::class, 'index'])->name('booking');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store')->middleware('auth');

// Проверка прав: бронирование должно принадлежать текущему пользователю
Route::put('/booking/{booking}/cancel', [BookingController::class, 'cancel'])
    ->middleware(['auth', 'can:modify,booking'])
    ->name('booking.cancel');

Route::put('/booking/{booking}/extend', [BookingController::class, 'extend'])
    ->middleware(['auth', 'can:modify,booking'])
    ->name('booking.extend');

Route::get('/tournaments', [TournamentController::class, 'index'])->name('tournaments');
Route::post('/tournaments/{tournament}/register', [TournamentController::class, 'register'])->name('tournaments.register');
Route::get('/tournaments/create', [TournamentController::class, 'create'])->name('tournaments.create')->middleware(['auth', 'admin']);
Route::post('/tournaments', [TournamentController::class, 'store'])->name('tournaments.store')->middleware(['auth', 'admin']);

// Маршруты для страниц деталей
Route::get('/promotions/{promotion}', [PromotionController::class, 'show'])->name('promotions.show');
Route::get('/news/{news}', [CyberNewsController::class, 'show'])->name('news.show');
Route::get('/components/{component}', [ComponentController::class, 'show'])->name('components.show');

// Уведомления
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::put('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
});

require __DIR__ . '/auth.php';
