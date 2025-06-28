<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Document;

class CheckDocumentAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if (!$user) {
            abort(401, 'Unauthenticated.');
        }

        // If the route has a document parameter, check access
        if ($request->route('document')) {
            $document = $request->route('document');
            
            // Superadmin can access any document
            if ($user->isSuperadmin()) {
                return $next($request);
            }
            
            // Department users and admins can only access documents from their department
            if ($user->isAdmin() || $user->isDepartmentUser()) {
                if ($document->department_id !== $user->department_id) {
                    abort(403, 'You do not have permission to access this document.');
                }
            } else {
                // Other roles - deny access
                abort(403, 'You do not have permission to access documents.');
            }
        }

        // If no document parameter, allow the request to proceed
        // (this handles index, create, store routes)
        return $next($request);
    }
} 