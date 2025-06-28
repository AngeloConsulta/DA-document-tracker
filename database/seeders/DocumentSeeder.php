<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\DocumentStatus;
use App\Models\Department;
use App\Models\User;
use App\Services\QrCodeService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DocumentSeeder extends Seeder
{
    protected QrCodeService $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    public function run(): void
    {
        // Get all required relationships
        $documentTypes = DocumentType::where('is_active', true)->get();
        $statuses = DocumentStatus::where('is_active', true)->get();
        $departments = Department::where('is_active', true)->get();
        $users = User::all();
        
        // Check if required dependencies exist
        if ($documentTypes->isEmpty()) {
            $this->command->warn('No document types found. Skipping document creation.');
            return;
        }
        
        if ($statuses->isEmpty()) {
            $this->command->warn('No document statuses found. Skipping document creation.');
            return;
        }
        
        if ($departments->isEmpty()) {
            $this->command->warn('No departments found. Skipping document creation.');
            return;
        }
        
        if ($users->isEmpty()) {
            $this->command->warn('No users found. Skipping document creation.');
            return;
        }

        $sampleDocuments = [
            [
                'title' => 'Annual Department Budget Review 2024',
                'description' => 'Comprehensive review of department budget allocations and expenditures for the current fiscal year. Includes detailed analysis of spending patterns and recommendations for next year.',
                'document_type_id' => $documentTypes->where('code', 'RPT')->first()->id,
                'status_id' => $statuses->where('code', 'PENDING')->first()->id,
                'department_id' => $departments->where('code', 'FIN')->first()->id,
                'direction' => 'incoming',
                'date_received' => now()->subDays(5),
                'due_date' => now()->addDays(7),
                'source' => 'Finance Department',
                'received_at' => now()->subDays(5),
                'file_path' => 'documents/budget_review_2024.pdf',
            ],
            [
                'title' => 'IT Infrastructure Upgrade Proposal',
                'description' => 'Strategic proposal for upgrading the organization\'s IT infrastructure to improve efficiency, security, and scalability. Includes cost-benefit analysis and implementation timeline.',
                'document_type_id' => $documentTypes->where('code', 'MEMO')->first()->id,
                'status_id' => $statuses->where('code', 'REVIEW')->first()->id,
                'department_id' => $departments->where('code', 'IT')->first()->id,
                'direction' => 'outgoing',
                'date_received' => now()->subDays(3),
                'due_date' => now()->addDays(14),
                'source' => 'IT Department',
                'received_at' => now()->subDays(3),
                'sent_at' => now()->subDays(2),
                'file_path' => 'documents/it_upgrade_proposal.pdf',
            ],
            [
                'title' => 'New Employee Onboarding Policy v2.1',
                'description' => 'Updated policy document for new employee onboarding procedures. Includes remote work considerations and digital-first approach.',
                'document_type_id' => $documentTypes->where('code', 'POL')->first()->id,
                'status_id' => $statuses->where('code', 'APPROVED')->first()->id,
                'department_id' => $departments->where('code', 'HR')->first()->id,
                'direction' => 'incoming',
                'date_received' => now()->subDays(10),
                'due_date' => now()->addDays(5),
                'source' => 'HR Department',
                'received_at' => now()->subDays(10),
                'file_path' => 'documents/onboarding_policy_v2.1.pdf',
            ],
            [
                'title' => 'Q3 Marketing Campaign Strategy',
                'description' => 'Comprehensive Q3 marketing campaign strategy and implementation plan. Includes digital marketing, social media, and traditional advertising approaches.',
                'document_type_id' => $documentTypes->where('code', 'RPT')->first()->id,
                'status_id' => $statuses->where('code', 'DRAFT')->first()->id,
                'department_id' => $departments->where('code', 'MKT')->first()->id,
                'direction' => 'outgoing',
                'date_received' => now()->subDay(),
                'due_date' => now()->addDays(21),
                'source' => 'Marketing Department',
                'received_at' => now()->subDay(),
                'sent_at' => now(),
                'file_path' => 'documents/q3_marketing_strategy.pdf',
            ],
            [
                'title' => 'Legal Compliance Review 2024',
                'description' => 'Annual legal compliance review and recommendations. Covers regulatory changes, risk assessment, and compliance improvement strategies.',
                'document_type_id' => $documentTypes->where('code', 'RPT')->first()->id,
                'status_id' => $statuses->where('code', 'COMPLETED')->first()->id,
                'department_id' => $departments->where('code', 'LEG')->first()->id,
                'direction' => 'incoming',
                'date_received' => now()->subDays(15),
                'due_date' => now()->subDays(2),
                'source' => 'Legal Department',
                'received_at' => now()->subDays(15),
                'file_path' => 'documents/legal_compliance_2024.pdf',
            ],
            [
                'title' => 'Research & Development Project Proposal',
                'description' => 'Innovation project proposal for new product development. Includes market research, technical feasibility, and resource requirements.',
                'document_type_id' => $documentTypes->where('code', 'RPT')->first()->id,
                'status_id' => $statuses->where('code', 'PENDING')->first()->id,
                'department_id' => $departments->where('code', 'R&D')->first()->id,
                'direction' => 'outgoing',
                'date_received' => now()->subDays(2),
                'due_date' => now()->addDays(30),
                'source' => 'R&D Department',
                'received_at' => now()->subDays(2),
                'sent_at' => now()->subDay(),
                'file_path' => 'documents/rd_project_proposal.pdf',
            ],
            [
                'title' => 'Operations Manual Update',
                'description' => 'Updated operations manual reflecting new procedures and best practices. Includes workflow diagrams and process improvements.',
                'document_type_id' => $documentTypes->where('code', 'POL')->first()->id,
                'status_id' => $statuses->where('code', 'REVIEW')->first()->id,
                'department_id' => $departments->where('code', 'OPS')->first()->id,
                'direction' => 'incoming',
                'date_received' => now()->subDays(7),
                'due_date' => now()->addDays(10),
                'source' => 'Operations Department',
                'received_at' => now()->subDays(7),
                'file_path' => 'documents/operations_manual_update.pdf',
            ],
        ];

        foreach ($sampleDocuments as $document) {
            // Generate tracking number
            $trackingNumber = 'DOC-' . date('Y') . '-' . Str::random(8);
            $document['tracking_number'] = $trackingNumber;
            
            // Set creator (first user)
            $document['created_by'] = $users->first()->id;
            
            // Set assignee (first user if only one exists, otherwise random different user)
            if ($users->count() === 1) {
                $document['current_assignee'] = $users->first()->id;
            } else {
                $assignee = $users->where('id', '!=', $document['created_by'])->first();
                $document['current_assignee'] = $assignee ? $assignee->id : $users->first()->id;
            }

            // Generate QR code for the document using the service
            $qrCodePath = $this->qrCodeService->generateDocumentQrCode(
                $trackingNumber, 
                $document['title'],
                [
                    'department' => $departments->find($document['department_id'])->name,
                    'status' => $statuses->find($document['status_id'])->name,
                    'type' => $documentTypes->find($document['document_type_id'])->name,
                ]
            );
            $document['qr_code_path'] = $qrCodePath;

            // Add metadata
            $document['metadata'] = json_encode([
                'version' => '1.0',
                'created_at' => now()->toISOString(),
                'qr_generated' => true,
                'file_type' => 'pdf',
                'file_size' => rand(100000, 5000000), // Random file size between 100KB and 5MB
                'security_level' => 'confidential',
                'tags' => ['document', 'tracking', 'qr-enabled'],
                'qr_service_version' => '1.0',
            ]);

            // Create the document
            $doc = Document::create($document);

            // Create initial history
            $doc->histories()->create([
                'user_id' => $document['created_by'],
                'to_status_id' => $document['status_id'],
                'to_department_id' => $document['department_id'],
                'to_user_id' => $document['current_assignee'],
                'remarks' => 'Document created and QR code generated',
                'action_type' => 'created'
            ]);

            $this->command->info("Created document: {$document['title']} with QR code: {$qrCodePath}");
        }

        $this->command->info('All documents created successfully with QR codes!');
    }
} 