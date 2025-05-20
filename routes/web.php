<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\ComponentController;
use App\Http\Controllers\CyberNewsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [WelcomeController::class, '__invoke'])->name('welcome');

// Переносим маршрут /dashboard в ProfileController
Route::get('/dashboard', [ProfileController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Team and application routes
    Route::get('/teams', [ProfileController::class, 'indexTeams'])->name('teams.index');
    Route::get('/dashboard/teams/create', [ProfileController::class, 'createTeam'])->name('dashboard.teams.create');
    Route::post('/dashboard/teams', [ProfileController::class, 'storeTeam'])->name('dashboard.teams.store');
    Route::post('/dashboard/teams/join', [ProfileController::class, 'joinTeam'])->name('dashboard.teams.join');
    Route::post('/dashboard/teams/{team}/apply', [ProfileController::class, 'apply'])->name('dashboard.teams.apply');
    Route::get('/dashboard/teams/join', [ProfileController::class, 'joinTeamForm'])->name('dashboard.teams.join.form');
    Route::post('/dashboard/teams/{team}/leave', [ProfileController::class, 'leaveTeam'])->name('dashboard.teams.leave');
    Route::delete('/dashboard/teams/{team}', [ProfileController::class, 'deleteTeam'])->name('dashboard.teams.delete');
    Route::get('/dashboard/teams/{team}/edit', [ProfileController::class, 'editTeam'])->name('dashboard.teams.edit');
    Route::put('/dashboard/teams/{team}', [ProfileController::class, 'updateTeam'])->name('dashboard.teams.update');
    Route::post('/dashboard/teams/submit-application', [ProfileController::class, 'submitApplication'])->name('dashboard.teams.submit-application');
});

// Welcome page routes
Route::get('/about', function () {
    return Inertia::render('About', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('about');

// Booking routes
Route::get('/booking', [BookingController::class, 'index'])->name('booking');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store')->middleware('auth');

Route::put('/booking/{booking}/cancel', [BookingController::class, 'cancel'])
    ->middleware(['auth', 'can:modify,booking'])
    ->name('booking.cancel');

Route::put('/booking/{booking}/extend', [BookingController::class, 'extend'])
    ->middleware(['auth', 'can:modify,booking'])
    ->name('booking.extend');

// Tournament routes
Route::middleware(['auth'])->group(function () {
    Route::get('/tournaments', [TournamentController::class, 'index'])->name('tournaments');
    Route::post('/tournaments/{tournament}/register', [TournamentController::class, 'register'])->name('tournaments.register');
    Route::get('/tournaments/create', [TournamentController::class, 'create'])->name('tournaments.create')->middleware('admin');
    Route::post('/tournaments', [TournamentController::class, 'store'])->name('tournaments.store')->middleware('admin');
    Route::get('/tournaments/{tournament}/teams/create', [TournamentController::class, 'createTeamForm'])->name('tournaments.teams.create');
    Route::post('/tournaments/{tournament}/teams/create', [TournamentController::class, 'createTeam'])->name('tournaments.teams.store');
});

// Detail page routes
Route::get('/promotions/{promotion}', [PromotionController::class, 'show'])->name('promotions.show');
Route::get('/news/{news}', [CyberNewsController::class, 'show'])->name('news.show');
Route::get('/components/{component}', [ComponentController::class, 'show'])->name('components.show');

// Notification routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::put('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
});

// Quiz and Store routes
Route::middleware(['auth'])->group(function () {
    Route::get('/games', [QuizController::class, 'index'])->name('games');
    Route::post('/games/quiz/submit', [QuizController::class, 'submit'])->name('games.quiz.submit');
    Route::get('/store', [StoreController::class, 'index'])->name('store');
    Route::post('/store/purchase', [StoreController::class, 'purchase'])->name('store.purchase');
});

require __DIR__ . '/auth.php';
