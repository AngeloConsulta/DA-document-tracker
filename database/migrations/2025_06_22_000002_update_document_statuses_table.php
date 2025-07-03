<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_statuses', function (Blueprint $table) {
            $table->foreignId('document_sub_type_id')->nullable()->after('id')->constrained('document_sub_types')->onDelete('cascade');
            if (Schema::hasColumn('document_statuses', 'document_type_id')) {
                $table->dropForeign(['document_type_id']);
                $table->dropColumn('document_type_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('document_statuses', function (Blueprint $table) {
            $table->unsignedBigInteger('document_type_id')->nullable()->after('id');
            $table->dropForeign(['document_sub_type_id']);
            $table->dropColumn('document_sub_type_id');
        });
    }
}; 