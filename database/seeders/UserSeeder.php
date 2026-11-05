<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['name' => 'Super Admin', 'email' => 'admin@kostmanager.test', 'role' => 'Super Admin'],
            ['name' => 'Pemilik Kost', 'email' => 'owner@kostmanager.test', 'role' => 'Owner'],
            ['name' => 'Staff Keuangan', 'email' => 'finance@kostmanager.test', 'role' => 'Finance'],
            ['name' => 'Staff Operasional', 'email' => 'staff@kostmanager.test', 'role' => 'Staff'],
        ];

        foreach ($accounts as $account) {
            $user = User::firstOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'status' => 'active',
                ]
            );

            if (! $user->hasRole($account['role'])) {
                $user->assignRole($account['role']);
            }
        }
    }
}
