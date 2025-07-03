<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Superadmin',
                'slug' => 'superadmin',
                'description' => 'Has full system access and control. Can manage all aspects of the system including users, departments, documents, and system settings.',
                'permissions' => [
                    '*', // All permissions
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Can manage users, departments, and documents within their scope. Has administrative privileges but not full system control.',
                'permissions' => [
                    'users.view',
                    'users.create',
                    'users.edit',
                    'users.delete',
                    'departments.view',
                    'departments.create',
                    'departments.edit',
                    'departments.delete',
                    'documents.view',
                    'documents.create',
                    'documents.edit',
                    'documents.delete',
                    'documents.approve',
                    'documents.reject',
                    'statistics.view',
                    'reports.view',
                    'reports.generate',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Department User',
                'slug' => 'department-user',
                'description' => 'Regular user from a department. Can create, view, and edit documents within their department scope.',
                'permissions' => [
                    'documents.view',
                    'documents.create',
                    'documents.edit',
                    'documents.submit',
                    'documents.track',
                    'profile.view',
                    'profile.edit',
                ],
                'is_active' => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                [
                    'name' => $role['name'],
                    'description' => $role['description'],
                    'permissions' => $role['permissions'],
                    'is_active' => $role['is_active'],
                ]
            );
        }

        $this->command->info('Roles seeded successfully!');
        $this->command->info('Created roles: Superadmin, Admin, Department User');
    }
} 