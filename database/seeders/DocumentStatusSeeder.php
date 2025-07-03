<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentSubType;
use App\Models\DocumentStatus;

class DocumentStatusSeeder extends Seeder
{
    public function run(): void
    {
        $voucherStatuses = [
            'Pre-process',
            'Funding',
            'Inspection',
            'Approval',
            'Final Process',
            'LDDAP',
        ];
        $voucherSubTypes = [
            'Salary',
            'Total Enterprise Value',
            'Catering',
            'Lodging',
            'Supplies',
            'Van Rental',
        ];
        foreach ($voucherSubTypes as $subTypeName) {
            $subType = DocumentSubType::where('name', $subTypeName)->first();
            if ($subType) {
                foreach ($voucherStatuses as $statusName) {
                    DocumentStatus::firstOrCreate([
                        'name' => $statusName,
                        'document_sub_type_id' => $subType->id,
                    ]);
                }
            }
        }
        // Add more statuses for other subtypes as needed
    }
} 