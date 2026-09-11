<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateRoleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::query()
            ->with('permissions')
            ->withCount('users')
            ->orderBy('name')
            ->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function edit(Role $role): View
    {
        $this->protectSuperAdmin($role);

        $permissions = Permission::query()
            ->orderBy('name')
            ->get()
            ->groupBy(fn($permission) => str($permission->name)->before('.')->toString());

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(
        UpdateRoleRequest $request,
        Role $role
    ): RedirectResponse {
        $this->protectSuperAdmin($role);

        $role->syncPermissions(
            $request->validated('permissions', [])
        );

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role permissions updated successfully.');
    }

    private function protectSuperAdmin(Role $role): void
    {
        if ($role->name === 'super_admin') {
            abort(403);
        }
    }
}