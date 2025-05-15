<?php

namespace App\Models;

use App\Models\User;
use App\Models\Team;
use App\Models\GameMatch;
use App\Models\Resource;
use App\Models\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tournament extends Model
{
    protected $fillable = [
        'name',
        'game',
        'date',
        'description',
        'prize',
        'max_participants',
        'mode',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tournament_user');
    }

    public function matches(): HasMany
    {
        return $this->hasMany(GameMatch::class, 'tournament_id');
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'tournament_team')
            ->withTimestamps();
    }

    public function resources(): BelongsToMany
    {
        return $this->belongsToMany(Resource::class, 'tournament_resource');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'tournament_id');
    }

    public function isFullForUsers(): bool
    {
        if ($this->max_participants <= 0) {
            return false;
        }
        return $this->users()->count() >= $this->max_participants;
    }

    public function isFullForTeams(): bool
    {
        if ($this->max_participants <= 0) {
            return false;
        }
        return $this->teams()->count() >= $this->max_participants;
    }
}
