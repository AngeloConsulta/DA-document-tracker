<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentStatus;

class DocumentStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $documentStatuses = DocumentStatus::latest()->paginate(10);
        return view('document-statuses.index', compact('documentStatuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!auth()->user()->hasPermission('document_statuses.create')) {
            abort(403, 'Unauthorized action.');
        }

        return view('document-statuses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:document_statuses',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
        ]);

        $validated['is_active'] = $request->has('is_active');

        DocumentStatus::create($validated);

        return redirect()->route('document-statuses.index')
            ->with('success', 'Document status created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DocumentStatus $documentStatus)
    {
        if (request()->ajax()) {
            return response()->view('document-statuses.show-modal', compact('documentStatus'));
        }
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DocumentStatus $documentStatus)
    {
        if (!auth()->user()->hasPermission('document_statuses.edit')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            return response()->view('document-statuses.edit-modal', compact('documentStatus'));
        }
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (!auth()->user()->hasPermission('document_statuses.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:document_statuses,code,' . $id,
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $documentStatus = DocumentStatus::findOrFail($id);
        $documentStatus->update($validated);

        return redirect()->route('document-statuses.index')
            ->with('success', 'Document status updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
