<?php

namespace App\Models;

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
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'tournament_user');
    }

    public function matches()
    {
        return $this->hasMany(GameMatch::class, 'tournament_id');
    }

    public function isFull()
    {
        return $this->users()->count() >= $this->max_participants;
    }

    public function resources(): BelongsToMany
    {
        return $this->belongsToMany(Resource::class, 'tournament_resource');
    }
}
