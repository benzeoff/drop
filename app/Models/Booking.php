<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Booking extends Model
{
    protected $fillable = ['user_id', 'resource_id', 'start_time', 'end_time', 'price', 'status'];

    protected $appends = ['status'];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'price' => 'decimal:2',
    ];

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusAttribute()
    {
        return Cache::remember("booking_status_{$this->id}", now()->addMinute(), function () {
            $now = Carbon::now();

            // Если статус уже установлен в базе, возвращаем его
            if (!is_null($this->attributes['status'])) {
                if ($this->attributes['status'] === 'completed' && $this->user) {
                    $this->user->notify(new \App\Notifications\BookingCompleted($this));
                }
                return $this->attributes['status'];
            }

            // Если время истекло, но статус не обновлён (доп. проверка)
            if ($now->greaterThanOrEqualTo(Carbon::parse($this->end_time))) {
                if ($this->user) {
                    $this->user->notify(new \App\Notifications\BookingCompleted($this));
                }
                return 'completed';
            }

            return 'confirmed';
        });
    }

    public function cancel()
    {
        $this->update(['status' => 'cancelled']);
        if ($this->resource) {
            $this->resource->update(['status' => 'available']);
        }
        Cache::forget("booking_status_{$this->id}");
    }
}
