<?php

/**
 * QR Scanner Usage Examples
 * 
 * This file demonstrates how the enhanced QR scanner works with different
 * QR code storage strategies in the document tracker system.
 */

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Department;
use App\Models\DocumentStatus;

// Example 1: Document with QR code value only
$document1 = Document::create([
    'tracking_number' => 'DOC-2024-001',
    'title' => 'Sample Document 1',
    'description' => 'Document with QR code value',
    'qr_code' => 'DOC-2024-001-QR-CODE',
    'qr_code_path' => null, // No image path stored
    'document_type_id' => 1,
    'department_id' => 1,
    'status_id' => 1,
    'direction' => 'incoming',
    'source' => 'External',
    'created_by' => 1,
    'date_received' => now(),
]);

// Example 2: Document with QR code image path only
$document2 = Document::create([
    'tracking_number' => 'DOC-2024-002',
    'title' => 'Sample Document 2',
    'description' => 'Document with QR code image path',
    'qr_code' => null, // No QR code value stored
    'qr_code_path' => 'documents/qr-codes/doc-2024-002.png',
    'document_type_id' => 1,
    'department_id' => 1,
    'status_id' => 1,
    'direction' => 'incoming',
    'source' => 'External',
    'created_by' => 1,
    'date_received' => now(),
]);

// Example 3: Document with both QR code value and image path
$document3 = Document::create([
    'tracking_number' => 'DOC-2024-003',
    'title' => 'Sample Document 3',
    'description' => 'Document with both QR code value and image path',
    'qr_code' => 'DOC-2024-003-QR-CODE',
    'qr_code_path' => 'documents/qr-codes/doc-2024-003.png',
    'document_type_id' => 1,
    'department_id' => 1,
    'status_id' => 1,
    'direction' => 'incoming',
    'source' => 'External',
    'created_by' => 1,
    'date_received' => now(),
]);

/**
 * Scanner Behavior Examples:
 * 
 * 1. Scanning "DOC-2024-001-QR-CODE" will find document1 (matches qr_code)
 * 2. Scanning "documents/qr-codes/doc-2024-002.png" will find document2 (matches qr_code_path)
 * 3. Scanning "DOC-2024-003-QR-CODE" will find document3 (matches qr_code - prioritized)
 * 4. Scanning "documents/qr-codes/doc-2024-003.png" will find document3 (matches qr_code_path)
 * 
 * The scanner uses this query:
 * 
 * SELECT * FROM documents 
 * WHERE qr_code = 'scanned_value' 
 *    OR qr_code_path = 'scanned_value'
 * LIMIT 1;
 */

/**
 * API Usage Examples:
 */

// Example API call to scan a QR code
$response = Http::withHeaders([
    'Content-Type' => 'application/json',
    'X-CSRF-TOKEN' => csrf_token(),
])->post('/scanner/scan', [
    'qr_code' => 'DOC-2024-001-QR-CODE'
]);

// Response will be:
// {
//     "success": true,
//     "document": {
//         "id": 1,
//         "tracking_number": "DOC-2024-001",
//         "title": "Sample Document 1",
//         "qr_code": "DOC-2024-001-QR-CODE",
//         "qr_code_path": null,
//         ...
//     },
//     "message": "Document found successfully"
// }

/**
 * Migration Strategy Examples:
 */

// If you're migrating from qr_code_path to qr_code:

// 1. Extract QR code value from existing image paths
$existingDocuments = Document::whereNotNull('qr_code_path')->get();

foreach ($existingDocuments as $document) {
    // Extract QR code value from the path or generate new one
    $qrCodeValue = extractQrCodeFromPath($document->qr_code_path);
    
    $document->update([
        'qr_code' => $qrCodeValue
    ]);
}

// 2. Or generate new QR codes for existing documents
foreach ($existingDocuments as $document) {
    $qrCodeValue = generateQrCode($document->tracking_number);
    
    $document->update([
        'qr_code' => $qrCodeValue
    ]);
}

/**
 * Helper Functions:
 */

function extractQrCodeFromPath($path) {
    // Extract QR code value from file path
    // This is just an example - implement based on your naming convention
    $filename = basename($path, '.png');
    return str_replace('doc-', 'DOC-', $filename) . '-QR-CODE';
}

function generateQrCode($trackingNumber) {
    // Generate QR code value based on tracking number
    return $trackingNumber . '-QR-CODE';
}

/**
 * Benefits of Dual Field Support:
 * 
 * 1. Backward Compatibility: Existing documents with qr_code_path continue to work
 * 2. Flexibility: Choose the storage strategy that fits your needs
 * 3. Migration Path: Gradually migrate from one approach to another
 * 4. Hybrid Approach: Use both fields for maximum compatibility
 * 5. Future-Proof: Easy to adapt to changing requirements
 */ 