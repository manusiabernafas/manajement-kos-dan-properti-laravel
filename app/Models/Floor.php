<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Floor extends Model
{
    use HasFactory;

    protected $fillable = [
        'building_id', 'floor_number', 'name', 'description', 'status',
    ];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function property(): HasOneThrough
    {
        return $this->hasOneThrough(Property::class, Building::class, 'id', 'id', 'building_id', 'property_id');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
