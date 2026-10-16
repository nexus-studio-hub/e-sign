<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Supplier;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['supplier', 'reviewerRole']);
        
        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Filter by role
        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }
        
        $users = $query->latest()->paginate(10)->withQueryString();
        
        return Inertia::render('SuperAdmin/Users/Index', compact('users'));
    }

    public function create()
    {
        $suppliers = Supplier::where('status', 'active')->get();
        $roles = Role::orderBy('level')->get();
        return Inertia::render('SuperAdmin/Users/Create', compact('suppliers', 'roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|in:supplier,reviewer',
            'role_id' => 'required_if:role,reviewer|nullable|exists:roles,id',
            'supplier_id' => 'required_if:role,supplier|nullable|exists:suppliers,id',
        ]);
        
        // Default password untuk non-admin adalah 'asdfasdf'
        if (empty($data['password'])) {
            $data['password'] = 'asdfasdf';
        }
        
        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = true;
        
        User::create($data);
        
        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    public function edit($id)
    {
        $user = User::with('reviewerRole')->findOrFail($id);
        $suppliers = Supplier::where('status', 'active')->get();
        $roles = Role::orderBy('level')->get();
        return Inertia::render('SuperAdmin/Users/Edit', compact('user', 'suppliers', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:8',
            'role' => 'required|in:supplier,reviewer',
            'role_id' => 'required_if:role,reviewer|nullable|exists:roles,id',
            'supplier_id' => 'required_if:role,supplier|nullable|exists:suppliers,id',
            'is_active' => 'boolean',
        ]);
        
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }
        
        $user->update($data);
        
        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting superadmin
        if ($user->role === 'superadmin') {
            return back()->with('error', 'Cannot delete superadmin user');
        }
        
        // Hard delete
        $user->delete();
        
        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent toggling superadmin
        if ($user->role === 'superadmin') {
            return back()->with('error', 'Cannot modify superadmin status');
        }
        
        $user->update(['is_active' => !$user->is_active]);
        
        $status = $user->is_active ? 'activated' : 'deactivated';
        return redirect()->route('users.index')->with('success', "User {$status} successfully");
    }
}
