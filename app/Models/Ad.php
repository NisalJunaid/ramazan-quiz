<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ad extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function slots(): BelongsToMany
    {
        return $this->belongsToMany(AdSlot::class, 'ad_slot_ads')
            ->withPivot(['weight', 'sort_order', 'is_enabled'])
            ->withTimestamps();
    }

    public function isActiveAt(?Carbon $now = null): bool
    {
        $now = $now ?? Carbon::now();

        if (! $this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at->greaterThan($now)) {
            return false;
        }

        if ($this->ends_at && $this->ends_at->lessThan($now)) {
            return false;
        }

        return true;
    }
}
