<?php

namespace App\Models;

use App\Models\Tournament;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameMatch extends Model
{
    protected $table = 'game_matches';

    protected $fillable = [
        'tournament_id',
        'player1_id',
        'player2_id',
        'team1_id',
        'team2_id',
        'player1_score',
        'player2_score',
        'status',
    ];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function player1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player1_id');
    }

    public function player2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player2_id');
    }

    public function team1(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team1_id');
    }

    public function team2(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team2_id');
    }
}
