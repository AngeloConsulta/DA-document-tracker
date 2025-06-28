<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DocumentPolicy
{
    /**
     * Intercept checks for superadmin.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isSuperadmin()) {
            return true;
        }

        return null; // Continue to the specific policy methods
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view documents list
        // The actual filtering by department will happen in the controller's query
        return $user->hasPermission('documents.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Document $document): bool
    {
        // Superadmin can view any document
        if ($user->isSuperadmin()) {
            return true;
        }

        // Department users can only view documents from their department
        if ($user->isDepartmentUser()) {
            return $user->hasPermission('documents.view') && 
                   $user->department_id === $document->department_id;
        }

        // Admin users can view documents from their department
        if ($user->isAdmin()) {
            return $user->hasPermission('documents.view') && 
                   $user->department_id === $document->department_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('documents.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Document $document): bool
    {
        // Superadmin can update any document
        if ($user->isSuperadmin()) {
            return true;
        }

        // Department users can only update documents from their department
        if ($user->isDepartmentUser()) {
            return $user->hasPermission('documents.edit') && 
                   $user->department_id === $document->department_id;
        }

        // Admin users can update documents from their department
        if ($user->isAdmin()) {
            return $user->hasPermission('documents.edit') && 
                   $user->department_id === $document->department_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Document $document): bool
    {
        // Superadmin can delete any document
        if ($user->isSuperadmin()) {
            return true;
        }

        // Department users can only delete documents from their department
        if ($user->isDepartmentUser()) {
            return $user->hasPermission('documents.delete') && 
                   $user->department_id === $document->department_id;
        }

        // Admin users can delete documents from their department
        if ($user->isAdmin()) {
            return $user->hasPermission('documents.delete') && 
                   $user->department_id === $document->department_id;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Document $document): bool
    {
        // Only superadmin can restore
        return $user->isSuperadmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Document $document): bool
    {
        // Only superadmin can force delete
        return $user->isSuperadmin();
    }

    /**
     * Determine whether the user can view document statistics.
     */
    public function viewStatistics(User $user): bool
    {
        return $user->isSuperadmin() || $user->hasPermission('statistics.view');
    }

    /**
     * Determine whether the user can manage users.
     */
    public function manageUsers(User $user): bool
    {
        return $user->isSuperadmin() || $user->hasPermission('users.manage');
    }
}
