<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $documentTypes = [
            [
                'name' => 'Letter',
                'code' => 'LTR',
                'description' => 'Formal written communication with external parties',
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Travel Order',
                'code' => 'TRO',
                'description' => 'Official authorization document for business travel',
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Withdrawal',
                'code' => 'WTH',
                'description' => 'Document for withdrawal of funds or resources',
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Travel Pass',
                'code' => 'TRP',
                'description' => 'Authorization document for travel permissions',
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'RIS',
                'code' => 'RIS',
                'description' => 'Requisition and Issue Slip for inventory and supplies',
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Trip Ticket',
                'code' => 'TRT',
                'description' => 'Document for vehicle or transportation authorization',
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Memorandum',
                'code' => 'MEMO',
                'description' => 'Internal communication document for official announcements and directives',
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Application for Leave',
                'code' => 'AFL',
                'description' => 'Formal request for employee leave or time off',
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Special Order',
                'code' => 'SPO',
                'description' => 'Official order for special assignments or duties',
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Itinerary of Travel',
                'code' => 'IOT',
                'description' => 'Detailed travel schedule and route documentation',
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Gate Pass',
                'code' => 'GTP',
                'description' => 'Authorization document for facility access',
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Purchase Request',
                'code' => 'PR',
                'description' => 'Formal request for procurement of goods or services',
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Activity Proposal',
                'code' => 'ACP',
                'description' => 'Proposal document for planned activities or events',
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Certificate of Registration',
                'code' => 'COR',
                'description' => 'Official registration certificate document',
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Endorsement Letter',
                'code' => 'END',
                'description' => 'Letter of endorsement or recommendation',
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'ORS',
                'code' => 'ORS',
                'description' => 'Obligation Request and Status document',
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Request Letter',
                'code' => 'RQL',
                'description' => 'Formal letter requesting specific actions or information',
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'License to Operate',
                'code' => 'LTO',
                'description' => 'Official license document for business operations',
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'TEV',
                'code' => 'TEV',
                'description' => 'Travel Expense Voucher for reimbursement',
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Job Order Contract',
                'code' => 'JOC',
                'description' => 'Contract document for job order agreements',
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Accomplishment Report',
                'code' => 'ACR',
                'description' => 'Report documenting completed tasks and achievements',
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Request for Quotation',
                'code' => 'RFQ',
                'description' => 'Document requesting price quotes from suppliers',
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Handlers',
                'code' => 'HDL',
                'description' => 'Document related to handlers or processing agents',
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'IPCR',
                'code' => 'IPCR',
                'description' => 'Individual Performance Commitment and Review document',
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Invitation Letter',
                'code' => 'INV',
                'description' => 'Formal invitation letter for events or meetings',
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Temporary Certificate of Handlers License & Transport Carrier Registration',
                'code' => 'TCHL',
                'description' => 'Temporary certificate for handlers license and transport carrier registration',
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'DTR',
                'code' => 'DTR',
                'description' => 'Daily Time Record for attendance tracking',
                'requires_approval' => false,
                'is_active' => true,
            ],
            [
                'name' => 'PPMP',
                'code' => 'PPMP',
                'description' => 'Project Procurement Management Plan document',
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Programs of Works',
                'code' => 'POW',
                'description' => 'Document outlining work programs and schedules',
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Temporary Certificate of Transport Carrier Registration',
                'code' => 'TCTR',
                'description' => 'Temporary certificate for transport carrier registration',
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'BAC',
                'code' => 'BAC',
                'description' => 'Bids and Awards Committee document',
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Notice of Inspection',
                'code' => 'NOI',
                'description' => 'Official notice for inspection activities',
                'requires_approval' => true,
                'is_active' => true,
            ],
        ];

        foreach ($documentTypes as $type) {
            DocumentType::updateOrCreate(
                ['code' => $type['code']],
                $type
            );
        }

        $this->command->info('Document types seeded successfully!');
    }
} 