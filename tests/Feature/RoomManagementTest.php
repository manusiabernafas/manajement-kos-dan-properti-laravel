<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesPropertyStructure;
use Tests\Support\SeedsRolesAndPermissions;
use Tests\TestCase;

class RoomManagementTest extends TestCase
{
    use RefreshDatabase;
    use SeedsRolesAndPermissions;
    use CreatesPropertyStructure;

    public function test_manager_can_create_room(): void
    {
        $this->seedRoles();
        $manager = $this->makeUser('Manager');
        $room = $this->makeRoom();
        $property = $room->property;
        $building = $room->building;
        $floor = $room->floor;
        $roomType = $room->roomType;

        $response = $this->actingAs($manager)->post('/rooms', [
            'property_id' => $property->id, 'building_id' => $building->id, 'floor_id' => $floor->id,
            'room_type_id' => $roomType->id, 'room_number' => 'A102', 'price' => 1500000,
            'deposit' => 1500000, 'capacity' => 1, 'status' => 'available',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('rooms', ['room_number' => 'A102', 'property_id' => $property->id]);
    }

    public function test_room_number_must_be_unique_within_property(): void
    {
        $this->seedRoles();
        $manager = $this->makeUser('Manager');
        $room = $this->makeRoom();

        $response = $this->actingAs($manager)->post('/rooms', [
            'property_id' => $room->property_id, 'building_id' => $room->building_id, 'floor_id' => $room->floor_id,
            'room_type_id' => $room->room_type_id, 'room_number' => $room->room_number, 'price' => 1500000,
            'deposit' => 1500000, 'capacity' => 1, 'status' => 'available',
        ]);

        $response->assertSessionHasErrors('room_number');
    }

    public function test_occupied_room_cannot_be_deleted(): void
    {
        $this->seedRoles();
        $manager = $this->makeUser('Manager');
        $room = $this->makeRoom(['status' => 'occupied']);

        $response = $this->actingAs($manager)->delete("/rooms/{$room->id}");

        $response->assertRedirect();
        $this->assertDatabaseHas('rooms', ['id' => $room->id, 'deleted_at' => null]);
    }

    public function test_room_status_visualisation_shows_correct_color_mapping(): void
    {
        $room = $this->makeRoom(['status' => 'maintenance']);

        $this->assertEquals('danger', $room->statusColor());
    }
}
