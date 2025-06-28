<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\DocumentStatus;
use App\Models\DocumentType;
use App\Models\Department;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QRScannerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles first
        $this->createRoles();
        
        // Create test data
        $this->user = User::factory()->create();
        $this->documentType = DocumentType::factory()->create();
        $this->department = Department::factory()->create();
        $this->status = DocumentStatus::factory()->create(['is_active' => true]);
    }

    private function createRoles(): void
    {
        $roles = [
            [
                'name' => 'Superadmin',
                'slug' => 'superadmin',
                'description' => 'Has full system access and control.',
                'permissions' => ['*'],
                'is_active' => true,
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Can manage users, departments, and documents within their scope.',
                'permissions' => [
                    'users.view', 'users.create', 'users.edit', 'users.delete',
                    'departments.view', 'departments.create', 'departments.edit', 'departments.delete',
                    'documents.view', 'documents.create', 'documents.edit', 'documents.delete',
                    'documents.approve', 'documents.reject', 'statistics.view', 'reports.view', 'reports.generate',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Department User',
                'slug' => 'department-user',
                'description' => 'Regular user from a department.',
                'permissions' => [
                    'documents.view', 'documents.create', 'documents.edit', 'documents.submit',
                    'documents.track', 'profile.view', 'profile.edit',
                ],
                'is_active' => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                [
                    'name' => $role['name'],
                    'description' => $role['description'],
                    'permissions' => json_encode($role['permissions']),
                    'is_active' => $role['is_active'],
                ]
            );
        }
    }

    /** @test */
    public function user_can_access_scanner_page()
    {
        $response = $this->actingAs($this->user)
            ->get(route('scanner.index'));

        $response->assertStatus(200);
        $response->assertViewIs('scanner.index');
        $response->assertSee('Scan Document QR Code');
    }

    /** @test */
    public function scanner_can_detect_valid_qr_code()
    {
        $document = Document::factory()->create([
            'qr_code' => 'TEST-QR-CODE-123',
            'document_type_id' => $this->documentType->id,
            'department_id' => $this->department->id,
            'status_id' => $this->status->id,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson(route('scanner.scan'), [
                'qr_code' => 'TEST-QR-CODE-123'
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Document found successfully'
        ]);
        $response->assertJsonStructure([
            'success',
            'document' => [
                'id',
                'qr_code',
                'document_type',
                'department',
                'status'
            ]
        ]);
    }

    /** @test */
    public function scanner_can_detect_document_by_qr_code_path()
    {
        $document = Document::factory()->create([
            'qr_code' => null,
            'qr_code_path' => 'documents/qr-codes/document-456.png',
            'document_type_id' => $this->documentType->id,
            'department_id' => $this->department->id,
            'status_id' => $this->status->id,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson(route('scanner.scan'), [
                'qr_code' => 'documents/qr-codes/document-456.png'
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Document found successfully'
        ]);
        $response->assertJsonStructure([
            'success',
            'document' => [
                'id',
                'qr_code_path',
                'document_type',
                'department',
                'status'
            ]
        ]);
    }

    /** @test */
    public function scanner_prioritizes_qr_code_over_qr_code_path()
    {
        // Create a document with both qr_code and qr_code_path
        $document = Document::factory()->create([
            'qr_code' => 'PRIORITY-QR-CODE',
            'qr_code_path' => 'PRIORITY-QR-CODE', // Same value
            'document_type_id' => $this->documentType->id,
            'department_id' => $this->department->id,
            'status_id' => $this->status->id,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson(route('scanner.scan'), [
                'qr_code' => 'PRIORITY-QR-CODE'
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Document found successfully'
        ]);

        // Verify the document was found
        $this->assertEquals($document->id, $response->json('document.id'));
    }

    /** @test */
    public function scanner_returns_error_for_invalid_qr_code()
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('scanner.scan'), [
                'qr_code' => 'INVALID-QR-CODE'
            ]);

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Document not found',
            'error_code' => 'DOCUMENT_NOT_FOUND'
        ]);
    }

    /** @test */
    public function scanner_validates_qr_code_input()
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('scanner.scan'), [
                'qr_code' => ''
            ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function user_can_update_document_status_after_scanning()
    {
        // Create a user with admin role and assign to the same department as the document
        $adminRole = Role::where('slug', 'admin')->first();
        $user = User::factory()->create([
            'role_id' => $adminRole->id,
            'department_id' => $this->department->id,
        ]);

        $document = Document::factory()->create([
            'qr_code' => 'TEST-QR-CODE-456',
            'document_type_id' => $this->documentType->id,
            'department_id' => $this->department->id, // Same department as user
            'status_id' => $this->status->id,
        ]);

        $newStatus = DocumentStatus::factory()->create(['is_active' => true]);

        $response = $this->actingAs($user)
            ->postJson(route('scanner.update-status', $document), [
                'status_id' => $newStatus->id,
                'remarks' => 'Status updated via scanner'
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);

        // Verify document status was updated
        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'status_id' => $newStatus->id
        ]);

        // Verify history was created
        $this->assertDatabaseHas('document_histories', [
            'document_id' => $document->id,
            'user_id' => $user->id,
            'from_status_id' => $this->status->id,
            'to_status_id' => $newStatus->id,
            'remarks' => 'Status updated via scanner',
            'action_type' => 'status_update'
        ]);
    }

    /** @test */
    public function superadmin_can_update_document_status_from_any_department()
    {
        // Create a superadmin user
        $superadminRole = Role::where('slug', 'superadmin')->first();
        $superadmin = User::factory()->create([
            'role_id' => $superadminRole->id,
        ]);

        // Create document in a different department
        $differentDepartment = Department::factory()->create();
        $document = Document::factory()->create([
            'qr_code' => 'TEST-QR-CODE-789',
            'document_type_id' => $this->documentType->id,
            'department_id' => $differentDepartment->id,
            'status_id' => $this->status->id,
        ]);

        $newStatus = DocumentStatus::factory()->create(['is_active' => true]);

        $response = $this->actingAs($superadmin)
            ->postJson(route('scanner.update-status', $document), [
                'status_id' => $newStatus->id,
                'remarks' => 'Status updated by superadmin'
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }

    /** @test */
    public function department_user_cannot_update_document_from_different_department()
    {
        // Create a department user
        $deptUserRole = Role::where('slug', 'department-user')->first();
        $deptUser = User::factory()->create([
            'role_id' => $deptUserRole->id,
            'department_id' => $this->department->id,
        ]);

        // Create document in a different department
        $differentDepartment = Department::factory()->create();
        $document = Document::factory()->create([
            'qr_code' => 'TEST-QR-CODE-999',
            'document_type_id' => $this->documentType->id,
            'department_id' => $differentDepartment->id,
            'status_id' => $this->status->id,
        ]);

        $newStatus = DocumentStatus::factory()->create(['is_active' => true]);

        $response = $this->actingAs($deptUser)
            ->postJson(route('scanner.update-status', $document), [
                'status_id' => $newStatus->id,
                'remarks' => 'Status update attempt'
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function scanner_can_fetch_available_statuses()
    {
        // Create multiple statuses
        DocumentStatus::factory()->count(3)->create(['is_active' => true]);
        DocumentStatus::factory()->create(['is_active' => false]); // Should not be included

        $response = $this->actingAs($this->user)
            ->getJson(route('scanner.statuses'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);
        $response->assertJsonStructure([
            'success',
            'statuses' => [
                '*' => ['id', 'name', 'description']
            ]
        ]);

        // Should only return active statuses
        $this->assertEquals(4, count($response->json('statuses'))); // 3 new + 1 from setUp
    }

    /** @test */
    public function scanner_requires_authentication()
    {
        $response = $this->get(route('scanner.index'));
        $response->assertRedirect(route('login'));

        $response = $this->postJson(route('scanner.scan'), [
            'qr_code' => 'TEST-QR-CODE'
        ]);
        $response->assertStatus(401);
    }

    /** @test */
    public function scanner_can_find_document_with_different_qr_code_and_path()
    {
        // Create a document with different values for qr_code and qr_code_path
        $document = Document::factory()->create([
            'qr_code' => 'QR-CODE-VALUE',
            'qr_code_path' => 'path/to/qr-code-image.png',
            'document_type_id' => $this->documentType->id,
            'department_id' => $this->department->id,
            'status_id' => $this->status->id,
        ]);

        // Test finding by qr_code
        $response1 = $this->actingAs($this->user)
            ->postJson(route('scanner.scan'), [
                'qr_code' => 'QR-CODE-VALUE'
            ]);

        $response1->assertStatus(200);
        $response1->assertJson([
            'success' => true,
            'message' => 'Document found successfully'
        ]);

        // Test finding by qr_code_path
        $response2 = $this->actingAs($this->user)
            ->postJson(route('scanner.scan'), [
                'qr_code' => 'path/to/qr-code-image.png'
            ]);

        $response2->assertStatus(200);
        $response2->assertJson([
            'success' => true,
            'message' => 'Document found successfully'
        ]);

        // Both should return the same document
        $this->assertEquals($document->id, $response1->json('document.id'));
        $this->assertEquals($document->id, $response2->json('document.id'));
    }
} 