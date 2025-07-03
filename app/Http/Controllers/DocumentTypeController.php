<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentType;
use Illuminate\Support\Str;

class DocumentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!auth()->user()->hasPermission('document_types.view')) {
            abort(403, 'Unauthorized action.');
        }

        $documentTypes = DocumentType::latest()->paginate(10);
        return view('document-types.index', compact('documentTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!auth()->user()->hasPermission('document_types.create')) {
            abort(403, 'Unauthorized action.');
        }

        return view('document-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasPermission('document_types.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:10|unique:document_types,code',
                'description' => 'nullable|string',
                'requires_approval' => 'boolean',
                'is_active' => 'boolean'
            ]);

            DocumentType::create($validated);

            return redirect()->route('document-types.index')
                ->with('success', 'Document type created successfully.');
        } catch (\Exception $e) {
            return redirect()->route('document-types.index')
                ->withInput()
                ->withErrors(['error' => 'Failed to create document type: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(DocumentType $documentType)
    {
        if (request()->ajax()) {
            return response()->view('document-types.show-modal', compact('documentType'));
        }
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DocumentType $documentType)
    {
        if (!auth()->user()->hasPermission('document_types.edit')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            return response()->view('document-types.edit-modal', compact('documentType'));
        }
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DocumentType $documentType)
    {
        if (!auth()->user()->hasPermission('document_types.edit')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:10|unique:document_types,code,' . $documentType->id,
                'description' => 'nullable|string',
                'requires_approval' => 'boolean',
                'is_active' => 'boolean'
            ]);

            $documentType->update($validated);

            return redirect()->route('document-types.index')
                ->with('success', 'Document type updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors(['error' => 'Failed to update document type: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DocumentType $documentType)
    {
        if (!auth()->user()->hasPermission('document_types.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Check if document type is in use
            if ($documentType->documents()->count() > 0) {
                return back()->with('error', 'Cannot delete document type that is in use.');
            }

            $documentType->delete();

            return redirect()->route('document-types.index')
                ->with('success', 'Document type deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete document type: ' . $e->getMessage());
        }
    }

    public function subTypes($typeId)
    {
        $type = DocumentType::with('subTypes')->findOrFail($typeId);
        return response()->json($type->subTypes);
    }
}
