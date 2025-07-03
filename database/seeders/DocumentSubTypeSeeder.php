<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentType;
use App\Models\DocumentSubType;

class DocumentSubTypeSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Communication' => ['Letter', 'Memorandum', 'Special Order'],
            'Voucher' => ['Salary', 'Total Enterprise Value', 'Catering', 'Lodging', 'Supplies', 'Van Rental'],
            'TOTP' => ['Travel Order', 'Travel Pass', 'Daily Time Record', 'Application of Leave'],
            'Proposal' => ['Training', 'Meeting', 'Activity'],
        ];
        foreach ($data as $type => $subTypes) {
            $typeModel = DocumentType::where('name', $type)->first();
            if ($typeModel) {
                foreach ($subTypes as $subType) {
                    DocumentSubType::firstOrCreate([
                        'name' => $subType,
                        'document_type_id' => $typeModel->id,
                    ]);
                }
            }
        }
    }
} 