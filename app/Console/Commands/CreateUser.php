<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create {--name=} {--email=} {--password=} {--role=} {--department=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user with a specific role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get available roles
        $roles = Role::where('is_active', true)->get();
        $departments = Department::where('is_active', true)->get();

        if ($roles->isEmpty()) {
            $this->error('No roles found. Please run the RoleSeeder first.');
            return 1;
        }

        // Get user input
        $name = $this->option('name') ?: $this->ask('Enter user name');
        $email = $this->option('email') ?: $this->ask('Enter user email');
        $password = $this->option('password') ?: $this->secret('Enter user password');
        $confirmPassword = $this->secret('Confirm user password');

        // Validate password confirmation
        if ($password !== $confirmPassword) {
            $this->error('Passwords do not match!');
            return 1;
        }

        // Validate email format
        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email|unique:users,email'
        ]);

        if ($validator->fails()) {
            $this->error('Invalid email or email already exists!');
            return 1;
        }

        // Select role
        $roleOptions = $roles->pluck('name', 'slug')->toArray();
        $roleSlug = $this->option('role') ?: $this->choice('Select user role', $roleOptions);
        $role = $roles->where('slug', $roleSlug)->first();

        if (!$role) {
            $this->error('Invalid role selected!');
            return 1;
        }

        // Select department (optional)
        $departmentId = null;
        if (!$departments->isEmpty()) {
            $departmentOptions = $departments->pluck('name', 'id')->toArray();
            $departmentOptions[0] = 'No Department';
            
            $selectedDepartment = $this->option('department') ?: $this->choice('Select department (optional)', $departmentOptions);
            
            if ($selectedDepartment !== 'No Department') {
                $departmentId = $selectedDepartment;
            }
        }

        // Create user
        try {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role_id' => $role->id,
                'department_id' => $departmentId,
                'is_active' => true,
            ]);

            $this->info('User created successfully!');
            $this->table(['Field', 'Value'], [
                ['Name', $user->name],
                ['Email', $user->email],
                ['Role', $role->name],
                ['Department', $user->department ? $user->department->name : 'None'],
                ['Status', $user->is_active ? 'Active' : 'Inactive'],
            ]);

        } catch (\Exception $e) {
            $this->error('Failed to create user: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
} 