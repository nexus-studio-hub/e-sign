<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::orderBy('level')->get();
        
        return Inertia::render('Settings/Roles/Index', [
            'roles' => $roles
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|min:3|max:50|unique:roles,name',
            'level' => 'required|integer|between:1,3',
        ]);

        // Auto-generate code from name
        $data['code'] = strtolower(preg_replace('/[^a-z0-9]+/', '_', $data['name']));
        $data['code'] = trim($data['code'], '_');

        Role::create($data);

        return redirect()->route('roles.index')->with('success', 'Role berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|min:3|max:50|unique:roles,name,' . $id,
            'level' => 'required|integer|between:1,3',
        ]);

        // Auto-update code from name
        $data['code'] = strtolower(preg_replace('/[^a-z0-9]+/', '_', $data['name']));
        $data['code'] = trim($data['code'], '_');

        $role->update($data);

        return redirect()->route('roles.index')->with('success', 'Role berhasil diupdate');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        // Check if role is being used by any user
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Role tidak bisa dihapus karena masih digunakan oleh user');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role berhasil dihapus');
    }
}
