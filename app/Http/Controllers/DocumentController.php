<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Department;
use App\Models\DocumentStatus;
use App\Services\TransactionCodeGenerator;
use App\Services\DocumentAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\DocumentHistory;
use App\Models\Notification;

class DocumentController extends Controller
{
    protected $documentAccessService;

    public function __construct(DocumentAccessService $documentAccessService)
    {
        $this->authorizeResource(Document::class, 'document');
        $this->documentAccessService = $documentAccessService;
    }

    /**
     * Display a listing of the resource (All Documents, with filter tabs).
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $filter = $request->get('filter', 'all');
        $query = Document::query();

        if ($filter === 'incoming') {
            $query->incoming($user);
        } elseif ($filter === 'outgoing') {
            $query->outgoing($user);
        } else {
            $query->accessibleTo($user);
        }

        $documents = $query->with(['documentType', 'status', 'department', 'creator', 'assignee'])->latest()->paginate(10);
        $documentTypes = DocumentType::where('is_active', true)->get();
        $departments = Department::all();
        $statuses = DocumentStatus::where('is_active', true)->get();
        $users = User::all();
        $userDepartment = $user->department_id ?? null;

        return view('documents.index', compact('documents', 'documentTypes', 'departments', 'statuses', 'users', 'userDepartment', 'filter'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        
        $documentTypes = DocumentType::where('is_active', true)->get();
        $departments = $this->documentAccessService->getFilteredDepartments($user);
        $statuses = DocumentStatus::where('is_active', true)->get();
        $users = $this->documentAccessService->getFilteredUsers($user);
        $userDepartment = $user->department_id ?? null;
        
        return view('documents.create', compact('documentTypes', 'departments', 'statuses', 'users', 'userDepartment'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $user = auth()->user();
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'document_type_id' => 'required|exists:document_types,id',
                'department_id' => 'required|exists:departments,id',
                'status_id' => 'required|exists:document_statuses,id',
                'current_assignee' => 'nullable|exists:users,id',
                'date_received' => 'required|date',
                'due_date' => 'nullable|date|after:date_received',
                'file' => 'nullable|file|max:10240', // 10MB max
            ]);

            // For non-superadmins, force department_id to their own department
            if (!$user->isSuperadmin()) {
                $validated['department_id'] = $user->department_id;
            }

            // Get the document type code and department code for tracking number generation
            $documentType = DocumentType::findOrFail($validated['document_type_id']);
            $departmentCode = $user->department->code ?? 'UNKNOWN'; // Assuming department has a 'code' attribute
            
            // Generate tracking number using the new format
            $validated['tracking_number'] = TransactionCodeGenerator::generate($departmentCode, $documentType->code);
            $validated['created_by'] = $user->id;

            // Handle file upload
            if ($request->hasFile('file')) {
                $path = $request->file('file')->store('documents');
                $validated['file_path'] = $path;
            }

            $document = Document::create($validated);

            // Create initial history
            $document->histories()->create([
                'user_id' => $user->id,
                'to_status_id' => $validated['status_id'],
                'to_department_id' => $validated['department_id'],
                'to_user_id' => $validated['current_assignee'],
                'remarks' => 'Document created'
            ]);

            // For AJAX requests (modal submissions), return JSON response with full document data
            if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                $document->load(['documentType', 'status', 'department', 'creator', 'assignee']);
                return response()->json([
                    'success' => true,
                    'message' => 'Document created successfully.',
                    'document' => [
                        'id' => $document->id,
                        'tracking_number' => $document->tracking_number,
                        'title' => $document->title,
                        'status' => [
                            'name' => $document->status->name,
                            'color' => $document->status->color,
                        ],
                        'department' => [
                            'name' => $document->department->name,
                        ],
                        'documentType' => [
                            'name' => $document->documentType->name,
                        ],
                        'assignee' => [
                            'name' => $document->assignee ? $document->assignee->name : null,
                        ],
                    ]
                ]);
            }

            // For regular form submissions, redirect to documents index
            return redirect()->route('documents.index')
                ->with('success', 'Document created successfully.');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false, 
                    'message' => 'Failed to create document: ' . $e->getMessage()
                ], 422);
            }
            
            return redirect()->route('documents.index')
                ->withInput()
                ->withErrors(['error' => 'Failed to create document: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        $document->load(['documentType', 'status', 'department', 'creator', 'assignee', 'histories.user', 'histories.fromStatus', 'histories.toStatus', 'histories.fromDepartment', 'histories.toDepartment', 'histories.fromUser', 'histories.toUser']);
        if (request()->ajax()) {
            return response(view('documents.show-modal', compact('document'))->render());
        }
        return view('documents.show', compact('document'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Document $document)
    {
        $user = auth()->user();
        
        $documentTypes = DocumentType::where('is_active', true)->get();
        $departments = $this->documentAccessService->getFilteredDepartments($user);
        $statuses = DocumentStatus::where('is_active', true)->get();
        $users = $this->documentAccessService->getFilteredUsers($user);
        
        if (request()->ajax()) {
            try {
                return response(view('documents.edit-modal', compact('document', 'documentTypes', 'departments', 'statuses', 'users'))->render());
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error rendering edit-modal: ' . $e->getMessage(), ['exception' => $e]);
                return response()->json(['success' => false, 'message' => 'Failed to load edit form: ' . $e->getMessage()], 500);
            }
        }

        // For non-AJAX requests, show the document in the index view
        $query = $this->documentAccessService->getAccessibleDocuments($user, ['documentType', 'status', 'department', 'creator', 'assignee']);
        $documents = $query->latest()->paginate(10);
        $userDepartment = $user->department_id ?? null;
            
        return view('documents.index', compact('documents', 'documentTypes', 'departments', 'statuses', 'users', 'userDepartment', 'document'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Document $document)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'document_type_id' => 'required|exists:document_types,id',
                'department_id' => 'required|exists:departments,id',
                'status_id' => 'required|exists:document_statuses,id',
                'current_assignee' => 'nullable|exists:users,id',
                'due_date' => 'nullable|date|after:date_received',
                'file' => 'nullable|file|max:10240',
            ]);

            // Handle file upload
            if ($request->hasFile('file')) {
                // Delete old file if exists
                if ($document->file_path) {
                    Storage::delete($document->file_path);
                }
                $path = $request->file('file')->store('documents');
                $validated['file_path'] = $path;
            }

            // Check for changes to create history
            $changes = [];
            if ($document->status_id !== $validated['status_id']) {
                $changes['to_status_id'] = $validated['status_id'];
                $changes['from_status_id'] = $document->status_id;
            }
            if ($document->department_id !== $validated['department_id']) {
                $changes['to_department_id'] = $validated['department_id'];
                $changes['from_department_id'] = $document->department_id;
            }
            if ($document->current_assignee !== $validated['current_assignee']) {
                $changes['to_user_id'] = $validated['current_assignee'];
                $changes['from_user_id'] = $document->current_assignee;
            }

            $document->update($validated);

            // Create history if there are changes
            if (!empty($changes)) {
                $document->histories()->create([
                    'user_id' => auth()->id(),
                    'remarks' => 'Document updated',
                    ...$changes
                ]);
            }

            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Document updated successfully.']);
            }

            return redirect()->route('documents.index')
                ->with('success', 'Document updated successfully.');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to update document: ' . $e->getMessage()]);
            }
            return back()->withInput()
                ->withErrors(['error' => 'Failed to update document: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for deleting the specified resource.
     */
    public function delete(Document $document)
    {
        return view('documents.delete', compact('document'));
    }

    /**
     * Forward the specified document to another department or user.
     */
    public function forward(Request $request, Document $document)
    {
        try {
            $user = auth()->user();

            $validated = $request->validate([
                'remarks' => 'nullable|string',
                'next_department_id' => 'nullable|exists:departments,id',
                'next_assignee_id' => 'nullable|exists:users,id',
            ]);

            $forwardedStatus = DocumentStatus::where('code', 'FORWARDED')->first();
            if (!$forwardedStatus) {
                // Log the error and return a user-friendly message
                \Illuminate\Support\Facades\Log::error('Document status "FORWARDED" not found.');
                return response()->json(['success' => false, 'message' => 'Forwarding status is not configured correctly. Please contact an administrator.'], 500);
            }

            DB::transaction(function () use ($document, $request, $user, $validated, $forwardedStatus) {
                // Set direction for sender (outgoing)
                $document->direction = 'outgoing';
                $document->save();

                // Update document for receiver
                $document->update([
                    'status_id' => $forwardedStatus->id,
                    'department_id' => $request->next_department_id ?? $document->department_id,
                    'current_assignee' => $request->next_assignee_id,
                    'sent_at' => now(), // Mark as sent
                    'direction' => 'incoming', // Set direction for receiver
                ]);

                // Log to document_trackers table
                $tracker = [
                    'document_id' => $document->id,
                    'from_user_id' => $user->id,
                    'to_user_id' => $request->next_assignee_id,
                    'action' => 'forwarded',
                    'remarks' => $validated['remarks'] ?? 'Document forwarded',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                DB::table('document_trackers')->insert($tracker);

                // Create and dispatch notification
                $notification = Notification::create([
                    'user_id' => $request->next_assignee_id,
                    'document_id' => $document->id,
                    'type' => 'document_forwarded',
                    'message' => "Document #{$document->tracking_number} has been forwarded to you.",
                    'from_department_id' => $user->department_id,
                    'data' => [
                        'from_user_id' => $user->id,
                        'from_user_name' => $user->name,
                        'remarks' => $validated['remarks'] ?? 'Document forwarded',
                    ],
                ]);

                // Dispatch DocumentForwarded event
                event(new \App\Events\DocumentForwarded($document, $request->next_assignee_id, $notification));
            });

            return response()->json(['success' => true, 'message' => 'Document forwarded successfully.']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error forwarding document: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'An unexpected error occurred while forwarding the document.'], 500);
        }
    }

    /**
     * Remove the specified document from storage.
     */
    public function destroy(Document $document)
    {
        $document->delete();

        return redirect()->route('documents.index')
            ->with('success', 'Document deleted successfully.');
    }

    /**
     * Show a printable voucher for the document, including the print QR code.
     */
    public function printVoucher(Document $document)
    {
        $document->load(['documentType', 'status', 'department', 'creator', 'assignee']);
        $printQrPath = 'qr_codes/print/' . $document->tracking_number . '-print.svg';
        $printQrUrl = \Storage::disk('public')->exists($printQrPath)
            ? \Storage::url($printQrPath)
            : null;
        if (request()->has('modal')) {
            return view('documents.voucher-modal', compact('document', 'printQrUrl'));
        }
        return view('documents.voucher', compact('document', 'printQrUrl'));
    }

    /**
     * Display the dedicated incoming documents page with summary cards and table.
     */
    public function incomingPage()
    {
        $user = auth()->user();
        $query = Document::incoming($user)->with(['documentType', 'status', 'department', 'creator', 'assignee']);
        $documents = $query->latest()->paginate(10);

        // Calculate summary counts
        $incomingCount = $query->count();
        $pendingCount = (clone $query)->whereHas('status', function($q) { $q->where('name', 'Pending'); })->count();
        $receivedCount = (clone $query)->whereHas('status', function($q) { $q->where('name', 'Received'); })->count();
        $endedCount = (clone $query)->whereHas('status', function($q) { $q->where('name', 'Ended'); })->count();

        return view('documents.incoming.index', compact('documents', 'incomingCount', 'pendingCount', 'receivedCount', 'endedCount'));
    }

    /**
     * Display the dedicated outgoing documents page with summary cards and table.
     */
    public function outgoingPage()
    {
        $user = auth()->user();
        $query = \App\Models\Document::outgoing($user)->with(['documentType', 'status', 'department', 'creator', 'assignee']);
        $documents = $query->latest()->paginate(10);

        // Calculate summary counts
        $outgoingCount = $query->count();
        $pendingCount = (clone $query)->whereHas('status', function($q) { $q->where('name', 'Pending'); })->count();
        $sentCount = (clone $query)->whereHas('status', function($q) { $q->where('name', 'Sent'); })->count();
        $endedCount = (clone $query)->whereHas('status', function($q) { $q->where('name', 'Ended'); })->count();

        // Pass required variables for the create-modal
        $documentTypes = \App\Models\DocumentType::where('is_active', true)->get();
        $departments = \App\Models\Department::all();
        $statuses = \App\Models\DocumentStatus::where('is_active', true)->get();
        $users = \App\Models\User::all();
        $userDepartment = $user->department_id ?? null;

        return view('documents.outgoing.index', compact(
            'documents', 'outgoingCount', 'pendingCount', 'sentCount', 'endedCount',
            'documentTypes', 'departments', 'statuses', 'users', 'userDepartment'
        ));
    }
}
