<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Contract extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'contract_number', 'tenant_id', 'room_id', 'property_id', 'booking_id',
        'start_date', 'end_date', 'monthly_price', 'deposit', 'billing_day',
        'terms', 'status', 'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'monthly_price' => 'decimal:2',
        'deposit' => 'decimal:2',
    ];

    public const STATUS_COLORS = [
        'draft' => 'muted',
        'active' => 'success',
        'expired' => 'danger',
        'terminated' => 'danger',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function checkIn(): HasOne
    {
        return $this->hasOne(CheckIn::class);
    }

    public function checkOut(): HasOne
    {
        return $this->hasOne(CheckOut::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class);
    }

    public function daysUntilExpiry(): int
    {
        return (int) Carbon::today()->diffInDays($this->end_date, false);
    }

    public function statusColor(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'muted';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
