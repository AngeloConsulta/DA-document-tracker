<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\DocumentHistory;
use App\Models\DocumentStatus;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;

class DocumentHistorySeeder extends Seeder
{
    public function run(): void
    {
        $documents = Document::with(['status', 'department', 'currentAssignee'])->get();
        $statuses = DocumentStatus::where('is_active', true)->get();
        $departments = Department::where('is_active', true)->get();
        $users = User::where('is_active', true)->get();

        if ($documents->isEmpty()) {
            $this->command->info('No documents found. Please run DocumentSeeder first.');
            return;
        }

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Skipping document history creation.');
            return;
        }

        if ($departments->isEmpty()) {
            $this->command->warn('No departments found. Skipping document history creation.');
            return;
        }

        foreach ($documents as $document) {
            // Create additional history entries for each document
            $this->createDocumentHistory($document, $statuses, $departments, $users);
        }

        $this->command->info('Document histories seeded successfully!');
    }

    private function createDocumentHistory($document, $statuses, $departments, $users)
    {
        $historyEntries = [];
        $currentStatus = $document->status;
        $currentDepartment = $document->department;
        $currentAssignee = $document->currentAssignee;

        // Simulate different workflow scenarios based on document type and current status
        switch ($document->status->code) {
            case 'PENDING':
                // Document moves from PENDING to REVIEW
                $historyEntries[] = [
                    'user_id' => $users->random()->id,
                    'document_id' => $document->id,
                    'from_status_id' => $currentStatus->id,
                    'to_status_id' => $statuses->where('code', 'REVIEW')->first()->id,
                    'from_department_id' => $currentDepartment->id,
                    'to_department_id' => $currentDepartment->id,
                    'from_user_id' => $currentAssignee->id,
                    'to_user_id' => $users->where('department_id', $currentDepartment->id)->random()->id,
                    'remarks' => 'Document assigned for review',
                    'action_type' => 'status_change',
                    'created_at' => now()->subDays(2),
                ];
                break;

            case 'REVIEW':
                // Document moves from REVIEW to PENDING_APPROVAL or REVISION
                $nextStatus = rand(0, 1) ? 'PENDING_APPROVAL' : 'REVISION';
                $historyEntries[] = [
                    'user_id' => $users->random()->id,
                    'document_id' => $document->id,
                    'from_status_id' => $currentStatus->id,
                    'to_status_id' => $statuses->where('code', $nextStatus)->first()->id,
                    'from_department_id' => $currentDepartment->id,
                    'to_department_id' => $currentDepartment->id,
                    'from_user_id' => $currentAssignee->id,
                    'to_user_id' => $users->where('department_id', $currentDepartment->id)->random()->id,
                    'remarks' => $nextStatus === 'PENDING_APPROVAL' ? 'Review completed, ready for approval' : 'Revision required based on review feedback',
                    'action_type' => 'status_change',
                    'created_at' => now()->subDays(1),
                ];
                break;

            case 'APPROVED':
                // Document moves from APPROVED to IN_PROGRESS
                $historyEntries[] = [
                    'user_id' => $users->random()->id,
                    'document_id' => $document->id,
                    'from_status_id' => $currentStatus->id,
                    'to_status_id' => $statuses->where('code', 'IN_PROGRESS')->first()->id,
                    'from_department_id' => $currentDepartment->id,
                    'to_department_id' => $currentDepartment->id,
                    'from_user_id' => $currentAssignee->id,
                    'to_user_id' => $currentAssignee->id,
                    'remarks' => 'Document approved, implementation started',
                    'action_type' => 'status_change',
                    'created_at' => now()->subDays(3),
                ];

                // Then to COMPLETED
                $historyEntries[] = [
                    'user_id' => $users->random()->id,
                    'document_id' => $document->id,
                    'from_status_id' => $statuses->where('code', 'IN_PROGRESS')->first()->id,
                    'to_status_id' => $statuses->where('code', 'COMPLETED')->first()->id,
                    'from_department_id' => $currentDepartment->id,
                    'to_department_id' => $currentDepartment->id,
                    'from_user_id' => $currentAssignee->id,
                    'to_user_id' => $currentAssignee->id,
                    'remarks' => 'Document implementation completed successfully',
                    'action_type' => 'status_change',
                    'created_at' => now()->subDay(),
                ];
                break;

            case 'COMPLETED':
                // Document moves to ARCHIVED
                $historyEntries[] = [
                    'user_id' => $users->random()->id,
                    'document_id' => $document->id,
                    'from_status_id' => $currentStatus->id,
                    'to_status_id' => $statuses->where('code', 'ARCHIVED')->first()->id,
                    'from_department_id' => $currentDepartment->id,
                    'to_department_id' => $currentDepartment->id,
                    'from_user_id' => $currentAssignee->id,
                    'to_user_id' => $currentAssignee->id,
                    'remarks' => 'Document archived for long-term storage',
                    'action_type' => 'status_change',
                    'created_at' => now(),
                ];
                break;

            case 'DRAFT':
                // Document moves from DRAFT to PENDING
                $historyEntries[] = [
                    'user_id' => $users->random()->id,
                    'document_id' => $document->id,
                    'from_status_id' => $currentStatus->id,
                    'to_status_id' => $statuses->where('code', 'PENDING')->first()->id,
                    'from_department_id' => $currentDepartment->id,
                    'to_department_id' => $currentDepartment->id,
                    'from_user_id' => $currentAssignee->id,
                    'to_user_id' => $currentAssignee->id,
                    'remarks' => 'Draft submitted for review',
                    'action_type' => 'status_change',
                    'created_at' => now()->subDays(1),
                ];
                break;

            default:
                // For other statuses, create a simple reassignment
                $historyEntries[] = [
                    'user_id' => $users->random()->id,
                    'document_id' => $document->id,
                    'from_status_id' => $currentStatus->id,
                    'to_status_id' => $currentStatus->id,
                    'from_department_id' => $currentDepartment->id,
                    'to_department_id' => $currentDepartment->id,
                    'from_user_id' => $currentAssignee->id,
                    'to_user_id' => $users->where('department_id', $currentDepartment->id)->random()->id,
                    'remarks' => 'Document reassigned to different user',
                    'action_type' => 'reassignment',
                    'created_at' => now()->subDays(1),
                ];
                break;
        }

        // Create additional history entries for cross-department transfers
        if (rand(0, 1)) { // 50% chance
            $newDepartment = $departments->where('id', '!=', $currentDepartment->id)->random();
            $historyEntries[] = [
                'user_id' => $users->random()->id,
                'document_id' => $document->id,
                'from_status_id' => $currentStatus->id,
                'to_status_id' => $currentStatus->id,
                'from_department_id' => $currentDepartment->id,
                'to_department_id' => $newDepartment->id,
                'from_user_id' => $currentAssignee->id,
                'to_user_id' => $users->where('department_id', $newDepartment->id)->random()->id,
                'remarks' => "Document transferred to {$newDepartment->name} department",
                'action_type' => 'transfer',
                'created_at' => now()->subDays(rand(1, 5)),
            ];
        }

        // Create the history entries
        foreach ($historyEntries as $entry) {
            DocumentHistory::create($entry);
        }

        $this->command->info("Created " . count($historyEntries) . " history entries for document: {$document->tracking_number}");
    }
} 