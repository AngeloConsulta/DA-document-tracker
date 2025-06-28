<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission)
    {
        \Illuminate\Support\Facades\Log::info('CheckPermission middleware reached', [
            'permission' => $permission,
            'user_authenticated' => auth()->check(),
        ]);

        if (!auth()->check()) {
            // User is not logged in
            return redirect('login');
        }

        $user = auth()->user();

        if ($user->isSuperadmin()) {
            // Superadmin has all permissions
            return $next($request);
        }

        if ($user->hasPermission($permission)) {
            // User has the required permission
            return $next($request);
        }

        // User does not have the required permission
        abort(403, 'Unauthorized action.');
    }
}
