<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ClearDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:clear {--force : Force the operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all documents from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('This will delete ALL documents and their associated files. Are you sure?')) {
                $this->info('Operation cancelled.');
                return;
            }
        }

        $this->info('Clearing documents...');

        // Start transaction
        DB::beginTransaction();

        try {
            // Get all document file paths before deletion
            $filePaths = DB::table('documents')->pluck('file_path')->filter();

            // Delete document histories first (due to foreign key constraints)
            DB::table('document_histories')->delete();
            
            // Delete the documents
            DB::table('documents')->delete();

            // Delete associated files from storage
            foreach ($filePaths as $path) {
                if ($path) {
                    Storage::delete($path);
                }
            }

            DB::commit();
            $this->info('All documents have been cleared successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('An error occurred while clearing documents: ' . $e->getMessage());
        }
    }
} 