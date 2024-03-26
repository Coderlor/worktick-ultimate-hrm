<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use DB;
use Auth;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_auth = auth()->user();
		if ($user_auth->can('client_view')){

            $clients = Client::where('deleted_at', '=', null)
            ->orderBy('id', 'desc')
            ->get();

            return view('client.client_list', compact('clients'));

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
        $user_auth = auth()->user();
		if ($user_auth->can('client_add')){

            request()->validate([
                'firstname' => 'required|string|max:255',
                'lastname'  => 'required|string|max:255',
                'email'     => 'required|string|email|max:255|unique:users',
                'password'  => 'required|string|min:6',
                'phone'     => 'nullable',
                'country'   => 'nullable|string|max:255',
                'city'      => 'nullable|string|max:255',
                'address'   => 'nullable|string|max:255',
                
            ]);

            $data = [];
            $data['code'] = $this->getNumberOrder();
            $data['firstname'] = $request['firstname'];
            $data['lastname'] = $request['lastname'];
            $data['username'] = $request['firstname'] .' '.$request['lastname'];
            $data['country'] = $request['country'];
            $data['city'] = $request['city'];
            $data['address'] = $request['address'];
            $data['email'] = $request['email'];
            $data['phone'] = $request['phone'];
            $data['role_users_id'] = 3;
            
            $user_data = [];
            $user_data['username'] = $request['firstname'] .' '.$request['lastname'];
            $user_data['email'] = $request->email;
            $user_data['avatar'] = 'no_avatar.png';
            $user_data['password'] = Hash::make($request['password']);
            $user_data['status'] = 1;
            $user_data['role_users_id'] = 3;


            \DB::transaction(function () use ($request , $user_data , $data) {

                $user = User::create($user_data);
                $user->syncRoles(3);

                $data['id'] = $user->id;
                $data['user_id'] = Auth::user()->id;
        
                Client::create($data);

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
        //
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
		if ($user_auth->can('client_edit')){

            $this->validate($request, [
                'firstname' => 'required|string|max:255',
                'lastname'  => 'required|string|max:255',
                'email'     => 'required|string|email|max:255|unique:users',
                'email'     => Rule::unique('users')->ignore($id),
                'phone'     => 'nullable',
                'country'   => 'nullable|string|max:255',
                'city'      => 'nullable|string|max:255',
                'address'   => 'nullable|string|max:255',
            ], [
                'email.unique' => 'This Email already taken.',
            ]);

            $data = [];
            $data['firstname'] = $request['firstname'];
            $data['lastname'] = $request['lastname'];
            $data['username'] = $request['firstname'] .' '.$request['lastname'];
            $data['country'] = $request['country'];
            $data['city'] = $request['city'];
            $data['address'] = $request['address'];
            $data['email'] = $request['email'];
            $data['phone'] = $request['phone'];
            $data['role_users_id'] = 3;
            
            $user_data = [];
            $user_data['username'] = $request['firstname'] .' '.$request['lastname'];
            $user_data['email'] = $request->email;
            $user_data['role_users_id'] = 3;

            \DB::transaction(function () use ($request , $id , $user_data , $data) {

                User::whereId($id)->update($user_data);
                Client::find($id)->update($data);
                
                $user = User::find($id);
                $user->syncRoles(3);

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
		if ($user_auth->can('client_delete')){

            Client::whereId($id)->update([
                'deleted_at' => Carbon::now(),
            ]);

            User::whereId($id)->update([
                'status' => 0,
            ]);

            return response()->json(['success' => true]);

        }
        return abort('403', __('You are not authorized'));
    }

     //-------------- Delete by selection  ---------------\\

     public function delete_by_selection(Request $request)
     {
        $user_auth = auth()->user();
        if($user_auth->can('client_delete')){
            $selectedIds = $request->selectedIds;
    
            foreach ($selectedIds as $client_id) {
                Client::whereId($client_id)->update([
                    'deleted_at' => Carbon::now(),
                ]);
    
                User::whereId($client_id)->update([
                    'status' => 0,
                ]);
            }
            return response()->json(['success' => true]);
        }
        return abort('403', __('You are not authorized'));
     }


    
    //------------- get Number Order Customer -------------\\

    public function getNumberOrder()
    {
        $last = DB::table('clients')->latest('id')->first();

        if ($last) {
            $code = $last->code + 1;
        } else {
            $code = 1;
        }
        return $code;
    }


    //------------- client_projects -------------\\

    public function client_projects_index()
    {
        $user_auth = auth()->user();
        if ($user_auth->role_users_id == 3){
            $projects = Project::where('deleted_at', '=', null)
            ->where('client_id', $user_auth->id)
            ->orderBy('id', 'desc')
            ->get();
            return view('client.client_projects', compact('projects'));
        }
        return abort('403', __('You are not authorized'));

    }

    //------------- client_projects_show_create_modal -------------\\

    public function client_projects_create()
     {
        $user_auth = auth()->user();
        if ($user_auth->role_users_id == 3){
            
            $companies = Company::where('deleted_at', '=', null)
            ->orderBy('id', 'desc')
            ->get(['id','name']);

            return response()->json($companies);
        }
        return abort('403', __('You are not authorized'));
    }

    public function client_projects_store(Request $request)
    {
        $user_auth = auth()->user();
		if ($user_auth->role_users_id == 3){

            $request->validate([
                'title'           => 'required|string|max:255',
                'summary'         => 'required|string|max:255',
                'company_id'      => 'required',
                'start_date'      => 'required',
                'end_date'        => 'required',
                'priority'        => 'required',
            ]);

           Project::create([
                'title'            => $request['title'],
                'summary'          => $request['summary'],
                'start_date'       => $request['start_date'],
                'end_date'         => $request['end_date'],
                'company_id'       => $request['company_id'],
                'client_id'        => $user_auth->id,
                'priority'         => $request['priority'],
                'status'           => 'not_started',
                'project_progress' => '0',
                'description'      => $request['description'],
            ]);


             return response()->json(['success' => true]);

        }
        return abort('403', __('You are not authorized'));
    }


       //------------- client_tasks_index -------------\\

    public function client_tasks_index()
    {
        $user_auth = auth()->user();
        if ($user_auth->role_users_id == 3){

            $client =  Client::with('projects')->findOrFail($user_auth->id);
            $projects =  $client->projects;
            $project_id = $projects->pluck('id');
            
            $tasks = Task::where('deleted_at', '=', null)
            ->whereIn('project_id' , $project_id)
            ->orderBy('id', 'desc')
            ->get();
            
            return view('client.client_tasks', compact('tasks'));
        }
        return abort('403', __('You are not authorized'));
    }

       //------------- client_tasks_show_create_modal -------------\\

       public function client_tasks_create()
       {
        $user_auth = auth()->user();
        if ($user_auth->role_users_id == 3){
            $companies = Company::where('deleted_at', '=', null)
            ->orderBy('id', 'desc')
            ->get(['id','name']);

            $projects = Project::where('deleted_at', '=', null)
            ->where('client_id', $user_auth->id)
            ->orderBy('id', 'desc')
            ->get(['id','title']);

            return response()->json([
                'companies' => $companies,
                'projects' => $projects,
            ]);
        }
        return abort('403', __('You are not authorized'));
  
      }

      public function client_tasks_store(Request $request)
      {
          $user_auth = auth()->user();
          if ($user_auth->role_users_id == 3){
  
              $request->validate([
                  'title'           => 'required|string|max:255',
                  'summary'         => 'required|string|max:255',
                  'project_id'      => 'required',
                  'start_date'      => 'required',
                  'end_date'        => 'required',
                  'company_id'      => 'required',
                  'priority'          => 'required',
              ]);
  
              Task::create([
                  'title'            => $request['title'],
                  'summary'          => $request['summary'],
                  'start_date'       => $request['start_date'],
                  'end_date'         => $request['end_date'],
                  'project_id'       => $request['project_id'],
                  'company_id'       => $request['company_id'],
                  'status'           => 'not_started',
                  'priority'         => $request['priority'],
                  'task_progress'    => '0',
                  'description'      => $request['description'],
              ]);
    
              return response()->json(['success' => true]);
  
          }
          return abort('403', __('You are not authorized'));
      }

}
