<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Display a listing of users.
     */
    public function index(): View
    {
        $users = User::with(['role', 'department'])->paginate(10);
        $roles = Role::where('is_active', true)->get();
        $departments = Department::where('is_active', true)->get();
        return view('users.index', compact('users', 'roles', 'departments'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        // Only superadmin can create users
        if (!auth()->user()->isSuperadmin()) {
            abort(403, 'Unauthorized action.');
        }

        $roles = Role::where('is_active', true)->get();
        $departments = Department::where('is_active', true)->get();
        
        // Log to check if the method is reached and data is fetched
        \Illuminate\Support\Facades\Log::info('UserController@create reached', [
            'roles_count' => $roles->count(),
            'departments_count' => $departments->count(),
        ]);
        
        return view('users.create', compact('roles', 'departments'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Only superadmin can create users
        if (!auth()->user()->isSuperadmin()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => ['required', 'exists:roles,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'is_active' => ['boolean'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
            'department_id' => $validated['department_id'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): View
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        // Only superadmin can edit users
        if (!auth()->user()->isSuperadmin()) {
            abort(403, 'Unauthorized action.');
        }

        $roles = Role::where('is_active', true)->get();
        $departments = Department::where('is_active', true)->get();

        if (request()->ajax()) {
            return response()->view('users.edit-modal', compact('user', 'roles', 'departments'));
        }

        abort(404);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        // Only superadmin can update users
        if (!auth()->user()->isSuperadmin()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'role_id' => ['required', 'exists:roles,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'is_active' => ['boolean'],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'department_id' => $request->department_id,
            'is_active' => $request->is_active ?? $user->is_active,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'User updated successfully.']);
        }

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Only superadmin can delete users
        if (!auth()->user()->isSuperadmin()) {
            abort(403, 'Unauthorized action.');
        }

        // Prevent superadmin from deleting themselves
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
} 