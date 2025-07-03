<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call our custom seeders in proper order
        $this->call([
            RoleSeeder::class,
            DocumentTypeSeeder::class,
            DocumentSubTypeSeeder::class,
            DepartmentSeeder::class,
            DocumentStatusSeeder::class,
            DocumentSeeder::class,
            DocumentHistorySeeder::class,
            UserSeeder::class,
        ]);

        $this->command->info('Database seeded successfully with QR code enabled document tracker!');
        $this->command->info('Note: Users and departments have been removed from seeding.');
        $this->command->info('You will need to create users and departments manually or through your application.');
        $this->command->info('Available roles: Superadmin, Admin, Department User');
    }
}
