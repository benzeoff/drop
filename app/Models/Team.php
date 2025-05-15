<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Team extends Model
{
    protected $fillable = ['name', 'logo', 'description', 'max_players', 'captain_id', 'invite_code'];

    protected $casts = [
        'max_players' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($team) {
            if (!$team->invite_code) {
                $team->invite_code = Str::random(10);
            }
        });
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_user')
            ->withTimestamps();
    }

    public function captain(): BelongsTo
    {
        return $this->belongsTo(User::class, 'captain_id');
    }

    public function tournaments(): BelongsToMany
    {
        return $this->belongsToMany(Tournament::class, 'tournament_team')
            ->withTimestamps();
    }

    public function isFull(): bool
    {
        // Проверяем, что max_players больше 0, чтобы избежать некорректного поведения
        if ($this->max_players <= 0) {
            return false;
        }
        return $this->users()->count() >= $this->max_players;
    }
}
