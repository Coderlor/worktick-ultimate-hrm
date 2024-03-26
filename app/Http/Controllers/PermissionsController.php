<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use \Nwidart\Modules\Facades\Module;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class PermissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_auth = auth()->user();
		if ($user_auth->can('group_permission')){

            $roles = Role::where('deleted_at', '=', null)->orderBy('id', 'desc')->get();
            return view('settings.permissions.permissions_list', compact('roles'));

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
		if ($user_auth->can('group_permission')){

            $allEnabledModules = Module::allEnabled();
            $ModulesEnabled = [];
            foreach($allEnabledModules as $key => $modules_name){
                $ModulesEnabled[] = $key;
            }

            return view('settings.permissions.create_permission',compact('ModulesEnabled'));

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
		if ($user_auth->can('group_permission')){

            \DB::transaction(function () use ($request) {

                $request->validate([
                    'name' => 'required|string|max:255'
                ]);

                //-- Create New Role
                $Role = new Role;
                $Role->name = $request['name'];
                $Role->guard_name = 'web';
                $Role->description = $request['description'];
                $Role->save();

                $get_role = Role::findOrFail($Role->id);
                $all_permissions = $request['permissions'];

                app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

                $get_role->syncPermissions($all_permissions);
            
            }, 10);

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
		if ($user_auth->can('group_permission')  && $id != 1 && $id != 2 && $id != 3){

            $Role = Role::where('deleted_at', '=', null)->with('permissions')->findOrFail($id);
            if ($Role) {
                $role['id'] = $Role->id;
                $role['name'] = $Role->name;
                $role['description'] = $Role->description;
                $permissions = [];
                if ($Role) {
                    foreach ($Role->permissions as $permission) {
                        $permissions[] = $permission->name;
                    }
                }
            }

            $allEnabledModules = Module::allEnabled();
            $ModulesEnabled = [];
            foreach($allEnabledModules as $key => $modules_name){
                $ModulesEnabled[] = $key;
            }

            return view('settings.permissions.edit_permission', compact('permissions','role','ModulesEnabled'));

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
		if ($user_auth->can('group_permission') && $id != 1 && $id != 2 && $id != 3){

            request()->validate([
                'name' => 'required',
            ]);

            \DB::transaction(function () use ($request, $id) {

                Role::whereId($id)->update([
                    'name'           => $request['name'],
                    'description'    => $request['description'],
                ]);
              

                $get_role = Role::findOrFail($id);
                $all_permissions = $request['permissions'];
                app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
                $get_role->syncPermissions($all_permissions);

            }, 10);

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
		if ($user_auth->can('group_permission') && $id != 1 && $id != 2 && $id != 3){

            Role::whereId($id)->update([
                'deleted_at' => Carbon::now(),
            ]);
            return response()->json(['success' => true]);

        }
        return abort('403', __('You are not authorized'));
        
    }

}
