<?php

namespace App\Http\Controllers;

use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('roles.view'), 403);

        $roles = Role::withCount(['users', 'permissions'])->orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get()->groupBy(fn ($p) => explode('.', $p->name)[0]);

        return view('roles.index', ['title' => 'Roles & Permissions', 'roles' => $roles, 'permissions' => $permissions]);
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->can('roles.create'), 403);

        $permissions = Permission::orderBy('name')->get()->groupBy(fn ($p) => explode('.', $p->name)[0]);

        return view('roles.create', ['title' => 'Tambah Role', 'permissions' => $permissions]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->can('roles.create'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role = Role::create(['name' => $validated['name'], 'guard_name' => 'web']);
        $role->syncPermissions($validated['permissions'] ?? []);

        AuditLogService::log('create', 'roles', $role->id, null, $role->toArray());

        return redirect()->route('roles.index')->with('success', 'Role berhasil ditambahkan.');
    }

    public function edit(Request $request, Role $role): View
    {
        abort_unless($request->user()->can('roles.update'), 403);

        $permissions = Permission::orderBy('name')->get()->groupBy(fn ($p) => explode('.', $p->name)[0]);
        $rolePermissionIds = $role->permissions->pluck('id')->toArray();

        return view('roles.edit', ['title' => 'Edit Role', 'role' => $role, 'permissions' => $permissions, 'rolePermissionIds' => $rolePermissionIds]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        abort_unless($request->user()->can('roles.update'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:roles,name,'.$role->id],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $old = $role->toArray();
        $role->update(['name' => $validated['name']]);
        $role->syncPermissions($validated['permissions'] ?? []);

        AuditLogService::log('update', 'roles', $role->id, $old, $role->toArray());

        return redirect()->route('roles.index')->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(Request $request, Role $role): RedirectResponse
    {
        abort_unless($request->user()->can('roles.delete'), 403);

        if ($role->users()->exists()) {
            return back()->with('error', 'Role tidak dapat dihapus karena masih digunakan oleh user.');
        }

        if (in_array($role->name, ['Super Admin', 'Owner', 'Tenant'])) {
            return back()->with('error', 'Role bawaan sistem tidak dapat dihapus.');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role berhasil dihapus.');
    }
}
