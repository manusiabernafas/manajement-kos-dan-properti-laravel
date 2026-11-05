<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckOut extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id', 'tenant_id', 'room_id', 'checkout_date', 'outstanding_amount',
        'penalty_amount', 'damage_cost', 'deposit_amount', 'refund_amount', 'final_amount',
        'reason', 'notes', 'created_by',
    ];

    protected $casts = [
        'checkout_date' => 'date',
        'outstanding_amount' => 'decimal:2',
        'penalty_amount' => 'decimal:2',
        'damage_cost' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
