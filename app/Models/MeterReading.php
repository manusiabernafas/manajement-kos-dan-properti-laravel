<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeterReading extends Model
{
    protected $fillable = [
        'meter_id', 'reading_date', 'previous_value', 'current_value', 'usage',
        'tariff', 'total', 'photo_path', 'notes', 'invoice_id', 'created_by',
    ];

    protected $casts = [
        'reading_date' => 'date',
        'previous_value' => 'decimal:2',
        'current_value' => 'decimal:2',
        'usage' => 'decimal:2',
        'tariff' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function meter(): BelongsTo
    {
        return $this->belongsTo(Meter::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
