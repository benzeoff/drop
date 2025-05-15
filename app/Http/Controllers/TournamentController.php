<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\GameMatch;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TournamentController extends Controller
{
    public function index(Request $request)
    {
        $query = Tournament::withCount('users')->with('matches.player1', 'matches.player2');

        if ($request->has('game') && $request->game !== 'all') {
            $query->where('game', $request->game);
        }

        if ($request->wantsJson() || $request->has('for_teams')) {
            return Tournament::where('mode', 'team')->get();
        }

        $tournaments = $query->paginate(6)->withQueryString();
        $games = Tournament::select('game')->distinct()->pluck('game');

        return Inertia::render('Tournaments', [
            'tournaments' => $tournaments,
            'games' => $games,
            'canLogin' => auth()->check(),
            'canRegister' => !auth()->check(),
        ]);
    }

    public function register($tournament)
    {
        $user = auth()->user();
        $tournament = Tournament::findOrFail($tournament);

        if ($tournament->mode === 'team') {
            return redirect()->route('teams.create', ['tournament' => $tournament->id])
                ->with('message', 'Создайте или выберите команду для регистрации.');
        }

        if ($tournament->users()->where('user_id', $user->id)->exists()) {
            return redirect()->back()->with('error', 'Вы уже зарегистрированы на этот турнир.');
        }

        $tournament->users()->attach($user->id);
        return redirect()->back()->with('success', 'Вы успешно зарегистрированы на турнир.');
    }

    public function create()
    {
        $users = User::all()->map(fn($user) => [
            'id' => $user->id,
            'name' => $user->name,
        ]);
        $teams = Team::all()->map(fn($team) => [
            'id' => $team->id,
            'name' => $team->name,
        ]);

        return Inertia::render('CreateTournament', [
            'canLogin' => auth()->check(),
            'canRegister' => !auth()->check(),
            'users' => $users,
            'teams' => $teams,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'game' => 'required|string|max:255',
            'date' => 'required|date|after:now',
            'description' => 'nullable|string',
            'prize' => 'nullable|string|max:255',
            'max_participants' => 'required|integer|min:2',
            'mode' => 'required|in:1v1,team',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
            'teams' => 'nullable|array',
            'teams.*' => 'exists:teams,id',
        ]);

        $tournament = Tournament::create($validated);

        // Attach users or teams based on mode
        if ($tournament->mode === '1v1' && !empty($validated['users'])) {
            $tournament->users()->attach($validated['users']);
        } elseif ($tournament->mode === 'team' && !empty($validated['teams'])) {
            $tournament->teams()->attach($validated['teams']);
        }

        return redirect()->route('tournaments')->with('success', 'Турнир успешно создан!');
    }

    public function generateMatches(Tournament $tournament)
    {
        if ($tournament->matches()->exists()) {
            return back()->with('error', 'Матчи уже сгенерированы.');
        }

        if ($tournament->mode === '1v1') {
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
        } else {
            $teams = $tournament->teams()->pluck('teams.id')->shuffle()->toArray();
            if (count($teams) < 2) {
                return back()->with('error', 'Недостаточно команд для генерации матчей.');
            }

            $matches = [];
            for ($i = 0; $i < count($teams); $i += 2) {
                if (isset($teams[$i + 1])) {
                    $matches[] = [
                        'tournament_id' => $tournament->id,
                        'team1_id' => $teams[$i],
                        'team2_id' => $teams[$i + 1],
                        'status' => 'pending',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            GameMatch::insert($matches);
        }

        return back()->with('success', 'Матчи успешно сгенерированы!');
    }

    public function createTeamForm($tournament)
    {
        $tournament = Tournament::findOrFail($tournament);
        $users = User::all();
        return Inertia::render('Teams/Create', [
            'tournament' => $tournament->id,
            'users' => $users->map(fn($user) => [
                'id' => $user->id,
                'name' => $user->name,
            ]),
        ]);
    }

    public function createTeam(Request $request, $tournament)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:teams,name',
            'user_ids' => 'required|array|min:2|max:5',
            'user_ids.*' => 'exists:users,id',
        ]);

        $currentUserId = auth()->id();
        if (!in_array($currentUserId, $request->user_ids)) {
            return redirect()->back()->with('error', 'Вы должны быть в составе команды.');
        }

        $tournament = Tournament::findOrFail($tournament);
        if ($tournament->mode === 'team' && $tournament->teams()->count() >= $tournament->max_participants) {
            return redirect()->back()->with('error', 'Достигнуто максимальное количество команд.');
        }

        $existingTeam = $tournament->teams()->whereHas('users', function ($query) use ($currentUserId) {
            $query->where('user_id', $currentUserId);
        })->first();

        if ($existingTeam) {
            return redirect()->back()->with('error', 'Вы уже зарегистрированы в команде на этот турнир.');
        }

        $team = Team::create([
            'name' => $request->name,
            'max_players' => count($request->user_ids),
        ]);

        $team->users()->attach($request->user_ids);

        $tournament->teams()->attach($team->id);

        return redirect()->route('tournaments')->with('success', 'Команда создана и зарегистрирована на турнир.');
    }
}
