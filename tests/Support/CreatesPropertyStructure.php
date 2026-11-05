<?php

namespace Tests\Support;

use App\Models\Building;
use App\Models\Floor;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Tenant;

trait CreatesPropertyStructure
{
    protected function makeRoom(array $roomOverrides = [], array $roomTypeOverrides = []): Room
    {
        $property = Property::create([
            'name' => 'Kost Test', 'code' => 'KT-'.uniqid(), 'address' => 'Jl. Test', 'status' => 'active',
        ]);

        $building = $property->buildings()->create([
            'name' => 'Gedung A', 'code' => 'A', 'number_of_floors' => 1, 'status' => 'active',
        ]);

        $floor = $building->floors()->create(['floor_number' => 1, 'status' => 'active']);

        $roomType = $property->roomTypes()->create(array_merge([
            'name' => 'Standard', 'code' => 'STD', 'monthly_price' => 1500000, 'deposit_amount' => 1500000,
            'capacity' => 1, 'status' => 'active',
        ], $roomTypeOverrides));

        return $property->rooms()->create(array_merge([
            'building_id' => $building->id, 'floor_id' => $floor->id, 'room_type_id' => $roomType->id,
            'room_number' => 'A101', 'price' => 1500000, 'deposit' => 1500000, 'capacity' => 1, 'status' => 'available',
        ], $roomOverrides));
    }

    protected function makeTenant(array $overrides = []): Tenant
    {
        return Tenant::create(array_merge([
            'tenant_code' => 'TNT-'.uniqid(),
            'full_name' => 'Budi Santoso',
            'phone' => '081234567890',
            'gender' => 'male',
            'status' => 'active',
        ], $overrides));
    }
}
