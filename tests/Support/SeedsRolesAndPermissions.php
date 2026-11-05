<?php

namespace Tests\Support;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

trait SeedsRolesAndPermissions
{
    protected function seedRoles(): void
    {
        $this->seed(RolePermissionSeeder::class);
    }

    protected function makeUser(string $role, array $attributes = []): User
    {
        $user = User::factory()->create(array_merge(['status' => 'active'], $attributes));
        $user->assignRole($role);

        return $user;
    }
}
