<?php

namespace Database\Seeders;

use App\Models\DocumentStatus;
use Illuminate\Database\Seeder;

class DocumentStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'Draft',
                'code' => 'DRAFT',
                'description' => 'Initial version of the document, not yet submitted for review',
                'color' => '#B0B0B0', // Mid Gray
                'is_active' => true,
            ],
            [
                'name' => 'Pending Review',
                'code' => 'PENDING',
                'description' => 'Document is submitted and waiting for initial review',
                'color' => '#FFA500', // Mid Orange
                'is_active' => true,
            ],
            [
                'name' => 'Under Review',
                'code' => 'REVIEW',
                'description' => 'Document is currently being reviewed by assigned personnel',
                'color' => '#42A5F5', // Mid Blue
                'is_active' => true,
            ],
            [
                'name' => 'Under Revision',
                'code' => 'REVISION',
                'description' => 'Document is being revised based on feedback',
                'color' => '#FFD54F', // Mid Yellow
                'is_active' => true,
            ],
            [
                'name' => 'Pending Approval',
                'code' => 'PENDING_APPROVAL',
                'description' => 'Document is ready for final approval',
                'color' => '#AB47BC', // Mid Purple
                'is_active' => true,
            ],
            [
                'name' => 'Approved',
                'code' => 'APPROVED',
                'description' => 'Document has been approved and is ready for implementation',
                'color' => '#43A047', // Mid Green
                'is_active' => true,
            ],
            [
                'name' => 'Rejected',
                'code' => 'REJECTED',
                'description' => 'Document has been rejected and requires major changes',
                'color' => '#E53935', // Mid Red
                'is_active' => true,
            ],
            [
                'name' => 'On Hold',
                'code' => 'ON_HOLD',
                'description' => 'Document processing is temporarily suspended',
                'color' => '#FFB300', // Mid Amber
                'is_active' => true,
            ],
            [
                'name' => 'In Progress',
                'code' => 'IN_PROGRESS',
                'description' => 'Document is actively being processed or implemented',
                'color' => '#26A69A', // Mid Teal
                'is_active' => true,
            ],
            [
                'name' => 'Completed',
                'code' => 'COMPLETED',
                'description' => 'Document process is fully completed',
                'color' => '#3949AB', // Mid Navy
                'is_active' => true,
            ],
            [
                'name' => 'Cancelled',
                'code' => 'CANCELLED',
                'description' => 'Document process has been cancelled',
                'color' => '#8D6E63', // Mid Brown
                'is_active' => true,
            ],
            [
                'name' => 'Archived',
                'code' => 'ARCHIVED',
                'description' => 'Document has been archived for long-term storage',
                'color' => '#5C6BC0', // Mid Indigo
                'is_active' => true,
            ],
            [
                'name' => 'Expired',
                'code' => 'EXPIRED',
                'description' => 'Document has passed its validity period',
                'color' => '#757575', // Mid Gray
                'is_active' => true,
            ],
            [
                'name' => 'Under Investigation',
                'code' => 'INVESTIGATION',
                'description' => 'Document is under investigation or audit',
                'color' => '#D32F2F', // Mid Crimson
                'is_active' => true,
            ],
            [
                'name' => 'Awaiting Signature',
                'code' => 'AWAITING_SIGNATURE',
                'description' => 'Document is waiting for required signatures',
                'color' => '#00ACC1', // Mid Cyan
                'is_active' => true,
            ],
            [
                'name' => 'Forwarded',
                'code' => 'FORWARDED',
                'description' => 'Document has been forwarded to another department/user',
                'color' => '#663399', // Rebecca Purple
                'is_active' => true,
            ],
        ];

        foreach ($statuses as $status) {
            DocumentStatus::updateOrCreate(
                ['code' => $status['code']],
                $status
            );
        }

        $this->command->info('Document statuses seeded successfully!');
    }
} 