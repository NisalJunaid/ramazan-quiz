<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class QuizDay extends Model
{
    protected $guarded = ['id'];

    public function quizRange(): BelongsTo
    {
        return $this->belongsTo(QuizRange::class);
    }

    public function question(): HasOne
    {
        return $this->hasOne(Question::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(Attempt::class);
    }
}
