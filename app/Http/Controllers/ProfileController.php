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
use Illuminate\Support\Facades\Log;
use Inertia\Response as InertiaResponse;

class ProfileController extends Controller
{
    /**
     * Display the user's dashboard.
     */
    public function dashboard(Request $request): Response
    {
        $user = $request->user();

        $user->load(['bookings' => function ($query) {
            $query->with('resource');
        }, 'teams' => function ($query) {
            $query->with('users', 'captain');
        }]);

        return Inertia::render('Dashboard', [
            'bookings' => $user->bookings,
            'teams' => $user->teams,
            'tournaments' => Tournament::where('mode', 'team')->get(),
        ]);
    }

    /**
     * Display the user's teams.
     */
    public function indexTeams(Request $request): InertiaResponse|RedirectResponse
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Пожалуйста, войдите в систему.');
        }

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
        return Inertia::render('Profile/Edit', [
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
        $user = $request->user();
        if (!$user) {
            Log::error('User is not authenticated in ProfileController@update');
            return redirect()->route('login')->with('error', 'Пожалуйста, войдите в систему.');
        }

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('dashboard')->with('success', 'Профиль обновлён.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (!$user) {
            Log::error('User is not authenticated in ProfileController@destroy');
            return redirect()->route('login')->with('error', 'Пожалуйста, войдите в систему.');
        }

        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Show form to create a team.
     */
    public function createTeam(): Response|RedirectResponse
    {
        $user = auth()->user();
        if (!$user) {
            Log::error('User is not authenticated in ProfileController@createTeam');
            return redirect()->route('login')->with('error', 'Пожалуйста, войдите в систему.');
        }

        $users = User::where('id', '!=', $user->id)->get();
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
    public function storeTeam(Request $request): RedirectResponse
    {
        $user = auth()->user();
        if (!$user) {
            Log::error('User is not authenticated in ProfileController@storeTeam');
            return redirect()->route('login')->with('error', 'Пожалуйста, войдите в систему.');
        }

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
            'captain_id' => $user->id,
        ]);

        $userIds = array_merge([$user->id], $request->user_ids);
        $team->users()->attach($userIds);

        return Redirect::route('dashboard')->with('success', 'Команда создана!');
    }

    /**
     * Show form to join a team.
     */
    public function joinTeamForm(): Response
    {
        return Inertia::render('Teams/Join');
    }

    /**
     * Join a team using an invite code.
     */
    public function joinTeam(Request $request): RedirectResponse
    {
        $user = auth()->user();
        if (!$user) {
            Log::error('User is not authenticated in ProfileController@joinTeam');
            return redirect()->route('login')->with('error', 'Пожалуйста, войдите в систему.');
        }

        $request->validate([
            'invite_code' => 'required|string|exists:teams,invite_code',
        ]);

        $team = Team::where('invite_code', $request->invite_code)->first();
        if (!$team) {
            return redirect()->back()->with('error', 'Неверный код приглашения.');
        }

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
    public function leaveTeam($teamId): RedirectResponse
    {
        $user = auth()->user();
        if (!$user) {
            Log::error('User is not authenticated in ProfileController@leaveTeam');
            return redirect()->route('login')->with('error', 'Пожалуйста, войдите в систему.');
        }

        $team = Team::findOrFail($teamId);

        if (!$team->users()->where('user_id', $user->id)->exists()) {
            return Redirect::route('dashboard')->with('error', 'Вы не состоите в этой команде.');
        }

        if ($team->captain_id === $user->id) {
            $newCaptain = $team->users()->where('id', '!=', $user->id)->first();
            if ($newCaptain) {
                $team->update(['captain_id' => $newCaptain->id]);
            } else {
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
    public function deleteTeam($teamId): RedirectResponse
    {
        $user = auth()->user();
        if (!$user) {
            Log::error('User is not authenticated in ProfileController@deleteTeam');
            return redirect()->route('login')->with('error', 'Пожалуйста, войдите в систему.');
        }

        $team = Team::findOrFail($teamId);

        if ($team->captain_id !== $user->id) {
            return Redirect::route('dashboard')->with('error', 'Только капитан может удалить команду.');
        }

        $team->delete();
        return Redirect::route('dashboard')->with('success', 'Команда удалена.');
    }

    /**
     * Show form to edit a team.
     */
    public function editTeam(Team $team): Response|RedirectResponse
    {
        $user = auth()->user();
        if (!$user) {
            Log::error('User is not authenticated in ProfileController@editTeam');
            return redirect()->route('login')->with('error', 'Пожалуйста, войдите в систему.');
        }

        if ($team->captain_id !== $user->id) {
            return Redirect::route('dashboard')->with('error', 'Только капитан может редактировать команду.');
        }

        $team->load('users');
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
    public function updateTeam(Request $request, $teamId): RedirectResponse
    {
        $user = auth()->user();
        if (!$user) {
            Log::error('User is not authenticated in ProfileController@updateTeam');
            return redirect()->route('login')->with('error', 'Пожалуйста, войдите в систему.');
        }

        $team = Team::findOrFail($teamId);

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
    public function apply(Request $request, $teamId): RedirectResponse
    {
        $user = auth()->user();
        if (!$user) {
            Log::error('User is not authenticated in ProfileController@apply');
            return redirect()->route('login')->with('error', 'Пожалуйста, войдите в систему.');
        }

        $team = Team::findOrFail($teamId);

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

    /**
     * Submit an application for a tournament.
     */
    public function submitApplication(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            Log::error('User is not authenticated in ProfileController@submitApplication');
            return redirect()->route('login')->with('error', 'Пожалуйста, войдите в систему.');
        }

        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'tournament_id' => 'required|exists:tournaments,id',
        ]);

        $team = Team::findOrFail($request->team_id);
        $tournament = Tournament::findOrFail($request->tournament_id);

        if (!$team->users()->where('user_id', $user->id)->exists()) {
            return redirect()->back()->with('error', 'Вы не состоите в этой команде.');
        }

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
}
