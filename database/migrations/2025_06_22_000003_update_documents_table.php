<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->foreignId('document_sub_type_id')->nullable()->after('id')->constrained('document_sub_types')->onDelete('set null');
            $table->foreignId('document_status_id')->nullable()->after('document_sub_type_id')->constrained('document_statuses')->onDelete('set null');
            // Remove old type/status fields if they exist
            if (Schema::hasColumn('documents', 'document_type_id')) {
                $table->dropForeign(['document_type_id']);
                $table->dropColumn('document_type_id');
            }
            if (Schema::hasColumn('documents', 'status_id')) {
                $table->dropForeign(['status_id']);
                $table->dropColumn('status_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->unsignedBigInteger('document_type_id')->nullable()->after('id');
            $table->unsignedBigInteger('status_id')->nullable()->after('document_type_id');
            $table->dropForeign(['document_sub_type_id']);
            $table->dropColumn('document_sub_type_id');
            $table->dropForeign(['document_status_id']);
            $table->dropColumn('document_status_id');
        });
    }
}; 