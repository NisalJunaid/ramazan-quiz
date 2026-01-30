<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    protected $guarded = ['id'];

    public function quizDay(): BelongsTo
    {
        return $this->belongsTo(QuizDay::class);
    }

    public function choices(): HasMany
    {
        return $this->hasMany(Choice::class);
    }
}
