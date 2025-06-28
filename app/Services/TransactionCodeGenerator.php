<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Facades\DB;

class TransactionCodeGenerator
{
    /**
     * Generate a new transaction code including department code, type code, year, and sequence.
     *
     * @param string $departmentCode The department code (e.g., "ORTD RR", "AMIA")
     * @param string $documentTypeCode The document type code (e.g., "MEMO", "RR")
     * @return string The generated transaction code
     */
    public static function generate(string $departmentCode, string $documentTypeCode): string
    {
        // Get the current year
        $year = date('Y');
        
        // Define the pattern to search for previous tracking numbers for this department and document type
        $searchPattern = "{$departmentCode} {$documentTypeCode} {$year}-%";

        // Get the last used number for this department, document type, and year
        $lastNumber = DB::table('documents')
            ->where('tracking_number', 'like', $searchPattern)
            ->orderBy('tracking_number', 'desc')
            ->value('tracking_number');
        
        // Extract the sequence number from the last tracking number
        $sequence = 1;
        if ($lastNumber) {
            $parts = explode('-', $lastNumber);
            if (count($parts) === 2) {
                $sequence = (int) $parts[1] + 1;
            }
        }
        
        $generatedTrackingNumber = '';
        
        // Keep generating until a unique tracking number is found
        do {
            // Format the sequence number with leading zeros
            $formattedSequence = str_pad($sequence, 3, '0', STR_PAD_LEFT);
            
            // Generate the potential tracking number
            $generatedTrackingNumber = "{$departmentCode} {$documentTypeCode} {$year}-{$formattedSequence}";
            
            // Check if this tracking number already exists
            $exists = Document::where('tracking_number', $generatedTrackingNumber)->exists();
            
            // If it exists, increment the sequence and try again
            if ($exists) {
                $sequence++;
            }
            
        } while ($exists);
        
        // Return the unique tracking number
        return $generatedTrackingNumber;
    }
} 