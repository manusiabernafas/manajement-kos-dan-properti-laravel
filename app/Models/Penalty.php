<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penalty extends Model
{
    protected $fillable = [
        'invoice_id', 'penalty_config_id', 'amount', 'reason', 'applied_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'applied_date' => 'date',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function penaltyConfig(): BelongsTo
    {
        return $this->belongsTo(PenaltyConfig::class);
    }
}
