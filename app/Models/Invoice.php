<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number', 'tenant_id', 'room_id', 'property_id', 'contract_id',
        'issue_date', 'due_date', 'period_month', 'period_year', 'subtotal',
        'discount', 'penalty', 'tax', 'total', 'paid_amount', 'remaining_amount',
        'status', 'notes', 'created_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'penalty' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
    ];

    public const STATUS_COLORS = [
        'draft' => 'muted',
        'unpaid' => 'warning',
        'partial' => 'info',
        'paid' => 'success',
        'overdue' => 'danger',
        'cancelled' => 'muted',
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

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function verifiedPayments(): HasMany
    {
        return $this->hasMany(Payment::class)->where('status', 'verified');
    }

    public function penaltyRecord(): HasOne
    {
        return $this->hasOne(Penalty::class);
    }

    public function meterReadings(): HasMany
    {
        return $this->hasMany(MeterReading::class);
    }

    public function statusColor(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'muted';
    }

    public function isOverdue(): bool
    {
        return $this->due_date->isPast() && in_array($this->status, ['unpaid', 'partial']);
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['unpaid', 'partial', 'overdue']);
    }
}
