<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizRange extends Model
{
    protected $guarded = ['id'];

    public function quizDays(): HasMany
    {
        return $this->hasMany(QuizDay::class);
    }
}
