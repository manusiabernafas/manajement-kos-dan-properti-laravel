<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id', 'name', 'code', 'description', 'monthly_price', 'weekly_price',
        'daily_price', 'deposit_amount', 'capacity', 'size', 'status',
    ];

    protected $casts = [
        'monthly_price' => 'decimal:2',
        'weekly_price' => 'decimal:2',
        'daily_price' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function facilities(): MorphToMany
    {
        return $this->morphToMany(Facility::class, 'facilitable');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
