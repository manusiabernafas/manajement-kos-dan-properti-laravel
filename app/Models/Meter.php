<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meter extends Model
{
    protected $fillable = [
        'room_id', 'meter_number', 'meter_type', 'unit', 'tariff', 'status',
    ];

    protected $casts = [
        'tariff' => 'decimal:2',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function readings(): HasMany
    {
        return $this->hasMany(MeterReading::class)->orderByDesc('reading_date');
    }

    public function latestReading(): ?MeterReading
    {
        return $this->readings()->first();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
