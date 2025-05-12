<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Resource extends Model
{
    protected $fillable = ['name', 'type', 'category', 'status', 'specifications', 'equipment'];

    protected $casts = [
        'specifications' => 'array',
        'equipment' => 'array',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Проверяет, доступен ли ресурс
     *
     * @return bool
     */
    public function isAvailable()
    {
        return $this->status === 'available';
    }

    public function tournaments(): BelongsToMany
    {
        return $this->belongsToMany(Tournament::class, 'tournament_resource');
    }
}
