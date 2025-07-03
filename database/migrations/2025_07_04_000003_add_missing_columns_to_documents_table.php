<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'document_type_id')) {
                $table->foreignId('document_type_id')->constrained('document_types');
            }
            if (!Schema::hasColumn('documents', 'status_id')) {
                $table->foreignId('status_id')->constrained('document_statuses');
            }
            if (!Schema::hasColumn('documents', 'department_id')) {
                $table->foreignId('department_id')->constrained('departments');
            }
            if (!Schema::hasColumn('documents', 'source')) {
                $table->string('source')->nullable();
            }
            if (!Schema::hasColumn('documents', 'received_at')) {
                $table->timestamp('received_at')->nullable();
            }
            if (!Schema::hasColumn('documents', 'sent_at')) {
                $table->timestamp('sent_at')->nullable();
            }
            if (!Schema::hasColumn('documents', 'created_by')) {
                $table->foreignId('created_by')->constrained('users');
            }
            if (!Schema::hasColumn('documents', 'current_assignee')) {
                $table->foreignId('current_assignee')->nullable()->constrained('users');
            }
            if (!Schema::hasColumn('documents', 'file_path')) {
                $table->string('file_path')->nullable();
            }
            if (!Schema::hasColumn('documents', 'metadata')) {
                $table->json('metadata')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            if (Schema::hasColumn('documents', 'document_type_id')) {
                $table->dropForeign(['document_type_id']);
                $table->dropColumn('document_type_id');
            }
            if (Schema::hasColumn('documents', 'status_id')) {
                $table->dropForeign(['status_id']);
                $table->dropColumn('status_id');
            }
            if (Schema::hasColumn('documents', 'department_id')) {
                $table->dropForeign(['department_id']);
                $table->dropColumn('department_id');
            }
            if (Schema::hasColumn('documents', 'source')) {
                $table->dropColumn('source');
            }
            if (Schema::hasColumn('documents', 'received_at')) {
                $table->dropColumn('received_at');
            }
            if (Schema::hasColumn('documents', 'sent_at')) {
                $table->dropColumn('sent_at');
            }
            if (Schema::hasColumn('documents', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }
            if (Schema::hasColumn('documents', 'current_assignee')) {
                $table->dropForeign(['current_assignee']);
                $table->dropColumn('current_assignee');
            }
            if (Schema::hasColumn('documents', 'file_path')) {
                $table->dropColumn('file_path');
            }
            if (Schema::hasColumn('documents', 'metadata')) {
                $table->dropColumn('metadata');
            }
        });
    }
}; 