<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'category', 'description', 'icon', 'status',
    ];

    public function properties(): MorphToMany
    {
        return $this->morphedByMany(Property::class, 'facilitable');
    }

    public function roomTypes(): MorphToMany
    {
        return $this->morphedByMany(RoomType::class, 'facilitable');
    }

    public function rooms(): MorphToMany
    {
        return $this->morphedByMany(Room::class, 'facilitable');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
