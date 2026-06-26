<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasPermission('roles.view')) {
            abort(403, 'Unauthorized action.');
        }

        $roles = Role::withCount('users')->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        if (!Auth::user()->hasPermission('roles.create')) {
            abort(403, 'Unauthorized action.');
        }

        $permissions = Permission::orderBy('module')->orderBy('name')->get()->groupBy('module');
        return view('roles.form', compact('permissions'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasPermission('roles.create')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }
        $validator = validator($request->all(), [
            'name'          => 'required|string|max:125|unique:roles,name',
            'description'   => 'nullable|string|max:255',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $role = Role::create([
            'name'        => $request->name,
            'guard_name'  => 'web',
            'description' => $request->description,
        ]);

        $role->permissions()->sync($request->permissions ?? []);

        ActivityLog::log("Menambahkan role baru: {$role->name}", $role, ['name' => $role->name, 'description' => $role->description], 'role');

        return response()->json([
            'message'  => 'Role berhasil ditambahkan.',
            'redirect' => route('roles.index'),
        ]);
    }

    public function edit(Role $role)
    {
        if (!Auth::user()->hasPermission('roles.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $permissions = Permission::orderBy('module')->orderBy('name')->get()->groupBy('module');
        $role->load('permissions');
        return view('roles.form', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        if (!Auth::user()->hasPermission('roles.edit')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }
        $validator = validator($request->all(), [
            'name'          => ['required', 'string', 'max:125', Rule::unique('roles')->ignore($role->id)],
            'description'   => 'nullable|string|max:255',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $role->update([
            'name'        => $request->name,
            'description' => $request->description,
        ]);

        $role->permissions()->sync($request->permissions ?? []);

        ActivityLog::log("Memperbarui role: {$role->name}", $role, $role->getChanges(), 'role');

        return response()->json([
            'message'  => 'Role berhasil diupdate.',
            'redirect' => route('roles.index'),
        ]);
    }

    public function destroy(Role $role)
    {
        if (!Auth::user()->hasPermission('roles.delete')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $name = $role->name;
        $role->delete();
        ActivityLog::log("Menghapus role: {$name}", $role, ['name' => $name], 'role');
        return response()->json(['message' => 'Role berhasil dihapus.']);
    }
}
