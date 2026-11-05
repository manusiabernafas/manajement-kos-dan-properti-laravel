<?php

namespace Tests\Feature;

use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\SeedsRolesAndPermissions;
use Tests\TestCase;

class PropertyManagementTest extends TestCase
{
    use RefreshDatabase;
    use SeedsRolesAndPermissions;

    public function test_owner_can_create_property(): void
    {
        $this->seedRoles();
        $owner = $this->makeUser('Owner');

        $response = $this->actingAs($owner)->post('/properties', [
            'name' => 'Kost Melati', 'code' => 'KM01', 'address' => 'Jl. Melati No. 1',
            'city' => 'Jakarta', 'status' => 'active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('properties', ['code' => 'KM01', 'name' => 'Kost Melati']);
    }

    public function test_property_code_must_be_unique(): void
    {
        $this->seedRoles();
        $owner = $this->makeUser('Owner');
        Property::create(['name' => 'Kost A', 'code' => 'DUP01', 'address' => 'Jl. A', 'status' => 'active']);

        $response = $this->actingAs($owner)->post('/properties', [
            'name' => 'Kost B', 'code' => 'DUP01', 'address' => 'Jl. B', 'status' => 'active',
        ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_owner_can_update_property(): void
    {
        $this->seedRoles();
        $owner = $this->makeUser('Owner');
        $property = Property::create(['name' => 'Kost Lama', 'code' => 'KL01', 'address' => 'Jl. Lama', 'status' => 'active']);

        $response = $this->actingAs($owner)->put("/properties/{$property->id}", [
            'name' => 'Kost Baru', 'code' => 'KL01', 'address' => 'Jl. Baru', 'status' => 'active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('properties', ['id' => $property->id, 'name' => 'Kost Baru']);
    }

    public function test_property_with_rooms_cannot_be_deleted(): void
    {
        $this->seedRoles();
        $owner = $this->makeUser('Owner');
        $property = Property::create(['name' => 'Kost X', 'code' => 'KX01', 'address' => 'Jl. X', 'status' => 'active']);

        $building = $property->buildings()->create(['name' => 'Gedung A', 'code' => 'A', 'number_of_floors' => 1, 'status' => 'active']);
        $floor = $building->floors()->create(['floor_number' => 1, 'status' => 'active']);
        $roomType = $property->roomTypes()->create(['name' => 'Standard', 'code' => 'STD', 'monthly_price' => 1000000, 'deposit_amount' => 1000000, 'capacity' => 1, 'status' => 'active']);
        $property->rooms()->create([
            'building_id' => $building->id, 'floor_id' => $floor->id, 'room_type_id' => $roomType->id,
            'room_number' => 'A101', 'price' => 1000000, 'deposit' => 1000000, 'capacity' => 1, 'status' => 'available',
        ]);

        $response = $this->actingAs($owner)->delete("/properties/{$property->id}");

        $response->assertRedirect();
        $this->assertDatabaseHas('properties', ['id' => $property->id, 'deleted_at' => null]);
    }
}
