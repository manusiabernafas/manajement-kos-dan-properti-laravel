<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'avatar',
        'password',
        'status',
        'theme_preference',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $attributes = [
        'status' => 'active',
        'theme_preference' => 'system',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function tenant(): HasOne
    {
        return $this->hasOne(Tenant::class);
    }

    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class, 'property_user');
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/'.$this->avatar);
        }

        $initials = collect(explode(' ', $this->name))->map(fn ($n) => strtoupper(substr($n, 0, 1)))->take(2)->implode('');

        return 'https://ui-avatars.com/api/?name='.urlencode($initials).'&background=5965F2&color=fff';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function canAccessProperty(int $propertyId): bool
    {
        if ($this->hasRole(['Super Admin', 'Owner'])) {
            return true;
        }

        return $this->properties()->where('properties.id', $propertyId)->exists();
    }

    public function routeNotificationForWhatsApp(): ?string
    {
        return $this->phone ?? $this->tenant?->phone;
    }
}
