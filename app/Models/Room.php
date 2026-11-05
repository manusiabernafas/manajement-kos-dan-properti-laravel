<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id', 'building_id', 'floor_id', 'room_type_id', 'room_number',
        'room_name', 'price', 'deposit', 'capacity', 'description', 'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'deposit' => 'decimal:2',
    ];

    public const STATUS_COLORS = [
        'available' => 'success',
        'occupied' => 'info',
        'reserved' => 'warning',
        'maintenance' => 'danger',
        'inactive' => 'muted',
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

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function activeContract(): HasOne
    {
        return $this->hasOne(Contract::class)->where('status', 'active')->latestOfMany();
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function meters(): HasMany
    {
        return $this->hasMany(Meter::class);
    }

    public function maintenanceTickets(): HasMany
    {
        return $this->hasMany(MaintenanceTicket::class);
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function facilities(): MorphToMany
    {
        return $this->morphToMany(Facility::class, 'facilitable');
    }

    public function currentTenant(): ?Tenant
    {
        $contract = $this->activeContract;

        return $contract?->tenant;
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function statusColor(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'muted';
    }
}
