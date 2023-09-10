<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateAdminUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with(['wallet', 'roles'])->latest()->paginate(10);
        $roles = Role::where('name','!=', 'admin')->get();
        return Inertia::render('Admin/Users/Users', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return Inertia::render('Admin/Users/Create',compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateAdminUserRequest $request)
    {
        $data = $request->validated();
        $user = new User;
        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'];
        $user->password = bcrypt($data['password']);
        $user->is_admin = 1;
        $user->user_type = 'individual';
        $user->save();
        $role = Role::find($data['role']);
        $user->assignRole($role);
        return redirect(route('users.index'))->with('message', 'Admin user created');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function setCreditLimit(Request $request, $user_id)
    {
        $user = User::find($user_id);
        $user->credit_limit = $request->amount;
        $user->save();
        return redirect(route('users.index'))->with('message', 'Credit limit has been set for this business account');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function changeUserRole(Request $request, $user_id): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        $user = User::find($user_id);
        $user->removeRole($user->roles[0]);
        $new_role = Role::find($request->role);
        $user->assignRole($new_role);
        return redirect(route('users.index'))->with('message', 'User role has been changed');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
