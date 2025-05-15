<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    protected $fillable = ['team_id', 'tournament_id', 'status'];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    // Аксессоры для безопасного доступа
    public function getTeamNameAttribute(): string
    {
        return $this->team ? $this->team->name : 'Команда не найдена';
    }

    public function getTournamentNameAttribute(): string
    {
        return $this->tournament ? $this->tournament->name : 'Турнир не найден';
    }
}
