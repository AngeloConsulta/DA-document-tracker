<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('permissions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Insert default roles
        DB::table('roles')->insert([
            [
                'name' => 'Superadmin',
                'slug' => 'superadmin',
                'description' => 'Has full system access and control',
                'permissions' => json_encode(['*']),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Can manage users and view statistics',
                'permissions' => json_encode([
                    'users.view',
                    'users.create',
                    'users.edit',
                    'users.delete',
                    'documents.view',
                    'documents.create',
                    'documents.edit',
                    'documents.delete',
                    'statistics.view',
                    'departments.view'
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Department User',
                'slug' => 'department-user',
                'description' => 'Regular user from a department',
                'permissions' => json_encode([
                    'documents.view',
                    'documents.create',
                    'documents.edit'
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
}; 