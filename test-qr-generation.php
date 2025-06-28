<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "Testing QR Code Generation...\n";
    
    // Test 1: Basic QR Code Generation
    echo "\n1. Testing basic QR code generation...\n";
    
    $qrCodeService = new \App\Services\QrCodeService();
    
    $testData = [
        'tracking_number' => 'TEST-2024-001',
        'title' => 'Test Document',
        'url' => 'https://example.com/test',
        'timestamp' => now()->toISOString(),
        'qr_version' => '1.0'
    ];
    
    $qrCodePath = $qrCodeService->generateCustomQrCode(
        json_encode($testData),
        'qr_codes/test_qr.svg',
        [
            'size' => 300,
            'margin' => 10,
            'error_correction_level' => \Endroid\QrCode\ErrorCorrectionLevel::High,
        ]
    );
    
    echo "✓ QR code generated successfully at: {$qrCodePath}\n";
    
    // Test 2: Check if file exists
    echo "\n2. Checking if file exists...\n";
    $fullPath = storage_path('app/public/' . $qrCodePath);
    if (file_exists($fullPath)) {
        echo "✓ File exists at: {$fullPath}\n";
        echo "✓ File size: " . filesize($fullPath) . " bytes\n";
    } else {
        echo "✗ File does not exist at: {$fullPath}\n";
    }
    
    // Test 3: Check URL generation
    echo "\n3. Testing URL generation...\n";
    $url = $qrCodeService->getQrCodeUrl($qrCodePath);
    echo "✓ QR code URL: {$url}\n";
    
    // Test 4: Test with Document model (if available)
    echo "\n4. Testing with Document model...\n";
    try {
        $document = \App\Models\Document::first();
        if ($document) {
            echo "✓ Found document: {$document->tracking_number}\n";
            
            $documentQrPath = $qrCodeService->generateDocumentQrCode(
                $document->tracking_number,
                $document->title,
                ['test' => 'data']
            );
            
            echo "✓ Document QR code generated: {$documentQrPath}\n";
        } else {
            echo "! No documents found in database\n";
        }
    } catch (\Exception $e) {
        echo "! Error with Document model: " . $e->getMessage() . "\n";
    }
    
    echo "\n✅ QR Code generation test completed successfully!\n";
    
} catch (\Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} 