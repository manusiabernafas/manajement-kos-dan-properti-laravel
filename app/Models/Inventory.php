<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'inventory_code', 'property_id', 'room_id', 'category', 'name', 'serial_number',
        'purchase_date', 'purchase_price', 'condition', 'status', 'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
    ];

    public const CONDITION_COLORS = [
        'good' => 'success',
        'minor_damage' => 'warning',
        'major_damage' => 'danger',
        'lost' => 'danger',
    ];

    public const STATUS_COLORS = [
        'active' => 'success',
        'maintenance' => 'warning',
        'lost' => 'danger',
        'retired' => 'muted',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(InventoryHistory::class)->orderByDesc('created_at');
    }

    public function conditionColor(): string
    {
        return self::CONDITION_COLORS[$this->condition] ?? 'muted';
    }

    public function statusColor(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'muted';
    }
}
