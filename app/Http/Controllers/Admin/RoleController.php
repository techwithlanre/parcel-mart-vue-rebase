<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
class RoleController extends Controller
{

    public function index()
    {
        $roles = Role::all();
        return Inertia::render('Admin/Roles/Index', compact('roles'));
    }


    public function create()
    {
        return Inertia::render('Admin/Roles/Create');
    }


    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'name' => 'required|unique:roles,name'
        ])->validate();

        Role::create($request->all());
        return redirect(route('roles.index'))->with('message', 'Role created successfully');
    }

    public function show(string $id)
    {
        $all_permissions = Permission::all();
        $permissions = [];
        $role = Role::find($id);
        foreach ($all_permissions as $ap) $permissions[$ap->name] = $role->hasPermissionTo($ap->name);
        return Inertia::render('Admin/Roles/Permissions', compact('permissions', 'id', 'role'));
    }

    public function edit(string $id)
    {
        $role = Role::find($id);
        return Inertia::render('Admin/Roles/Edit', compact('role'));
    }


    public function update(Request $request, string $id)
    {
        Validator::make($request->all(), [
            'name' => ['required']
        ])->validate();

        $role = Role::find($id);
        $role->name = $request->name;
        $role->save();
        return redirect(route('roles.index'))->with('message', 'Role updated successfully');
    }

    public function destroy(string $id)
    {
        //
    }

    public function updatePermissions(Request $request, string $id)
    {
        $role = Role::find($id);
        $permissions = $request->all();
        foreach ($permissions as $k => $v)  ($v === true) ? $role->givePermissionTo($k) : $role->revokePermissionTo($k);
        return redirect(route('roles.show', $id))->with('message', 'Permissions updated');
    }
}
