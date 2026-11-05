<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceTicket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_number', 'property_id', 'room_id', 'tenant_id', 'category', 'title',
        'description', 'priority', 'assigned_to', 'estimated_cost', 'actual_cost',
        'status', 'resolved_at', 'created_by',
    ];

    protected $casts = [
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'resolved_at' => 'datetime',
    ];

    public const STATUS_COLORS = [
        'open' => 'warning',
        'assigned' => 'info',
        'in_progress' => 'info',
        'waiting' => 'muted',
        'resolved' => 'success',
        'closed' => 'success',
        'cancelled' => 'danger',
    ];

    public const PRIORITY_COLORS = [
        'low' => 'muted',
        'medium' => 'info',
        'high' => 'warning',
        'urgent' => 'danger',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(MaintenanceComment::class)->orderBy('created_at');
    }

    public function statusColor(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'muted';
    }

    public function priorityColor(): string
    {
        return self::PRIORITY_COLORS[$this->priority] ?? 'muted';
    }
}
