<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Department;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $departmentId = $user->department_id;

        $documentsQuery = Document::query();
        if (!$user->isSuperadmin() && !$user->isAdmin()) {
            $documentsQuery->where('department_id', $departmentId);
        }

        $stats = [
            'total_documents' => $documentsQuery->count(),
            'pending_documents' => (clone $documentsQuery)
                ->whereHas('status', function($query) {
                    $query->where('code', 'pending');
                })->count(),
            'departments' => Department::where('is_active', true)->count(),
            'my_assigned_documents' => Document::where('current_assignee', auth()->id())->count(),
            'incoming_documents' => Document::incoming($user)->count(),
            'outgoing_documents' => Document::outgoing($user)->count()
        ];

        $recentDocuments = (clone $documentsQuery)
            ->with(['documentType', 'status', 'department'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'recentDocuments'));
    }
}
