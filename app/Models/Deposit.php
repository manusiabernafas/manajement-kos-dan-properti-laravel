<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'contract_id', 'room_id', 'amount', 'received_date',
        'refund_amount', 'deduction', 'refund_date', 'status', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'received_date' => 'date',
        'refund_amount' => 'decimal:2',
        'deduction' => 'decimal:2',
        'refund_date' => 'date',
    ];

    public const STATUS_COLORS = [
        'held' => 'info',
        'partially_refunded' => 'warning',
        'refunded' => 'success',
        'forfeited' => 'danger',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function statusColor(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'muted';
    }
}
