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
                'name' => 'ORED',
                'code' => 'ORED',
                'description' => 'Office of the Regional Director',
                'head_name' => 'Rodel P. Tornilla, MABE',
                'is_active' => true,
            ],
            [
                'name' => 'ORTDO',
                'code' => 'ORTDO',
                'description' => 'Office of the Regional Technical Director for Operations',
                'head_name' => 'Mary Grace Dp. Rodriguez, Ph.D.',
                'is_active' => true,
            ],
            [
                'name' => 'ORTDRR',
                'code' => 'ORTDRR',
                'description' => 'Office of the Regional Technical Director for Research and Regulations',
                'head_name' => 'Lorenzo L. Alvina',
                'is_active' => true,
            ],
            [
                'name' => 'PMED',
                'code' => 'PMED',
                'description' => 'Planning, Monitoring and Evaluation Division',
                'head_name' => 'Engr. Teodoro C. Eleda',
                'is_active' => true,
            ],
            [
                'name' => 'AMAD',
                'code' => 'AMAD',
                'description' => 'Agribusiness & Marketing Assistance Division',
                'head_name' => 'Engr. Luisito Baltazar',
                'is_active' => true,
            ],
            [
                'name' => 'AFD',
                'code' => 'AFD',
                'description' => 'Administrative and Finance Division',
                'head_name' => 'Imelda P. Acompañado',
                'is_active' => true,
            ],
            [
                'name' => 'FOD',
                'code' => 'FOD',
                'description' => 'Field Operations Division',
                'head_name' => 'Earl Vincent P. Vegas',
                'is_active' => true,
            ],
            [
                'name' => 'Research Division',
                'code' => 'RSD',
                'description' => 'Research Division',
                'head_name' => 'Maria Christina F. Campita, Ph.D.',
                'is_active' => true,
            ],
            [
                'name' => 'Regulatory Division',
                'code' => 'RGD',
                'description' => 'Regulatory Division',
                'head_name' => 'Dr. Marissa N. Guillermo',
                'is_active' => true,
            ],
            [
                'name' => 'ILD',
                'code' => 'ILD',
                'description' => 'Integrated Laboratories Division',
                'head_name' => 'Anacleto B. Esplana',
                'is_active' => true,
            ],
            [
                'name' => 'RAED',
                'code' => 'RAED',
                'description' => 'Regional Agricultural Engineering Division',
                'head_name' => 'Engr. Jerry Eboña',
                'is_active' => true,
            ],
            [
                'name' => 'HRMS',
                'code' => 'HR',
                'description' => 'Human resource Management Section',
                'head_name' => 'Janet M. Pasamba',
                'is_active' => true,
            ],
            [
                'name' => 'Accounting',
                'code' => 'ACNT',
                'description' => 'Accounting Section',
                'head_name' => 'Lorraine Sermonia',
                'is_active' => true,
            ],
            [
                'name' => 'Budget',
                'code' => 'BGT',
                'description' => 'Budget Section',
                'head_name' => 'Michelle A. Sabido',
                'is_active' => true,
            ],
            [
                'name' => 'Cash',
                'code' => 'CSH',
                'description' => 'Cash Unit',
                'head_name' => 'Nelia A. Bustarga',
                'is_active' => true,
            ],
            [
                'name' => 'RAFIS',
                'code' => 'RAFIS',
                'description' => 'Regional Agriculture and Fisheries Information Section',
                'head_name' => 'Lovella P. Guarin',
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
        $this->command->info('Created departments: ORED, ORTDO, ORTDRR, PMED, AMAD, AFD, FOD, RSD, RGD, ILD, RAED, HRMS, ACNT, BGT, CSH, RAFIS');
    }
} 