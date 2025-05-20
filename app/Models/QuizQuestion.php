<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    protected $fillable = ['question', 'options', 'correct_option'];

    protected $casts = [
        'options' => 'array',
    ];
}
