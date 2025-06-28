<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Document;
use App\Models\Role;
use App\Models\Department;
use App\Models\DocumentType;
use App\Models\DocumentStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class DocumentAccessTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $superadmin;
    protected $admin;
    protected $departmentUser;
    protected $department1;
    protected $department2;
    protected $document1;
    protected $document2;

    protected function setUp(): void
    {
        parent::setUp();

        // Create departments
        $this->department1 = Department::factory()->create(['name' => 'IT Department']);
        $this->department2 = Department::factory()->create(['name' => 'HR Department']);

        // Get existing roles or create them if they don't exist
        $superadminRole = Role::where('slug', 'superadmin')->first();
        if (!$superadminRole) {
            $superadminRole = Role::factory()->create([
                'name' => 'Superadmin',
                'slug' => 'superadmin',
                'permissions' => ['*']
            ]);
        }

        $adminRole = Role::where('slug', 'admin')->first();
        if (!$adminRole) {
            $adminRole = Role::factory()->create([
                'name' => 'Admin',
                'slug' => 'admin',
                'permissions' => ['documents.view', 'documents.create', 'documents.edit', 'documents.delete']
            ]);
        }

        $departmentUserRole = Role::where('slug', 'department-user')->first();
        if (!$departmentUserRole) {
            $departmentUserRole = Role::factory()->create([
                'name' => 'Department User',
                'slug' => 'department-user',
                'permissions' => ['documents.view', 'documents.create', 'documents.edit']
            ]);
        }

        // Create users
        $this->superadmin = User::factory()->create([
            'role_id' => $superadminRole->id,
            'department_id' => $this->department1->id
        ]);

        $this->admin = User::factory()->create([
            'role_id' => $adminRole->id,
            'department_id' => $this->department1->id
        ]);

        $this->departmentUser = User::factory()->create([
            'role_id' => $departmentUserRole->id,
            'department_id' => $this->department2->id
        ]);

        // Create document types and statuses
        $documentType = DocumentType::factory()->create();
        $documentStatus = DocumentStatus::factory()->create();

        // Create documents
        $this->document1 = Document::factory()->create([
            'department_id' => $this->department1->id,
            'document_type_id' => $documentType->id,
            'status_id' => $documentStatus->id,
            'created_by' => $this->superadmin->id
        ]);

        $this->document2 = Document::factory()->create([
            'department_id' => $this->department2->id,
            'document_type_id' => $documentType->id,
            'status_id' => $documentStatus->id,
            'created_by' => $this->departmentUser->id
        ]);
    }

    /** @test */
    public function superadmin_can_view_all_documents()
    {
        $this->actingAs($this->superadmin);

        $response = $this->get(route('documents.index'));

        $response->assertStatus(200);
        $response->assertSee($this->document1->title);
        $response->assertSee($this->document2->title);
    }

    /** @test */
    public function admin_can_only_view_documents_from_their_department()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('documents.index'));

        $response->assertStatus(200);
        $response->assertSee($this->document1->title);
        $response->assertDontSee($this->document2->title);
    }

    /** @test */
    public function department_user_can_only_view_documents_from_their_department()
    {
        $this->actingAs($this->departmentUser);

        $response = $this->get(route('documents.index'));

        $response->assertStatus(200);
        $response->assertDontSee($this->document1->title);
        $response->assertSee($this->document2->title);
    }

    /** @test */
    public function superadmin_can_view_any_document()
    {
        $this->actingAs($this->superadmin);

        $response = $this->get(route('documents.show', $this->document1));
        $response->assertStatus(200);

        $response = $this->get(route('documents.show', $this->document2));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_cannot_view_document_from_different_department()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('documents.show', $this->document2));
        $response->assertStatus(403);
    }

    /** @test */
    public function department_user_cannot_view_document_from_different_department()
    {
        $this->actingAs($this->departmentUser);

        $response = $this->get(route('documents.show', $this->document1));
        $response->assertStatus(403);
    }

    /** @test */
    public function superadmin_can_edit_any_document()
    {
        $this->actingAs($this->superadmin);

        $response = $this->get(route('documents.edit', $this->document1));
        $response->assertStatus(200);

        $response = $this->get(route('documents.edit', $this->document2));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_cannot_edit_document_from_different_department()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('documents.edit', $this->document2));
        $response->assertStatus(403);
    }

    /** @test */
    public function department_user_cannot_edit_document_from_different_department()
    {
        $this->actingAs($this->departmentUser);

        $response = $this->get(route('documents.edit', $this->document1));
        $response->assertStatus(403);
    }

    /** @test */
    public function superadmin_can_delete_any_document()
    {
        $this->actingAs($this->superadmin);

        $response = $this->delete(route('documents.destroy', $this->document1));
        $response->assertRedirect(route('documents.index'));

        $this->assertSoftDeleted($this->document1);
    }

    /** @test */
    public function admin_cannot_delete_document_from_different_department()
    {
        $this->actingAs($this->admin);

        $response = $this->delete(route('documents.destroy', $this->document2));
        $response->assertStatus(403);

        $this->assertDatabaseHas('documents', ['id' => $this->document2->id]);
    }

    /** @test */
    public function superadmin_sees_all_departments_in_create_form()
    {
        $this->actingAs($this->superadmin);

        $response = $this->get(route('documents.create'));

        $response->assertStatus(200);
        $response->assertSee($this->department1->name);
        $response->assertSee($this->department2->name);
    }

    /** @test */
    public function admin_sees_only_their_department_in_create_form()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('documents.create'));

        $response->assertStatus(200);
        $response->assertSee($this->department1->name);
        $response->assertDontSee($this->department2->name);
    }

    /** @test */
    public function department_user_sees_only_their_department_in_create_form()
    {
        $this->actingAs($this->departmentUser);

        $response = $this->get(route('documents.create'));

        $response->assertStatus(200);
        $response->assertDontSee($this->department1->name);
        $response->assertSee($this->department2->name);
    }

    /** @test */
    public function superadmin_can_create_document_for_any_department()
    {
        $this->actingAs($this->superadmin);

        $documentType = DocumentType::first();
        $documentStatus = DocumentStatus::first();

        $response = $this->post(route('documents.store'), [
            'title' => 'Test Document',
            'description' => 'Test Description',
            'document_type_id' => $documentType->id,
            'department_id' => $this->department2->id,
            'status_id' => $documentStatus->id,
            'date_received' => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('documents', [
            'title' => 'Test Document',
            'department_id' => $this->department2->id
        ]);
    }

    /** @test */
    public function admin_cannot_create_document_for_different_department()
    {
        $this->actingAs($this->admin);

        $documentType = DocumentType::first();
        $documentStatus = DocumentStatus::first();

        $response = $this->post(route('documents.store'), [
            'title' => 'Test Document',
            'description' => 'Test Description',
            'document_type_id' => $documentType->id,
            'department_id' => $this->department2->id, // Different department
            'status_id' => $documentStatus->id,
            'date_received' => now()->format('Y-m-d'),
        ]);

        // The system should automatically set the department to the user's department
        $this->assertDatabaseHas('documents', [
            'title' => 'Test Document',
            'department_id' => $this->department1->id // Should be admin's department
        ]);
    }
} 