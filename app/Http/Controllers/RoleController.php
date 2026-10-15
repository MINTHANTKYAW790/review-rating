<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('superadmin');
    }

    public function index(): View
    {
        $this->authorize('viewAny', Role::class);

        $roles = Role::query()
            ->when(auth()->user()->hasRole('admin'), function ($query) {
                $query->where('name', '!=', 'superadmin');
            })
            ->get();

        return view('roles.index', compact('roles'));
    }

    public function create(): View
    {
        $this->authorize('create', Role::class);

        $permissions = Permission::all()->groupBy(function ($permission) {
            return str_contains($permission->name, 'dashboard') ? 'System'
                : (str_contains($permission->name, 'roles') ? 'Roles'
                : (str_contains($permission->name, 'users') ? 'Users'
                : (str_contains($permission->name, 'stores') ? 'Stores'
                : (str_contains($permission->name, 'staff') ? 'Staff'
                : ((str_contains($permission->name, 'plan') || str_contains($permission->name, 'payment') || str_contains($permission->name, 'subscription')) ? 'Billing'
                : 'Reviews')))));
        });

        return view('roles.create', compact('permissions'));
    }

    public function store(RoleRequest $request): RedirectResponse
    {
        $this->authorize('create', Role::class);

        if (auth()->user()->hasRole('admin') && $request->name === 'superadmin') {
            abort(403, 'You cannot create the superadmin role.');
        }

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($request->permissions ?? []);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($role)
            ->event('created')
            ->log('Role created: '.$role->name);

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    public function show(Role $role): View
    {
        $this->authorize('view', $role);

        return view('roles.show', compact('role'));
    }

    public function edit(Role $role): View
    {
        $this->authorize('update', $role);

        $permissions = Permission::all()->groupBy(function ($permission) {
            return str_contains($permission->name, 'dashboard') ? 'System'
                : (str_contains($permission->name, 'roles') ? 'Roles'
                : (str_contains($permission->name, 'users') ? 'Users'
                : (str_contains($permission->name, 'stores') ? 'Stores'
                : (str_contains($permission->name, 'staff') ? 'Staff'
                : ((str_contains($permission->name, 'plan') || str_contains($permission->name, 'payment') || str_contains($permission->name, 'subscription')) ? 'Billing'
                : 'Reviews')))));
        });

        return view('roles.edit', compact('role', 'permissions'));
    }

    public function update(RoleRequest $request, Role $role): RedirectResponse
    {
        $this->authorize('update', $role);

        $role->update([
            'name' => $request->name,
        ]);

        $role->syncPermissions($request->permissions ?? []);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($role)
            ->event('updated')
            ->log('Role updated: '.$role->name);

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->authorize('delete', $role);

        $roleName = $role->name;
        $role->delete();

        activity()
            ->causedBy(auth()->user())
            ->event('deleted')
            ->log('Role deleted: '.$roleName);

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }
}
