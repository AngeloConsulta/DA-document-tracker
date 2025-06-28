<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Department;
use App\Models\User;
use App\Models\DocumentHistory;
use App\Models\Notification;
use App\Events\DocumentForwarded;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class DocumentRoutingController extends Controller
{
    protected $forwardedStatus;

    public function __construct()
    {
        $this->forwardedStatus = \App\Models\DocumentStatus::firstOrCreate(
            ['code' => 'FORWARDED'],
            [
                'name' => 'Forwarded',
                'description' => 'Document has been forwarded to another department or user',
                'is_active' => true
            ]
        );
    }

    /**
     * Show routing interface for a document
     */
    public function showRouting(Document $document): View
    {
        $document->load(['department', 'assignee', 'histories.user', 'histories.toDepartment', 'histories.toUser']);
        
        $departments = Department::where('is_active', true)->get();
        $users = User::where('is_active', true)->get();
        
        return view('documents.routing', compact('document', 'departments', 'users'));
    }

    /**
     * Route document to another department
     */
    public function routeToDepartment(Request $request, Document $document): JsonResponse
    {
        $request->validate([
            'to_department_id' => 'required|exists:departments,id',
            'to_user_id' => 'nullable|exists:users,id',
            'remarks' => 'required|string|max:500',
            'priority' => 'nullable|in:low,normal,high,urgent'
        ]);

        try {
            $oldDepartmentId = $document->department_id;
            $oldAssigneeId = $document->current_assignee;

            $document->update([
                'department_id' => $request->to_department_id,
                'current_assignee' => $request->to_user_id,
                'status_id' => $this->forwardedStatus->id,
                'metadata' => array_merge($document->metadata ?? [], [
                    'priority' => $request->priority ?? 'normal',
                    'routed_at' => now()->toISOString()
                ])
            ]);

            // Create routing history (null-safe)
            $document->histories()->create([
                'user_id' => auth()->id(),
                'from_department_id' => $oldDepartmentId,
                'to_department_id' => $request->to_department_id,
                'from_user_id' => $oldAssigneeId,
                'to_user_id' => $request->to_user_id,
                'remarks' => $request->remarks,
                'action_type' => 'routing'
            ]);

            // Create notification for the target user
            if ($request->to_user_id) {
                $notification = Notification::create([
                    'user_id' => $request->to_user_id,
                    'document_id' => $document->id,
                    'from_department_id' => $oldDepartmentId,
                    'to_department_id' => $request->to_department_id,
                    'title' => 'Document Routed',
                    'message' => "Document {$document->tracking_number} has been routed to your department.",
                    'type' => 'document_routed'
                ]);

                // Broadcast the event
                broadcast(new DocumentForwarded($document, $request->to_user_id, $notification))->toOthers();
            }

            return response()->json([
                'success' => true,
                'message' => 'Document routed successfully',
                'document' => $document->fresh(['department', 'assignee'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to route document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Forward document to another user
     */
    public function forwardToUser(Request $request, Document $document): JsonResponse
    {
        $request->validate([
            'to_user_id' => 'required|exists:users,id',
            'remarks' => 'required|string|max:500',
            'priority' => 'nullable|in:low,normal,high,urgent'
        ]);

        try {
            $oldAssigneeId = $document->current_assignee;

            $document->update([
                'current_assignee' => $request->to_user_id,
                'status_id' => $this->forwardedStatus->id,
                'metadata' => array_merge($document->metadata ?? [], [
                    'priority' => $request->priority ?? 'normal',
                    'forwarded_at' => now()->toISOString()
                ])
            ]);

            // Create forwarding history (null-safe)
            $document->histories()->create([
                'user_id' => auth()->id(),
                'from_user_id' => $oldAssigneeId,
                'to_user_id' => $request->to_user_id,
                'remarks' => $request->remarks,
                'action_type' => 'forwarding'
            ]);

            // Create notification for the target user
            $notification = Notification::create([
                'user_id' => $request->to_user_id,
                'document_id' => $document->id,
                'from_department_id' => $document->department_id,
                'to_department_id' => $document->department_id,
                'title' => 'Document Forwarded',
                'message' => "Document {$document->tracking_number} has been forwarded to you.",
                'type' => 'document_forwarded'
            ]);

            // Broadcast the event
            broadcast(new DocumentForwarded($document, $request->to_user_id, $notification))->toOthers();

            return response()->json([
                'success' => true,
                'message' => 'Document forwarded successfully',
                'document' => $document->fresh(['assignee'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to forward document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get routing history for a document
     */
    public function getRoutingHistory(Document $document): JsonResponse
    {
        $history = $document->histories()
            ->with(['user', 'fromDepartment', 'toDepartment', 'fromUser', 'toUser'])
            ->whereIn('action_type', ['routing', 'forwarding'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'history' => $history
        ]);
    }

    /**
     * Get available routing options
     */
    public function getRoutingOptions(): JsonResponse
    {
        $departments = Department::where('is_active', true)
            ->with('users')
            ->get();
        
        $users = User::where('is_active', true)->get();

        return response()->json([
            'success' => true,
            'departments' => $departments,
            'users' => $users
        ]);
    }

    /**
     * Bulk route multiple documents
     */
    public function bulkRoute(Request $request): JsonResponse
    {
        $request->validate([
            'document_ids' => 'required|array',
            'document_ids.*' => 'exists:documents,id',
            'to_department_id' => 'required|exists:departments,id',
            'to_user_id' => 'nullable|exists:users,id',
            'remarks' => 'required|string|max:500'
        ]);

        try {
            $documents = Document::whereIn('id', $request->document_ids)->get();
            $routedCount = 0;

            foreach ($documents as $document) {
                $oldDepartmentId = $document->department_id;
                $oldAssigneeId = $document->current_assignee;

                $document->update([
                    'department_id' => $request->to_department_id,
                    'current_assignee' => $request->to_user_id
                ]);

                $document->histories()->create([
                    'user_id' => auth()->id(),
                    'from_department_id' => $oldDepartmentId,
                    'to_department_id' => $request->to_department_id,
                    'from_user_id' => $oldAssigneeId,
                    'to_user_id' => $request->to_user_id,
                    'remarks' => $request->remarks,
                    'action_type' => 'bulk_routing'
                ]);

                $routedCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully routed {$routedCount} documents",
                'routed_count' => $routedCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk route documents: ' . $e->getMessage()
            ], 500);
        }
    }
} 