<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AdSlot extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'rotation_seconds' => 'integer',
    ];

    public function fixedAd(): BelongsTo
    {
        return $this->belongsTo(Ad::class, 'fixed_ad_id');
    }

    public function ads(): BelongsToMany
    {
        return $this->belongsToMany(Ad::class, 'ad_slot_ads')
            ->withPivot(['weight', 'sort_order', 'is_enabled'])
            ->withTimestamps();
    }
}
