<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentType;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Communication', 'code' => 'COMM'],
            ['name' => 'Voucher', 'code' => 'VCHR'],
            ['name' => 'TOTP', 'code' => 'TOTP'],
            ['name' => 'Proposal', 'code' => 'PROP'],
        ];
        foreach ($types as $type) {
            DocumentType::firstOrCreate(['name' => $type['name']], $type);
        }
    }
} 