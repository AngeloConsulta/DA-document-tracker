# Role-Based Access Control (RBAC) Implementation

## Overview

This document describes the implementation of role-based access control for the Document Tracker system. The system implements a hierarchical permission system that ensures users can only access documents and perform actions based on their role and department assignment.

## User Roles

### 1. Superadmin
- **Permissions**: Full system access (`*`)
- **Document Access**: Can view, create, edit, and delete documents from ALL departments
- **Department Management**: Can assign documents to any department
- **User Management**: Can manage all users across all departments

### 2. Admin
- **Permissions**: `users.view`, `users.create`, `users.edit`, `users.delete`, `documents.view`, `documents.create`, `documents.edit`, `documents.delete`, `statistics.view`, `departments.view`
- **Document Access**: Can view, create, edit, and delete documents from their assigned department only
- **Department Management**: Can only work with documents from their own department
- **User Management**: Can manage users within their department

### 3. Department User
- **Permissions**: `documents.view`, `documents.create`, `documents.edit`
- **Document Access**: Can view, create, and edit documents from their assigned department only
- **Department Management**: Can only work with documents from their own department
- **User Management**: No user management permissions

## Implementation Components

### 1. Document Policy (`app/Policies/DocumentPolicy.php`)

The DocumentPolicy implements Laravel's authorization system with the following key methods:

- `before()`: Grants superadmin full access to all operations
- `viewAny()`: Controls access to document listings
- `view()`: Controls access to individual documents
- `create()`: Controls document creation permissions
- `update()`: Controls document editing permissions
- `delete()`: Controls document deletion permissions

### 2. Document Access Service (`app/Services/DocumentAccessService.php`)

A service class that provides reusable methods for:

- `applyRoleBasedFilter()`: Applies department-based filtering to queries
- `canAccessDocument()`: Checks if a user can access a specific document
- `getFilteredDepartments()`: Returns departments based on user role
- `getFilteredUsers()`: Returns users based on user role
- `validateDepartmentAssignment()`: Validates department assignments

### 3. Document Access Middleware (`app/Http/Middleware/CheckDocumentAccess.php`)

Middleware that automatically checks document access permissions for routes with document parameters.

### 4. Updated Controllers

The DocumentController has been updated to use the DocumentAccessService for consistent role-based filtering across all methods.

## Access Control Rules

### Document Viewing
- **Superadmin**: Can view all documents from all departments
- **Admin/Department User**: Can only view documents from their assigned department

### Document Creation
- **Superadmin**: Can create documents for any department
- **Admin/Department User**: Can only create documents for their own department (automatically enforced)

### Document Editing
- **Superadmin**: Can edit any document
- **Admin/Department User**: Can only edit documents from their department

### Document Deletion
- **Superadmin**: Can delete any document
- **Admin**: Can delete documents from their department
- **Department User**: Cannot delete documents (no permission)

### Department and User Filtering
- **Superadmin**: Sees all departments and users in dropdowns
- **Admin/Department User**: Only see their own department and users from their department

## Route Protection

The following routes are protected with the `document.access` middleware:

```php
// Document CRUD routes
Route::resource('documents', DocumentController::class)->middleware('document.access');

// Document-specific operations
Route::post('/documents/{document}/forward', [DocumentController::class, 'forward'])->middleware('document.access');

// Incoming document routes
Route::get('/{document}', [DocumentController::class, 'showIncoming'])->middleware('document.access');
Route::get('/{document}/edit', [DocumentController::class, 'editIncoming'])->middleware('document.access');
Route::put('/{document}', [DocumentController::class, 'updateIncoming'])->middleware('document.access');
Route::get('/{document}/delete', [DocumentController::class, 'deleteIncoming'])->middleware('document.access');
Route::delete('/{document}', [DocumentController::class, 'destroyIncoming'])->middleware('document.access');

// Document routing
Route::prefix('documents/{document}/routing')->middleware('document.access')->group(function () {
    // Routing routes...
});

// QR Code operations
Route::post('documents/{document}/generate', [QrCodeController::class, 'generateForDocument'])->middleware('document.access');
Route::get('documents/{document}/download', [QrCodeController::class, 'download'])->middleware('document.access');
Route::get('documents/{document}/show', [QrCodeController::class, 'show'])->middleware('document.access');
Route::post('documents/{document}/print', [QrCodeController::class, 'generateForPrinting'])->middleware('document.access');

// Document status updates
Route::post('/documents/{document}/status', [DocumentScannerController::class, 'updateStatus'])->middleware('document.access');
Route::post('/documents/{document}/status', [QRScannerController::class, 'updateStatus'])->middleware('document.access');

// Document export
Route::get('/documents/{document}/history/excel', [DocumentExportController::class, 'exportHistoryToExcel'])->middleware('document.access');
```

## Testing

Comprehensive tests have been created in `tests/Feature/DocumentAccessTest.php` to verify:

- Superadmin can access all documents
- Admin can only access documents from their department
- Department users can only access documents from their department
- Proper error responses (403) for unauthorized access
- Form filtering based on user roles
- Document creation restrictions

## Usage Examples

### Checking Document Access in Controllers
```php
// Using the service
$user = auth()->user();
$query = $this->documentAccessService->getAccessibleDocuments($user, ['department', 'creator']);
$documents = $query->latest()->paginate(10);
```

### Checking Document Access in Policies
```php
public function view(User $user, Document $document): bool
{
    if ($user->isSuperadmin()) {
        return true;
    }
    
    return $user->hasPermission('documents.view') && 
           $user->department_id === $document->department_id;
}
```

### Using Middleware
```php
Route::get('/documents/{document}', [DocumentController::class, 'show'])
    ->middleware('document.access');
```

## Security Considerations

1. **Database Level**: All queries are filtered at the database level to prevent unauthorized data access
2. **Application Level**: Policies and middleware provide additional security layers
3. **UI Level**: Forms and dropdowns are filtered based on user permissions
4. **Route Level**: Middleware prevents unauthorized route access

## Maintenance

To add new roles or modify permissions:

1. Update the roles migration or seeder
2. Modify the DocumentPolicy if needed
3. Update the DocumentAccessService if new filtering logic is required
4. Add appropriate tests
5. Update this documentation

## Troubleshooting

### Common Issues

1. **403 Forbidden Errors**: Check user role and department assignment
2. **Missing Documents**: Verify user's department matches document's department
3. **Empty Dropdowns**: Check if user has proper role and department assignment

### Debugging

Enable debug mode and check:
- User's role and permissions
- User's department assignment
- Document's department assignment
- Policy evaluation results 