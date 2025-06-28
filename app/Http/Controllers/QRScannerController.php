<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentStatus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class QRScannerController extends Controller
{
    /**
     * Display the scanner view
     */
    public function index(): View
    {
        $statuses = DocumentStatus::where('is_active', true)->get();
        return view('scanner.index', compact('statuses'));
    }

    /**
     * Handle the QR code scan request
     */
    public function scan(Request $request): JsonResponse
    {
        $request->validate([
            'qr_code' => 'required|string|max:255'
        ]);

        try {
            // Search for document using both qr_code and qr_code_path
            $document = Document::where(function($query) use ($request) {
                $query->where('qr_code', $request->qr_code)
                      ->orWhere('qr_code_path', $request->qr_code);
            })
            ->with(['documentType', 'department', 'status', 'histories' => function($query) {
                $query->latest()->take(5);
            }])
            ->first();

            if (!$document) {
                Log::info('QR Code scan: Document not found', [
                    'qr_code' => $request->qr_code,
                    'user_id' => auth()->id(),
                    'ip' => $request->ip()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Document not found',
                    'error_code' => 'DOCUMENT_NOT_FOUND'
                ], 404);
            }

            // Log successful scan
            Log::info('QR Code scan: Document found', [
                'document_id' => $document->id,
                'qr_code' => $request->qr_code,
                'user_id' => auth()->id(),
                'document_status' => $document->status->name ?? 'Unknown',
                'matched_field' => $document->qr_code === $request->qr_code ? 'qr_code' : 'qr_code_path'
            ]);

            return response()->json([
                'success' => true,
                'document' => $document,
                'message' => 'Document found successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('QR Code scan error', [
                'qr_code' => $request->qr_code,
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the scan',
                'error_code' => 'PROCESSING_ERROR'
            ], 500);
        }
    }

    /**
     * Update document status after scanning
     */
    public function updateStatus(Request $request, Document $document): JsonResponse
    {
        $request->validate([
            'status_id' => 'required|exists:document_statuses,id',
            'remarks' => 'nullable|string|max:500'
        ]);

        try {
            $oldStatus = $document->status;
            
            $document->update([
                'status_id' => $request->status_id
            ]);

            // Add to document history
            $document->histories()->create([
                'user_id' => auth()->id(),
                'from_status_id' => $oldStatus->id ?? null,
                'to_status_id' => $request->status_id,
                'remarks' => $request->remarks,
                'action_type' => 'status_update'
            ]);

            // Log the status update
            Log::info('Document status updated via QR scanner', [
                'document_id' => $document->id,
                'old_status' => $oldStatus->name ?? 'Unknown',
                'new_status' => $document->fresh()->status->name ?? 'Unknown',
                'user_id' => auth()->id(),
                'remarks' => $request->remarks
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'document' => $document->fresh()->load(['documentType', 'department', 'status'])
            ]);

        } catch (\Exception $e) {
            Log::error('Document status update error', [
                'document_id' => $document->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating document status',
                'error_code' => 'UPDATE_ERROR'
            ], 500);
        }
    }

    /**
     * Get available document statuses for the scanner
     */
    public function getStatuses(): JsonResponse
    {
        try {
            $statuses = DocumentStatus::where('is_active', true)
                ->select('id', 'name', 'description')
                ->get();

            return response()->json([
                'success' => true,
                'statuses' => $statuses
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching document statuses', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error fetching statuses'
            ], 500);
        }
    }
} 