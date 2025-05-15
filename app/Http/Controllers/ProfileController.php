<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Tournament;
use App\Models\User;
use App\Models\Team;
use App\Models\Application;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's teams.
     */
    public function indexTeams(Request $request): Response
    {
        $user = $request->user();
        $teams = $user->teams()->with('users', 'captain')->get();
        return Inertia::render('Teams/Index', [
            'teams' => $teams,
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        $user = $request->user();
        return Inertia::render('Dashboard', [
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
            'bookings' => $user->bookings,
            'teams' => $user->teams()->with('users', 'captain')->get(),
            'tournaments' => Tournament::where('mode', 'team')->get(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('dashboard')->with('success', 'Профиль обновлён.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Show form to create a team.
     */
    public function createTeam()
    {
        $users = User::where('id', '!=', auth()->id())->get();
        $user = auth()->user();
        if ($user->teams()->exists()) {
            return Redirect::route('dashboard')->with('error', 'Вы уже состоите в команде. Выйдите из текущей команды, чтобы создать новую.');
        }
        return Inertia::render('Teams/CreateTeam', [
            'users' => $users->map(fn($user) => [
                'id' => $user->id,
                'name' => $user->name,
            ]),
        ]);
    }

    /**
     * Store a new team.
     */
    public function storeTeam(Request $request)
    {
        $user = auth()->user();
        if ($user->teams()->exists()) {
            return Redirect::route('dashboard')->with('error', 'Вы уже состоите в команде. Выйдите из текущей команды, чтобы создать новую.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:teams,name',
            'logo' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'max_players' => 'required|numeric|min:2|max:5',
            'user_ids' => 'required|array|min:1|max:4',
            'user_ids.*' => 'exists:users,id',
        ]);

        $team = Team::create([
            'name' => $request->name,
            'logo' => $request->logo,
            'description' => $request->description,
            'max_players' => $request->max_players,
            'captain_id' => auth()->id(),
        ]);

        $userIds = array_merge([auth()->id()], $request->user_ids);
        $team->users()->attach($userIds);

        return Redirect::route('dashboard')->with('success', 'Команда создана!');
    }

    /**
     * Show form to join a team.
     */
    public function joinTeamForm()
    {
        return Inertia::render('Teams/Join');
    }

    /**
     * Join a team using an invite code.
     */
    public function joinTeam(Request $request)
    {
        $request->validate([
            'invite_code' => 'required|string|exists:teams,invite_code',
        ]);

        $team = Team::where('invite_code', $request->invite_code)->first();
        $user = auth()->user();

        if ($team->isFull()) {
            return redirect()->back()->with('error', 'Команда заполнена.');
        }

        if ($team->users()->where('user_id', $user->id)->exists()) {
            return redirect()->back()->with('error', 'Вы уже в этой команде.');
        }

        $team->users()->attach($user->id);
        return Redirect::route('dashboard')->with('success', 'Вы присоединились к команде!');
    }

    /**
     * Leave a team.
     */
    public function leaveTeam($teamId)
    {
        $team = Team::findOrFail($teamId);
        $user = auth()->user();

        if (!$team->users()->where('user_id', $user->id)->exists()) {
            return Redirect::route('dashboard')->with('error', 'Вы не состоите в этой команде.');
        }

        if ($team->captain_id === $user->id) {
            // Если капитан уходит, передаём роль следующему пользователю
            $newCaptain = $team->users()->where('id', '!=', $user->id)->first();
            if ($newCaptain) {
                $team->update(['captain_id' => $newCaptain->id]);
            } else {
                // Если больше нет участников, удаляем команду
                $team->delete();
                return Redirect::route('dashboard')->with('success', 'Команда удалена, так как не осталось участников.');
            }
        }

        $team->users()->detach($user->id);
        return Redirect::route('dashboard')->with('success', 'Вы вышли из команды.');
    }

    /**
     * Delete a team (only for captain).
     */
    public function deleteTeam($teamId)
    {
        $team = Team::findOrFail($teamId);
        $user = auth()->user();

        if ($team->captain_id !== $user->id) {
            return Redirect::route('dashboard')->with('error', 'Только капитан может удалить команду.');
        }

        $team->delete();
        return Redirect::route('dashboard')->with('success', 'Команда удалена.');
    }

    /**
     * Show form to edit a team.
     */
    public function editTeam(Team $team)
    {
        $user = auth()->user();

        if ($team->captain_id !== $user->id) {
            return Redirect::route('dashboard')->with('error', 'Только капитан может редактировать команду.');
        }

        $team->load('users'); // Ensure users relationship is loaded
        $availableUsers = User::whereNotIn('id', $team->users->pluck('id')->push($user->id))->get();

        return Inertia::render('Teams/EditTeam', [
            'team' => $team,
            'users' => $availableUsers->map(fn($user) => [
                'id' => $user->id,
                'name' => $user->name,
            ]),
        ]);
    }

    /**
     * Update a team.
     */
    public function updateTeam(Request $request, $teamId)
    {
        $team = Team::findOrFail($teamId);
        $user = auth()->user();

        if ($team->captain_id !== $user->id) {
            return Redirect::route('dashboard')->with('error', 'Только капитан может редактировать команду.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:teams,name,' . $teamId,
            'logo' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'max_players' => 'required|numeric|min:2|max:5',
            'remove_user_ids' => 'nullable|array',
            'remove_user_ids.*' => 'exists:users,id',
            'add_user_ids' => 'nullable|array',
            'add_user_ids.*' => 'exists:users,id',
        ]);

        $team->update([
            'name' => $request->name,
            'logo' => $request->logo,
            'description' => $request->description,
            'max_players' => $request->max_players,
        ]);

        if ($request->remove_user_ids) {
            if (in_array($user->id, $request->remove_user_ids)) {
                return Redirect::route('dashboard')->with('error', 'Капитан не может удалить сам себя из команды.');
            }
            $team->users()->detach($request->remove_user_ids);
            if ($team->users()->count() === 1) {
                return Redirect::route('dashboard')->with('success', 'Все участники удалены, остался только капитан.');
            }
        }

        if ($request->add_user_ids) {
            $currentUserIds = $team->users->pluck('id')->toArray();
            $newUserIds = array_diff($request->add_user_ids, $currentUserIds);
            if (count($team->users) + count($newUserIds) > $team->max_players) {
                return Redirect::route('dashboard')->with('error', 'Превышено максимальное количество участников.');
            }
            $team->users()->attach($newUserIds);
        }

        return Redirect::route('dashboard')->with('success', 'Команда обновлена!');
    }

    /**
     * Apply for a tournament with a team.
     */
    public function apply(Request $request, $teamId)
    {
        $team = Team::findOrFail($teamId);

        // Removed captain check temporarily
        // if ($team->captain_id !== auth()->id()) {
        //     return redirect()->back()->with('error', 'Только капитан может подать заявку.');
        // }

        $request->validate([
            'tournament_id' => 'required|exists:tournaments,id',
        ]);

        $tournament = Tournament::findOrFail($request->tournament_id);
        if ($tournament->mode !== 'team') {
            return redirect()->back()->with('error', 'Этот турнир не для команд.');
        }

        if ($tournament->isFullForTeams()) {
            return redirect()->back()->with('error', 'Турнир заполнен.');
        }

        $existingApplication = Application::where('team_id', $team->id)
            ->where('tournament_id', $tournament->id)
            ->first();
        if ($existingApplication) {
            return redirect()->back()->with('error', 'Заявка на этот турнир уже подана.');
        }

        $application = Application::create([
            'team_id' => $team->id,
            'tournament_id' => $tournament->id,
            'status' => 'pending',
        ]);

        return Redirect::route('dashboard')->with('success', 'Заявка подана!');
    }

    public function submitApplication(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'tournament_id' => 'required|exists:tournaments,id',
        ]);

        $team = Team::findOrFail($request->team_id);
        $tournament = Tournament::findOrFail($request->tournament_id);

        // Check if the user is part of the team (optional, can be removed for now)
        if (!$team->users()->where('user_id', $user->id)->exists()) {
            return redirect()->back()->with('error', 'Вы не состоите в этой команде.');
        }

        // Check tournament mode
        if ($tournament->mode !== 'team') {
            return redirect()->back()->with('error', 'Этот турнир не для команд.');
        }

        // Check if tournament is full
        if ($tournament->isFullForTeams()) {
            return redirect()->back()->with('error', 'Турнир заполнен.');
        }

        // Check for existing application
        $existingApplication = Application::where('team_id', $team->id)
            ->where('tournament_id', $tournament->id)
            ->first();
        if ($existingApplication) {
            return redirect()->back()->with('error', 'Заявка на этот турнир уже подана.');
        }

        // Create the application
        $application = Application::create([
            'team_id' => $team->id,
            'tournament_id' => $tournament->id,
            'status' => 'pending',
        ]);

        return Redirect::route('dashboard')->with('success', 'Заявка подана!');
    }
}
