<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pricing extends Model
{
    protected $fillable = ['type', 'category', 'package_type', 'duration', 'price'];
}
