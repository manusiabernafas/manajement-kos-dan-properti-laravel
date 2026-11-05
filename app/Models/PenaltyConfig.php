<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PenaltyConfig extends Model
{
    protected $fillable = [
        'property_id', 'calculation_type', 'amount', 'grace_period_days', 'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function penalties(): HasMany
    {
        return $this->hasMany(Penalty::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
