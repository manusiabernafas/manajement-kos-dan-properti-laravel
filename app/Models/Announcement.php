<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'content', 'target_type', 'property_id', 'building_id', 'floor_id',
        'room_id', 'publish_at', 'expired_at', 'status', 'created_by',
    ];

    protected $casts = [
        'publish_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reads(): HasMany
    {
        return $this->hasMany(AnnouncementRead::class);
    }

    public function isActive(): bool
    {
        $now = now();
        $published = ! $this->publish_at || $this->publish_at->lte($now);
        $notExpired = ! $this->expired_at || $this->expired_at->gte($now);

        return $this->status === 'published' && $published && $notExpired;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'published')
            ->where(fn ($q) => $q->whereNull('publish_at')->orWhere('publish_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('expired_at')->orWhere('expired_at', '>=', now()));
    }
}
