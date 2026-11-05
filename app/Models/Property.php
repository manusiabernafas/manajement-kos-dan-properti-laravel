<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'code', 'address', 'city', 'province', 'postal_code',
        'phone', 'email', 'description', 'latitude', 'longitude',
        'google_maps_url', 'bank_name', 'bank_account_number', 'bank_account_name',
        'rules', 'status', 'created_by',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class);
    }

    public function floors(): HasMany
    {
        return $this->hasManyThrough(Floor::class, Building::class);
    }

    public function roomTypes(): HasMany
    {
        return $this->hasMany(RoomType::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function maintenanceTickets(): HasMany
    {
        return $this->hasMany(MaintenanceTicket::class);
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }

    public function penaltyConfigs(): HasMany
    {
        return $this->hasMany(PenaltyConfig::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'property_user');
    }

    public function facilities(): MorphToMany
    {
        return $this->morphToMany(Facility::class, 'facilitable');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
