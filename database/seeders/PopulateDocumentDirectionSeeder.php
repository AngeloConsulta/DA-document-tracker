<?php

namespace Database\Seeders;

use App\Models\Document;
use Illuminate\Database\Seeder;

class PopulateDocumentDirectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documents = Document::whereNull('direction')->get();
        
        if ($documents->isEmpty()) {
            $this->command->info('No documents found without direction values.');
            return;
        }

        $updatedCount = 0;
        
        foreach ($documents as $document) {
            // Determine direction based on sent_at field
            $direction = $document->sent_at ? 'outgoing' : 'incoming';
            
            $document->update(['direction' => $direction]);
            $updatedCount++;
        }

        $this->command->info("Updated {$updatedCount} documents with direction values.");
    }
} 