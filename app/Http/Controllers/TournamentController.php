<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\GameMatch;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TournamentController extends Controller
{
    public function index(Request $request)
    {
        $query = Tournament::withCount('users')->with('matches.player1', 'matches.player2');

        // Фильтр по игре
        if ($request->has('game') && $request->game !== 'all') {
            $query->where('game', $request->game);
        }

        // Пагинация
        $tournaments = $query->paginate(6)->withQueryString();

        // Список уникальных игр для фильтра
        $games = Tournament::select('game')->distinct()->pluck('game');

        return Inertia::render('Tournaments', [
            'tournaments' => $tournaments,
            'games' => $games,
            'canLogin' => auth()->check(),
            'canRegister' => !auth()->check(),
        ]);
    }

    public function register(Request $request, Tournament $tournament)
    {
        $user = auth()->user();

        if (!$user) {
            return back()->with('error', 'Войдите, чтобы зарегистрироваться.');
        }

        if ($tournament->isFull()) {
            return back()->with('error', 'Турнир заполнен.');
        }

        if ($tournament->users()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'Вы уже зарегистрированы.');
        }

        $tournament->users()->attach($user->id);

        return back()->with('success', 'Вы успешно зарегистрированы на турнир!');
    }

    public function create()
    {
        return Inertia::render('CreateTournament', [
            'canLogin' => auth()->check(),
            'canRegister' => !auth()->check(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'game' => 'required|string|max:255',
            'date' => 'required|date|after:now',
            'description' => 'nullable|string',
            'prize' => 'nullable|string|max:255',
            'max_participants' => 'required|integer|min:2',
        ]);

        Tournament::create($request->all());

        return redirect()->route('tournaments')->with('success', 'Турнир успешно создан!');
    }

    public function generateMatches(Tournament $tournament)
    {
        if ($tournament->matches()->exists()) {
            return back()->with('error', 'Матчи уже сгенерированы.');
        }

        $users = $tournament->users()->pluck('users.id')->shuffle()->toArray();
        if (count($users) < 2) {
            return back()->with('error', 'Недостаточно участников для генерации матчей.');
        }

        $matches = [];
        for ($i = 0; $i < count($users); $i += 2) {
            if (isset($users[$i + 1])) {
                $matches[] = [
                    'tournament_id' => $tournament->id,
                    'player1_id' => $users[$i],
                    'player2_id' => $users[$i + 1],
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        GameMatch::insert($matches);

        return back()->with('success', 'Матчи успешно сгенерированы!');
    }
}
