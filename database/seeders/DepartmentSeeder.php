<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Office of the Regional Director',
                'code' => 'ORED',
                'description' => 'Office responsible for overall regional leadership and strategic direction',
                'head_name' => 'Regional Director',
                'is_active' => true,
            ],
            [
                'name' => 'Office of the Regional Technical Director in Operation',
                'code' => 'ORTDO',
                'description' => 'Office responsible for regional technical operations and implementation',
                'head_name' => 'Regional Technical Director in Operation',
                'is_active' => true,
            ],
            [
                'name' => 'Office of the Regional Technical Director in Research and Regulation',
                'code' => 'ORTDRR',
                'description' => 'Office responsible for regional research initiatives and regulatory compliance',
                'head_name' => 'Regional Technical Director in Research and Regulation',
                'is_active' => true,
            ],
            [
                'name' => 'Admin Finance Division',
                'code' => 'AFD',
                'description' => 'Division responsible for administrative and financial management',
                'head_name' => 'Admin Finance Division Head',
                'is_active' => true,
            ],
            [
                'name' => 'Human Resources and Management Section',
                'code' => 'HRMS',
                'description' => 'Section responsible for human resources management and personnel administration',
                'head_name' => 'HRMS Section Head',
                'is_active' => true,
            ],
            [
                'name' => 'Agribusiness and Marketing Assistance Division',
                'code' => 'AMAD',
                'description' => 'Division responsible for agribusiness development and marketing support',
                'head_name' => 'AMAD Division Head',
                'is_active' => true,
            ],
        ];

        foreach ($departments as $department) {
            Department::updateOrCreate(
                ['code' => $department['code']],
                $department
            );
        }

        $this->command->info('Departments seeded successfully!');
        $this->command->info('Created departments: ORED, ORTDO, ORTDRR, AFD, HRMS, AMAD');
    }
} 