<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_statuses', function (Blueprint $table) {
            // Drop unique index if it exists
            $table->dropUnique(['code']);
            $table->dropColumn('code');
        });
    }

    public function down(): void
    {
        Schema::table('document_statuses', function (Blueprint $table) {
            $table->string('code')->unique()->nullable();
        });
    }
}; 