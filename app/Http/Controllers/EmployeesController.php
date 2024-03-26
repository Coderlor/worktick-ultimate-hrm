<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\EmployeeExperience;
use App\Models\EmployeeDocument;
use App\Models\EmployeeAccount;
use App\Models\Department;
use App\Models\OfficeShift;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Award;
use App\Models\Travel;
use App\Models\Complaint;
use App\Models\Project;
use App\Models\Task;
use App\Models\Training;
use Carbon\Carbon;
use Illuminate\Auth\Access\Gate;
use Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;

class EmployeesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $user_auth = auth()->user();
		if ($user_auth->can('employee_view')){

            $employees = Employee::with('company:id,name','office_shift:id,name','department:id,department','designation:id,designation')
            ->where('deleted_at', '=', null)
            ->where('leaving_date' , NULL)
            ->get();
            return view('employee.employee_list', compact('employees'));
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
        if($user_auth->can('employee_add')){  

            $roles = Role::where('deleted_at', '=', null)->get(['id','name']);
            $companies = Company::where('deleted_at', '=', null)->get(['id','name']);
            return view('employee.create_employee', compact('companies','roles'));
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
        if($user_auth->can('employee_add')){

            $this->validate($request, [
                'firstname'      => 'required|string|max:255',
                'lastname'       => 'required|string|max:255',
                'country'        => 'required|string|max:255',
                'gender'         => 'required',
                'phone'          => 'required',
                'company_id'     => 'required',
                'department_id'  => 'required',
                'designation_id' => 'required',
                'office_shift_id'   => 'required',
                'role_users_id'   => 'required',
                'email'     => 'required|string|email|max:255|unique:users',
                'password'  => 'required|string|min:6|confirmed',
                'password_confirmation' => 'required',
            ], [
                'email.unique' => 'This Email already taken.',
            ]);
          
            $data = [];
            $data['firstname'] = $request['firstname'];
            $data['lastname'] = $request['lastname'];
            $data['username'] = $request['firstname'] .' '.$request['lastname'];
            $data['country'] = $request['country'];
            $data['email'] = $request['email'];
            $data['gender'] = $request['gender'];
            $data['phone'] = $request['phone'];
            $data['birth_date'] = $request['birth_date'];
            $data['company_id'] = $request['company_id'];
            $data['department_id'] = $request['department_id'];
            $data['designation_id'] = $request['designation_id'];
            $data['office_shift_id'] = $request['office_shift_id'];
            $data['joining_date'] = $request['joining_date'];
            $data['role_users_id'] = $request['role_users_id'];
            
            $user_data = [];
            $user_data['username'] = $request['firstname'] .' '.$request['lastname'];
            $user_data['email'] = $request->email;
            $user_data['avatar'] = 'no_avatar.png';
            $user_data['password'] = Hash::make($request['password']);
            $user_data['status'] = 1;
            $user_data['role_users_id'] = $request['role_users_id'];

            \DB::transaction(function () use ($request , $user_data , $data) {

                $user = User::create($user_data);
                $user->syncRoles($request['role_users_id']);

                $data['id'] = $user->id;
                $data['user_id'] = Auth::user()->id;
        
                Employee::create($data);

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
        $user_auth = auth()->user();
        if($user_auth->can('employee_details')){

            $employee = Employee::where('deleted_at', '=', null)->findOrFail($id);
            $experiences = EmployeeExperience::where('employee_id' , $id)->where('deleted_at', '=', null)->orderBy('id', 'desc')->get();
            $documents = EmployeeDocument::where('employee_id' , $id)->where('deleted_at', '=', null)->orderBy('id', 'desc')->get();
            $accounts_bank = EmployeeAccount::where('employee_id' , $id)->where('deleted_at', '=', null)->orderBy('id', 'desc')->get();
            $companies = Company::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','name']);
            $office_shifts = OfficeShift::where('company_id' , $employee->company_id)->where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','name']);
            $departments = Department::where('company_id' , $employee->company_id)->where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','department']);
            $designations = Designation::where('department_id' , $employee->department_id)->where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','designation']);
            $roles = Role::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','name']);

            $leaves = Leave::where('employee_id' , $id)
            ->join('companies','companies.id','=','leaves.company_id')
            ->join('departments','departments.id','=','leaves.department_id')
            ->join('employees','employees.id','=','leaves.employee_id')
            ->join('leave_types','leave_types.id','=','leaves.leave_type_id')
            ->where('leaves.deleted_at' , '=', null)
            ->select('leaves.*',
                'employees.username AS employee_name', 'employees.id AS employee_id',
                'leave_types.title AS leave_type_title', 'leave_types.id AS leave_type_id',
                'companies.name AS company_name', 'companies.id AS company_id',
                'departments.department AS department_name', 'departments.id AS department_id')
            ->orderBy('id', 'desc')
            ->get();

            $awards = Award::where('employee_id' , $id)
            ->join('companies','companies.id','=','awards.company_id')
            ->join('departments','departments.id','=','awards.department_id')
            ->join('employees','employees.id','=','awards.employee_id')
            ->join('award_types','award_types.id','=','awards.award_type_id')
            ->where('awards.deleted_at' , '=', null)
            ->select('awards.*',
            'employees.username AS employee_name', 'employees.id AS employee_id',
            'award_types.title AS award_type_title', 'award_types.id AS award_type_id',
            'companies.name AS company_name', 'companies.id AS company_id',
            'departments.department AS department_name', 'departments.id AS department_id')
            ->orderBy('id', 'desc')
            ->get();

            $travels = Travel::where('employee_id' , $id)
            ->with('company:id,name','employee:id,username','arrangement_type:id,title')
            ->where('deleted_at', '=', null)
            ->orderBy('id', 'desc')
            ->get();

            $complaints = Complaint::where('employee_from' , $id)
            ->with('company:id,name','EmployeeFrom:id,username','EmployeeAgainst:id,username')
            ->where('deleted_at', '=', null)
            ->orderBy('id', 'desc')
            ->get();

            $tasks = Task::where('deleted_at', '=', null)
            ->with('company:id,name','project:id,title','assignedEmployees')
            ->join('employee_task','tasks.id','=','employee_task.task_id')
            ->where('employee_id', $id)
            ->orderBy('id', 'desc')
            ->get();

            $projects = Project::where('deleted_at', '=', null)
            ->with('company:id,name','client:id,username','assignedEmployees')
            ->join('employee_project','projects.id','=','employee_project.project_id')
            ->where('employee_id', $id)
            ->orderBy('id', 'desc')
            ->get();

            $trainings = Training::where('deleted_at', '=', null)
            ->with('company:id,name','trainer:id,name','TrainingSkill:id,training_skill','assignedEmployees')
            ->join('employee_training','trainings.id','=','employee_training.training_id')
            ->where('employee_id', $id)
            ->orderBy('id', 'desc')
            ->get();

            return view('employee.employee_details',
                compact('employee','companies','departments','designations','roles','documents',
                            'office_shifts','experiences','accounts_bank','leaves','awards','travels','complaints',
                            'tasks','projects','trainings')
                        );
        }
        return abort('403', __('You are not authorized'));
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
        if($user_auth->can('employee_edit')){
            $employee = Employee::where('deleted_at', '=', null)->findOrFail($id);
            $roles = Role::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','name']);
            $companies = Company::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','name']);
            $office_shifts = OfficeShift::where('company_id' , $employee->company_id)->where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','name']);
            $departments = Department::where('company_id' , $employee->company_id)->where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','department']);
            $designations = Designation::where('department_id' , $employee->department_id)->where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','designation']);
            return view('employee.edit_employee', compact('employee','companies','departments','designations','office_shifts','roles'));
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
        if($user_auth->can('employee_edit')){

            $this->validate($request, [
                'firstname'      => 'required|string|max:255',
                'lastname'       => 'required|string|max:255',
                'country'        => 'required|string|max:255',
                'gender'         => 'required',
                'phone'          => 'required',
                'total_leave'    => 'required|numeric|min:0',
                'company_id'     => 'required',
                'department_id'  => 'required',
                'designation_id' => 'required',
                'office_shift_id'   => 'required',
                'email'     => 'required|string|email|max:255|unique:users',
                'email'          => Rule::unique('users')->ignore($id),
                'role_users_id'   => 'required',
                'basic_salary'          => 'nullable|numeric',
                'hourly_rate'          => 'nullable|numeric',
            ], [
                'email.unique' => 'This Email already taken.',
            ]);

           
            $data = [];
            $data['firstname'] = $request['firstname'];
            $data['lastname'] = $request['lastname'];
            $data['username'] = $request['firstname'] .' '.$request['lastname'];
            $data['country'] = $request['country'];
            $data['email'] = $request['email'];
            $data['gender'] = $request['gender'];
            $data['phone'] = $request['phone'];
            $data['birth_date'] = $request['birth_date'];
            $data['company_id'] = $request['company_id'];
            $data['department_id'] = $request['department_id'];
            $data['designation_id'] = $request['designation_id'];
            $data['office_shift_id'] = $request['office_shift_id'];
            $data['joining_date'] = $request['joining_date'];
            $data['role_users_id'] = $request['role_users_id'];
            $data['leaving_date'] = $request['leaving_date']?$request['leaving_date']:NULL;
            $data['marital_status'] = $request['marital_status'];
            $data['employment_type'] = $request['employment_type'];
            $data['city'] = $request['city'];
            $data['province'] = $request['province'];
            $data['zipcode'] = $request['zipcode'];
            $data['address'] = $request['address'];
            $data['basic_salary'] = $request['basic_salary'];
            $data['hourly_rate'] = $request['hourly_rate'];

            //calculation of total_leave & remaining_leave
            $employee_leave_info = Employee::find($id);
            if($employee_leave_info->total_leave == 0)
            {
                $data['total_leave'] = $request->total_leave;
		        $data['remaining_leave'] = $request->total_leave;
            }
            elseif($request->total_leave > $employee_leave_info->total_leave ){
                $data['total_leave'] = $request->total_leave;
		        $data['remaining_leave'] = $request->remaining_leave + ($request->total_leave - $employee_leave_info->total_leave);
            }
 	        elseif($request->total_leave < $employee_leave_info->total_leave ){
                $data['total_leave'] = $request->total_leave;
		        $data['remaining_leave'] = $request->remaining_leave - ($employee_leave_info->total_leave - $request->total_leave);
 
            }else{
                $data['total_leave'] = $request->total_leave;
                $data['remaining_leave'] = $employee_leave_info->remaining_leave;
            }
            
            $user_data = [];
            $user_data['username'] = $request['firstname'] .' '.$request['lastname'];
            $user_data['email'] = $request->email;
            // $user_data['password'] = Hash::make($request['password']);
            $user_data['role_users_id'] = $request['role_users_id'];

            \DB::transaction(function () use ($request , $id , $user_data , $data) {

                User::whereId($id)->update($user_data);
                Employee::find($id)->update($data);
                
                $user = User::find($id);
                $user->syncRoles($data['role_users_id']);

            }, 10);
         
            return response()->json(['success' => true]);
        }
        return abort('403', __('You are not authorized'));
    }


    public function update_social_profile(Request $request, $id)
    {
        $user_auth = auth()->user();
        if($user_auth->can('employee_edit')){
       
            Employee::whereId($id)->update($request->all());

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
        if($user_auth->can('employee_delete')){

            Employee::whereId($id)->update([
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
        if($user_auth->can('employee_delete')){
            $selectedIds = $request->selectedIds;
    
            foreach ($selectedIds as $employee_id) {
                Employee::whereId($employee_id)->update([
                    'deleted_at' => Carbon::now(),
                ]);
    
                User::whereId($employee_id)->update([
                    'status' => 0,
                ]);
            }
            return response()->json(['success' => true]);
        }
        return abort('403', __('You are not authorized'));
     }
 


    public function Get_all_employees()
    {
        $employees = Employee::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','username']);

        return response()->json($employees);
    }

      
    public function Get_employees_by_company(Request $request)
    {
        $employees = Employee::where('company_id' , $request->id)->where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','username']);

        return response()->json($employees);
    }

          
    public function Get_employees_by_department(Request $request)
    {
        $employees = Employee::where('department_id' , $request->id)->where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','username']);

        return response()->json($employees);
    }


    public function Get_office_shift_by_company(Request $request)
    {
        $office_shifts = OfficeShift::where('company_id' , $request->id)->where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','name']);

        return response()->json($office_shifts);
    }
}
