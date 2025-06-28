<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'source')) {
                $table->string('source')->nullable(); // For incoming documents: sender, For outgoing documents: recipient
            }
            if (!Schema::hasColumn('documents', 'received_at')) {
                $table->timestamp('received_at')->nullable(); // For incoming documents
            }
            if (!Schema::hasColumn('documents', 'sent_at')) {
                $table->timestamp('sent_at')->nullable(); // For outgoing documents
            }
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['source', 'received_at', 'sent_at']);
        });
    }
}; 