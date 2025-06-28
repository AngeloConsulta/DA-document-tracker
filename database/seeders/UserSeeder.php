<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Get roles
        $superadminRole = Role::where('slug', 'superadmin')->first();
        $adminRole = Role::where('slug', 'admin')->first();

        // Create Superadmin
        User::updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Superadmin',
                'email' => 'superadmin@example.com',
                'password' => Hash::make('password'),
                'role_id' => $superadminRole ? $superadminRole->id : null,
                'department_id' => null,
                'is_active' => true,
            ]
        );

        // Create 5 Admins
        for ($i = 1; $i <= 5; $i++) {
            User::updateOrCreate(
                ['email' => "admin{$i}@example.com"],
                [
                    'name' => "admin{$i}",
                    'email' => "admin{$i}@example.com",
                    'password' => Hash::make('password'),
                    'role_id' => $adminRole ? $adminRole->id : null,
                    'department_id' => null,
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Superadmin and 5 admin users created successfully!');
    }
} 