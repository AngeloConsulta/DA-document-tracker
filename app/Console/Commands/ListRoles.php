<?php

namespace App\Console\Commands;

use App\Models\Role;
use Illuminate\Console\Command;

class ListRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all available roles in the system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $roles = Role::where('is_active', true)->get();

        if ($roles->isEmpty()) {
            $this->warn('No active roles found in the system.');
            return;
        }

        $this->info('Available Roles:');
        $this->newLine();

        $headers = ['ID', 'Name', 'Slug', 'Description', 'Permissions Count'];
        $rows = [];

        foreach ($roles as $role) {
            $permissionsCount = is_array($role->permissions) ? count($role->permissions) : 0;
            
            $rows[] = [
                $role->id,
                $role->name,
                $role->slug,
                $role->description,
                $permissionsCount
            ];
        }

        $this->table($headers, $rows);

        // Show detailed permissions for each role
        $this->newLine();
        $this->info('Detailed Permissions:');
        $this->newLine();

        foreach ($roles as $role) {
            $this->line("<fg=yellow>{$role->name} ({$role->slug}):</>");
            $this->line("  {$role->description}");
            
            if (is_array($role->permissions)) {
                if (in_array('*', $role->permissions)) {
                    $this->line("  <fg=green>Permissions: All permissions (*)</>");
                } else {
                    $this->line("  <fg=green>Permissions:</>");
                    foreach ($role->permissions as $permission) {
                        $this->line("    - {$permission}");
                    }
                }
            } else {
                $this->line("  <fg=red>No permissions defined</>");
            }
            
            $this->newLine();
        }
    }
} 