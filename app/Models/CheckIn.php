<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckIn extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id', 'tenant_id', 'room_id', 'booking_id', 'check_in_date',
        'monthly_price', 'deposit', 'billing_date', 'notes', 'created_by',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'billing_date' => 'date',
        'monthly_price' => 'decimal:2',
        'deposit' => 'decimal:2',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
