<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use App\Services\QrCodeService;

class QrCodeController extends Controller
{
    protected QrCodeService $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Generate QR code for a document
     */
    public function generateForDocument(Request $request, Document $document): JsonResponse
    {
        try {
            $document->load(['documentType', 'department', 'status']);
            
            $qrCodeData = [
                'tracking_number' => $document->tracking_number,
                'title' => $document->title,
                'type' => $document->documentType->name,
                'department' => $document->department->name,
                'status' => $document->status->name,
                'date_received' => $document->date_received->format('Y-m-d'),
                'url' => route('documents.show', $document),
                'document_id' => $document->id
            ];

            $qrCodePath = $this->qrCodeService->generateCustomQrCode(
                json_encode($qrCodeData),
                'qr_codes/' . $document->tracking_number . '.svg',
                [
                    'size' => 300,
                    'margin' => 10,
                    'error_correction_level' => \Endroid\QrCode\ErrorCorrectionLevel::High,
                ]
            );

            $document->update(['qr_code_path' => $qrCodePath]);

            return response()->json([
                'success' => true,
                'message' => 'QR code generated successfully',
                'qr_code_path' => $qrCodePath,
                'download_url' => route('qr-codes.download', $document)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate QR code: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download QR code for a document
     */
    public function download(Document $document): Response
    {
        if (!$document->qr_code_path) {
            abort(404, 'QR code not found');
        }

        $path = Storage::path('public/' . $document->qr_code_path);
        
        if (!file_exists($path)) {
            abort(404, 'QR code file not found');
        }

        return response()->download($path, 'document-' . $document->tracking_number . '-qr.svg');
    }

    /**
     * Show QR code for a document
     */
    public function show(Document $document)
    {
        if (!$document->qr_code_path) {
            return redirect()->route('qr-codes.generate', $document)
                ->with('info', 'QR code not found. Generating new QR code...');
        }

        $qrCodeUrl = Storage::url($document->qr_code_path);
        
        return view('qr-codes.show', compact('document', 'qrCodeUrl'));
    }

    /**
     * Generate QR code for printing (larger size)
     */
    public function generateForPrinting(Document $document): JsonResponse
    {
        try {
            $document->load(['documentType', 'department', 'status']);
            
            $qrCodeData = [
                'tracking_number' => $document->tracking_number,
                'title' => $document->title,
                'type' => $document->documentType->name,
                'department' => $document->department->name,
                'status' => $document->status->name,
                'date_received' => $document->date_received->format('Y-m-d'),
                'url' => route('documents.show', $document),
                'document_id' => $document->id
            ];

            $qrCodePath = $this->qrCodeService->generateCustomQrCode(
                json_encode($qrCodeData),
                'qr_codes/print/' . $document->tracking_number . '-print.svg',
                [
                    'size' => 600,
                    'margin' => 20,
                    'error_correction_level' => \Endroid\QrCode\ErrorCorrectionLevel::High,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Print QR code generated successfully',
                'qr_code_path' => $qrCodePath,
                'download_url' => Storage::url($qrCodePath)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate print QR code: ' . $e->getMessage()
            ], 500);
        }
    }
} 