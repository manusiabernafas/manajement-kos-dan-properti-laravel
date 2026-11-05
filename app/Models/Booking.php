<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_number', 'tenant_id', 'room_id', 'booking_date', 'check_in_date',
        'expected_checkout_date', 'monthly_price', 'deposit', 'status', 'notes', 'created_by',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'check_in_date' => 'date',
        'expected_checkout_date' => 'date',
        'monthly_price' => 'decimal:2',
        'deposit' => 'decimal:2',
    ];

    public const STATUS_COLORS = [
        'inquiry' => 'muted',
        'reserved' => 'warning',
        'confirmed' => 'info',
        'cancelled' => 'danger',
        'expired' => 'danger',
        'checked_in' => 'success',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function checkIn(): HasOne
    {
        return $this->hasOne(CheckIn::class);
    }

    public function contract(): HasOne
    {
        return $this->hasOne(Contract::class);
    }

    public function statusColor(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'muted';
    }
}
