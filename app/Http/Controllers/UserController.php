<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use File;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_auth = auth()->user();
		if ($user_auth->can('user_view')){

            $roles = Role::where('deleted_at', '=', null)->where('id', '!=' , 3)->get(['id','name']);
            $users = User::with('RoleUser')->orderBy('id', 'desc')->get();

            return view('user.user_list', compact('users','roles'));

        }
        return abort('403', __('You are not authorized'));

    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user_auth = auth()->user();
		if ($user_auth->can('user_add')){

            $roles = Role::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','name']);
            return response()->json($roles);

        }
        return abort('403', __('You are not authorized'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user_auth = auth()->user();
		if ($user_auth->can('user_add')){

            $request->validate([
                'username'  => 'required|string|max:255',
                'email'     => 'required|string|email|max:255|unique:users',
                'password'  => 'required|string|min:6|confirmed',
                'password_confirmation' => 'required',
                'avatar'    => 'nullable|image|mimes:jpeg,png,jpg,bmp,gif,svg|max:2048',
                'status'    => 'required',
            ]);

            if ($request->hasFile('avatar')) {


                $image = $request->file('avatar');
                $filename = time().'.'.$image->extension();  
                $image->move(public_path('/assets/images/avatar'), $filename);

            } else {
                $filename = 'no_avatar.png';
            }

            $user = User::create([
                'username'  => $request['username'],
                'email'     => $request['email'],
                'avatar'    => $filename,
                'password'  => Hash::make($request['password']),
                'role_users_id'   => 1,
                'status'    => $request['status'],
            ]);

            $user->assignRole(1);

            return response()->json(['success' => true]);

        }
        return abort('403', __('You are not authorized'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user_auth = auth()->user();
		if ($user_auth->can('user_edit')){

            $roles = Role::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','name']);
            return response()->json($roles);

        }
        return abort('403', __('You are not authorized'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user_auth = auth()->user();
		if ($user_auth->can('user_edit')){

            $this->validate($request, [
                'email' => 'required|string|email|max:255|unique:users',
                'email' => Rule::unique('users')->ignore($id),
                'username'  => 'required|string|max:255',
                'avatar'    => 'nullable|image|mimes:jpeg,png,jpg,bmp,gif,svg|max:2048',
                'password'  =>  'sometimes|nullable|string|confirmed|min:6,'.$id,
                'status'    => 'required',
            ], [
                'email.unique' => 'This Email already taken.',
            ]);

            $user = User::findOrFail($id);
            $current = $user->password;

            if ($request->password != null) {
                if ($request->password != $current) {
                    $pass = Hash::make($request->password);
                } else {
                    $pass = $user->password;
                }

            } else {
                $pass = $user->password;
            }

            $currentAvatar = $user->avatar;
            if ($request->avatar != null) {
                if ($request->avatar != $currentAvatar) {

                    $image = $request->file('avatar');
                    $filename = time().'.'.$image->extension();  
                    $image->move(public_path('/assets/images/avatar'), $filename);
                    $path = public_path() . '/assets/images/avatar';
                    $userPhoto = $path . '/' . $currentAvatar;
                    if (file_exists($userPhoto)) {
                        if ($user->avatar != 'no_avatar.png') {
                            @unlink($userPhoto);
                        }
                    }
                } else {
                    $filename = $currentAvatar;
                }
            }else{
                $filename = $currentAvatar;
            }


            $user = User::whereId($id)->update([
                'username'  => $request['username'],
                'email'     => $request['email'],
                'avatar'    => $filename,
                'password'  => $pass,
                'status'    => $request['status'],
            ]);

            return response()->json(['success' => true]);

        }
        return abort('403', __('You are not authorized'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user_auth = auth()->user();
		if ($user_auth->can('user_delete')){

            User::whereId($id)->update([
                'status' => 0,
            ]);

            return response()->json(['success' => true]);

        }
        return abort('403', __('You are not authorized'));
    }


    public function assignRole(Request $request)
    {
        $user_auth = auth()->user();
        if ($user_auth->can('group_permission') && $user_auth->role_users_id == 1 && $request->role_id != 3){
            
            //remove role
            $get_user = User::find($request->user_id);
            $get_user->removeRole($get_user->role_users_id);

            User::whereId($request->user_id)->update([
                'role_users_id' => $request->role_id,
            ]);

            $user_updated = User::find($request->user_id);
            $user_updated->assignRole($request->role_id);

            return response()->json(['success' => true]);

        }
        return abort('403', __('You are not authorized'));
    }





     // Factory data
    //  public function getAllPermissions()
    //  {
    //      $user_auth = auth()->user();
    //      $user_auth->assignRole(1);
 
    //      $all_permissions  = Permission::pluck('name');
    //      $role = Role::find(1);
    //      $role->syncPermissions($all_permissions);
    //  }
 
}
