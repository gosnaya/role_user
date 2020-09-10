<?php

namespace App\Http\Controllers\Backend\Role_User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Role_User\Models\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('haveaccess', 'user.index');

        //Con el with trae el usuario con pivot es decir con los roles que tiene el usuario
        $users = User::with('roles')->orderBy('id', 'Desc')->paginate(2);


        return view('user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        Gate::authorize('view', [$user, ['user.show', 'userown.show']]);

        $roles = Role::orderBy('name')->get();

        return view('user.show', compact('user', 'roles'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        Gate::authorize('update', [$user, ['user.edit', 'userown.edit']]);

        $roles = Role::orderBy('name')->get();

        return view('user.edit', [
            'user' => $user,
            'roles' => $roles
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, User $user)
    {
        $this->authorize('update', [$user, ['user.edit', 'userown.edit']]);
        //Gate::authorize('haveaccess', 'user.edit');

        $user->update([
            'name' => $request->name,
            'email' => $request->email
        ]);



        if ($request->get('roles')) {
            $user->roles()->sync($request->get('roles'));
        }


        return redirect()->route('user.show', $user->id)->with('status_success', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        Gate::authorize('haveaccess', 'user.destroy');
        $user->delete();
        return redirect()->route('user.index')->with('status_success', 'User deleted successfully');
    }
}
