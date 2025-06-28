<?php

namespace App\Services;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\ErrorCorrectionLevel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QrCodeService
{
    /**
     * Generate QR code for a document
     *
     * @param string $trackingNumber
     * @param string $title
     * @param array $additionalData
     * @return string
     */
    public function generateDocumentQrCode(string $trackingNumber, string $title, array $additionalData = []): string
    {
        // Create QR code data
        $qrData = json_encode([
            'tracking_number' => $trackingNumber,
            'title' => $title,
            'url' => url("/documents/{$trackingNumber}"),
            'timestamp' => now()->toISOString(),
            'qr_version' => '1.0',
            'additional_data' => $additionalData,
        ]);

        // Create QR code
        $qrCode = new QrCode($qrData);

        // Create writer
        $writer = new SvgWriter();

        // Write QR code (all options)
        $result = $writer->write($qrCode, null, null, [
            'size' => 300,
            'margin' => 10,
            'error_correction_level' => ErrorCorrectionLevel::High,
            'foreground_color' => new Color(0, 0, 0),
            'background_color' => new Color(255, 255, 255),
        ]);

        // Generate filename
        $filename = 'qr_codes/' . $trackingNumber . '_' . Str::slug($title) . '.svg';
        
        // Ensure directory exists
        Storage::disk('public')->makeDirectory('qr_codes');
        
        // Save QR code
        Storage::disk('public')->put($filename, $result->getString());

        return $filename;
    }

    /**
     * Generate QR code with custom styling
     *
     * @param string $data
     * @param string $filename
     * @param array $options
     * @return string
     */
    public function generateCustomQrCode(string $data, string $filename, array $options = []): string
    {
        $size = $options['size'] ?? 300;
        $margin = $options['margin'] ?? 10;
        $foregroundColor = $options['foreground_color'] ?? [0, 0, 0];
        $backgroundColor = $options['background_color'] ?? [255, 255, 255];
        $errorCorrection = $options['error_correction_level'] ?? ErrorCorrectionLevel::High;

        // Create QR code
        $qrCode = new QrCode($data);

        // Create writer
        $writer = new SvgWriter();

        // Write QR code (all options)
        $result = $writer->write($qrCode, null, null, [
            'size' => $size,
            'margin' => $margin,
            'error_correction_level' => $errorCorrection,
            'foreground_color' => new Color(...$foregroundColor),
            'background_color' => new Color(...$backgroundColor),
        ]);

        // Ensure directory exists
        Storage::disk('public')->makeDirectory('qr_codes');
        
        // Save QR code
        Storage::disk('public')->put($filename, $result->getString());

        return $filename;
    }

    /**
     * Generate QR code for document tracking URL
     *
     * @param string $trackingNumber
     * @return string
     */
    public function generateTrackingQrCode(string $trackingNumber): string
    {
        $trackingUrl = url("/track/{$trackingNumber}");
        
        return $this->generateCustomQrCode(
            $trackingUrl,
            "qr_codes/tracking_{$trackingNumber}.svg",
            [
                'size' => 250,
                'margin' => 8,
                'foreground_color' => [0, 51, 102], // Dark blue
                'background_color' => [255, 255, 255],
                'error_correction_level' => ErrorCorrectionLevel::High,
            ]
        );
    }

    /**
     * Generate QR code for document download
     *
     * @param string $trackingNumber
     * @param string $documentId
     * @return string
     */
    public function generateDownloadQrCode(string $trackingNumber, string $documentId): string
    {
        $downloadUrl = url("/documents/{$documentId}/download");
        
        return $this->generateCustomQrCode(
            $downloadUrl,
            "qr_codes/download_{$trackingNumber}.svg",
            [
                'size' => 200,
                'margin' => 5,
                'foreground_color' => [0, 128, 0], // Green
                'background_color' => [255, 255, 255],
                'error_correction_level' => ErrorCorrectionLevel::High,
            ]
        );
    }

    /**
     * Delete QR code file
     *
     * @param string $filename
     * @return bool
     */
    public function deleteQrCode(string $filename): bool
    {
        return Storage::disk('public')->delete($filename);
    }

    /**
     * Get QR code URL
     *
     * @param string $filename
     * @return string
     */
    public function getQrCodeUrl(string $filename): string
    {
        return Storage::disk('public')->url($filename);
    }
} 