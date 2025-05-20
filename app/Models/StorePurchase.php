<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StorePurchase extends Model
{
    protected $fillable = ['user_id', 'item_name', 'points_spent', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
