<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_code', 'full_name', 'nik', 'phone', 'email', 'gender', 'birth_place',
        'birth_date', 'address', 'occupation', 'company', 'emergency_contact_name',
        'emergency_contact_phone', 'emergency_contact_relation', 'photo', 'notes',
        'status', 'user_id', 'created_by',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(TenantDocument::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function activeContract(): HasOne
    {
        return $this->hasOne(Contract::class)->where('status', 'active')->latestOfMany();
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class);
    }

    public function maintenanceTickets(): HasMany
    {
        return $this->hasMany(MaintenanceTicket::class);
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class);
    }

    public function currentRoom(): ?Room
    {
        return $this->activeContract?->room;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function outstandingBalance(): float
    {
        return (float) $this->invoices()->whereIn('status', ['unpaid', 'partial', 'overdue'])->sum('remaining_amount');
    }
}
