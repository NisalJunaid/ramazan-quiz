<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Choice extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
