<?php

namespace App\Services;

use App\Models\User;
use App\Models\Document;
use Illuminate\Database\Eloquent\Builder;

class DocumentAccessService
{
    /**
     * Apply role-based filtering to a document query
     */
    public function applyRoleBasedFilter(Builder $query, User $user): Builder
    {
        if ($user->isSuperadmin()) {
            // Superadmin can see all documents from all departments
            return $query;
        } elseif ($user->isAdmin() || $user->isDepartmentUser()) {
            // Admin and department users can only see documents from their department
            return $query->where('department_id', $user->department_id);
        } else {
            // Other roles - return empty query
            return $query->whereRaw('1 = 0'); // This will return no results
        }
    }

    /**
     * Check if a user can access a specific document
     */
    public function canAccessDocument(User $user, Document $document): bool
    {
        if ($user->isSuperadmin()) {
            return true;
        }

        if ($user->isAdmin() || $user->isDepartmentUser()) {
            return $user->department_id === $document->department_id;
        }

        return false;
    }

    /**
     * Get filtered departments based on user role
     */
    public function getFilteredDepartments(User $user)
    {
        if ($user->isSuperadmin()) {
            return \App\Models\Department::where('is_active', true)->get();
        } else {
            return \App\Models\Department::where('is_active', true)
                ->where('id', $user->department_id)
                ->get();
        }
    }

    /**
     * Get filtered users based on user role
     */
    public function getFilteredUsers(User $user)
    {
        if ($user->isSuperadmin()) {
            return \App\Models\User::where('is_active', true)->get();
        } else {
            return \App\Models\User::where('is_active', true)
                ->where('department_id', $user->department_id)
                ->get();
        }
    }

    /**
     * Validate document department assignment for non-superadmin users
     */
    public function validateDepartmentAssignment(User $user, int $departmentId): bool
    {
        if ($user->isSuperadmin()) {
            return true;
        }

        return $user->department_id === $departmentId;
    }

    /**
     * Get documents accessible to the user
     */
    public function getAccessibleDocuments(User $user, array $with = [])
    {
        $query = Document::with($with);
        return $this->applyRoleBasedFilter($query, $user);
    }
} 