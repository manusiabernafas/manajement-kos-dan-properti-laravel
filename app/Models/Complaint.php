<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Complaint extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_number', 'tenant_id', 'property_id', 'room_id', 'category', 'title',
        'description', 'priority', 'status', 'assigned_to', 'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public const STATUS_COLORS = MaintenanceTicket::STATUS_COLORS;
    public const PRIORITY_COLORS = MaintenanceTicket::PRIORITY_COLORS;

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ComplaintComment::class)->orderBy('created_at');
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
