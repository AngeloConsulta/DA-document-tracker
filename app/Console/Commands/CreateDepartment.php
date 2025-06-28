<?php

namespace App\Console\Commands;

use App\Models\Department;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateDepartment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'department:create {--name=} {--code=} {--description=} {--head=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new department';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get user input
        $name = $this->option('name') ?: $this->ask('Enter department name');
        $code = $this->option('code') ?: $this->ask('Enter department code (e.g., HR, IT, FIN)');
        $description = $this->option('description') ?: $this->ask('Enter department description (optional)');
        $headName = $this->option('head') ?: $this->ask('Enter department head name (optional)');

        // Validate input
        $validator = Validator::make([
            'name' => $name,
            'code' => $code,
        ], [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:departments,code',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return 1;
        }

        // Create department
        try {
            $department = Department::create([
                'name' => $name,
                'code' => strtoupper($code),
                'description' => $description,
                'head_name' => $headName,
                'is_active' => true,
            ]);

            $this->info('Department created successfully!');
            $this->table(['Field', 'Value'], [
                ['Name', $department->name],
                ['Code', $department->code],
                ['Description', $department->description ?: 'None'],
                ['Head', $department->head_name ?: 'None'],
                ['Status', $department->is_active ? 'Active' : 'Inactive'],
            ]);

        } catch (\Exception $e) {
            $this->error('Failed to create department: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
} 