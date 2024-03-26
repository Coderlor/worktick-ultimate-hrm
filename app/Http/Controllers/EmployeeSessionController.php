<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use File;
use DB;
use App\Models\EmployeeProject;
use App\Models\EmployeeTask;
use App\Models\Complaint;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Company;
use App\Models\Designation;
use App\Models\EmployeeExperience;
use App\Models\EmployeeDocument;
use App\Models\EmployeeAccount;
use App\Models\Department;
use App\Models\OfficeShift;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Award;
use App\Models\Travel;
use App\Models\Project;
use App\Models\Task;
use App\Models\Training;
use Carbon\Carbon;
use DateTime;
use Exception;

class EmployeeSessionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function attendance_by_employee(Request $request , $id)
    {
        
        $last_attendance =   Attendance::where('deleted_at', '=', null)
        ->where('date' , now()->format('Y-m-d'))
        ->where('employee_id', $id)
        ->orderBy('id','desc')->first() ?? null;
        $employee = Employee::findOrFail($id);
        $data_attendance = [];
        
        $current_day = now()->format('Y-m-d');
            try{
                $punch_in  = new DateTime(substr($request->office_punch_in, 0, -2));
                $punch_out  = new DateTime(substr($request->office_punch_out, 0, -2));
                $time_now = new DateTime(now());
            
            }catch(Exception $e){
                return $e;
            }

            $data['employee_id']  = $id;
            $data['date']  = $current_day;
            $data['company_id']  = $employee->company_id;

            if(!$last_attendance){
                if($time_now > $punch_in){
                    $data['clock_in'] = $time_now->format('H:i');
                    $time_diff = $punch_in->diff( new DateTime($data['clock_in']))->format('%H:%I');
                    $data['late_time'] = $time_diff;
                }
                else{
                    $data['clock_in'] = $punch_in->format('H:i');
                }

                $data['status'] = 'present';
                $data['clock_in_out'] = 1;
                $data['clock_in_ip'] = $request->ip();

                //create new attendance
                Attendance::create($data);
                return redirect()->back();
            }
            else{
                if($last_attendance->clock_in_out == 1){
                    $last_clock_in = new DateTime($last_attendance->clock_in);
                    if($time_now < $punch_out){
                        $data['clock_out'] = $time_now->format('H:i');
                        $time_diff = $punch_out->diff( new DateTime($data['clock_out']))->format('%H:%I');
                        $data['depart_early'] = $time_diff;
                    }
                    elseif($time_now > $punch_out){
                        $data['clock_out'] = $time_now->format('H:i');
                        if($last_clock_in > $punch_out){
                            $time_diff = $last_clock_in->diff( new DateTime($data['clock_out']))->format('%H:%I');
                        }else{
                            $time_diff = $punch_out->diff( new DateTime($data['clock_out']))->format('%H:%I');
                        }
                        $data['overtime'] = $time_diff;
                    }
                    else{
                        $data['clock_out'] = $punch_out->format('H:i');
                    }
                    $data['clock_in_ip'] = $request->ip();

                    $work_duration = $last_clock_in->diff(new DateTime($data['clock_out']))->format('%H:%I');
                    $data['total_work'] = $work_duration;
                    $data['clock_in_out'] = 0;

                    $attendance = Attendance::findOrFail($last_attendance->id);
                    $attendance->update($data);
                    return redirect()->back();
                }
                else{
                    $data['clock_in'] = $time_now->format('H:i');
                    $data['clock_in_ip'] = $request->ip();
                    $data['clock_in_out'] = 1;

                    $last_clock_out = new DateTime($last_attendance->clock_out);
                    $data['total_rest'] = $last_clock_out->diff(new DateTime($data['clock_in']))->format('%H:%I');
                    Attendance::create($data);
                    return redirect()->back();
                }
            }

         
            return response()->json(['success' => true]);

    }



    public function get_employee_profile()
    {
        $user_auth = auth()->user();
        $employee = Employee::findOrFail($user_auth->id);

        $user['id'] = $employee->id;
        $user['firstname'] = $employee->firstname;
        $user['lastname'] = $employee->lastname;
        $user['username'] = $employee->username;
        $user['email'] = $employee->email;
        $user['phone'] = $employee->phone;
        $user['country'] = $employee->country;
        $user['avatar'] = "";
        $user['password'] = "";
    
        return view('session_employee.employee_profile', compact('user'));
    }


    public function Update_employee_profile(Request $request, $id)
    {
        $user_auth = auth()->user();
        if($user_auth = $id){

            $this->validate($request, [
                'firstname'      => 'required|string|max:255',
                'lastname'       => 'required|string|max:255',
                'country'        => 'required|string|max:255',
                'phone'          => 'required',
                'email'         => 'required|string|email|max:255|unique:users',
                'email'          => Rule::unique('users')->ignore($id),
                'avatar'    => 'nullable|image|mimes:jpeg,png,jpg,bmp,gif,svg|max:2048',
                'password'  =>  'sometimes|nullable|string|min:6,'.$id,
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

           
            $data = [];
            $data['firstname'] = $request['firstname'];
            $data['lastname'] = $request['lastname'];
            $data['username'] = $request['firstname'] .' '.$request['lastname'];
            $data['country'] = $request['country'];
            $data['email'] = $request['email'];
            $data['phone'] = $request['phone'];
           
            $user_data = [];
            $user_data['username'] = $request['firstname'] .' '.$request['lastname'];
            $user_data['email'] = $request->email;
            $user_data['password'] = $pass;
            $user_data['avatar'] = $filename;

            \DB::transaction(function () use ($request , $id , $user_data , $data) {

                User::whereId($id)->update($user_data);
                Employee::find($id)->update($data);
            
            }, 10);
         
            return response()->json(['success' => true]);
        }
        return abort('403', __('You are not authorized'));
    }


    public function employee_details()
    {
        $user_auth = auth()->user();

            $employee = Employee::where('deleted_at', '=', null)->findOrFail($user_auth->id);

            $total_leave_taken =  $employee->total_leave - $employee->remaining_leave;
            $total_leave_remaining = $employee->remaining_leave;

            $employee_project = Project::where('deleted_at', '=', null)
            ->with('assignedEmployees') 
            ->join('employee_project', 'projects.id', '=', 'employee_project.project_id')
            ->where('employee_id', $user_auth->id)
            ->groupBy('status')
            ->get([
                DB::raw(DB::raw("status As status_project")),
                DB::raw("count(*) As count"),
            ])
            ->pluck('count', 'status_project');

            $employee_task = Task::where('deleted_at', '=', null)
            ->with('assignedEmployees') 
            ->join('employee_task', 'tasks.id', '=', 'employee_task.task_id')
            ->where('employee_id', $user_auth->id)
            ->groupBy('status')
            ->get([
                DB::raw(DB::raw("status As status_task")),
                DB::raw("count(*) As count"),
            ])
            ->pluck('count', 'status_task');


            $documents = EmployeeDocument::where('employee_id' , $user_auth->id)->where('deleted_at', '=', null)->orderBy('id', 'desc')->get();
            $experiences = EmployeeExperience::where('employee_id' , $user_auth->id)->where('deleted_at', '=', null)->orderBy('id', 'desc')->get();
            $accounts_bank = EmployeeAccount::where('employee_id' , $user_auth->id)->where('deleted_at', '=', null)->orderBy('id', 'desc')->get();

            $leaves = Leave::where('employee_id' , $user_auth->id)
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

            $awards = Award::where('employee_id' , $user_auth->id)
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

            $travels = Travel::where('employee_id' , $user_auth->id)
            ->with('company:id,name','employee:id,username','arrangement_type:id,title')
            ->where('deleted_at', '=', null)
            ->orderBy('id', 'desc')
            ->get();

            $complaints = Complaint::where('employee_from' , $user_auth->id)
            ->with('company:id,name','EmployeeFrom:id,username','EmployeeAgainst:id,username')
            ->where('deleted_at', '=', null)
            ->orderBy('id', 'desc')
            ->get();

            $projects = Project::where('deleted_at', '=', null)
            ->with('client:id,username','assignedEmployees') 
            ->join('employee_project', 'projects.id', '=', 'employee_project.project_id')
            ->where('employee_id', $user_auth->id)
            ->orderBy('id', 'desc')
            ->get();

            $tasks = Task::where('deleted_at', '=', null)
            ->with('project:id,title','assignedEmployees') 
            ->join('employee_task', 'tasks.id', '=', 'employee_task.task_id')
            ->where('employee_id', $user_auth->id)
            ->orderBy('id', 'desc')
            ->get();

        
            $trainings = Training::where('deleted_at', '=', null)
            ->with('company:id,name','trainer:id,name','TrainingSkill:id,training_skill','assignedEmployees')
            ->join('employee_training','trainings.id','=','employee_training.training_id')
            ->where('employee_id', $user_auth->id)
            ->orderBy('id', 'desc')
            ->get();

            return view('session_employee.employee_details',
                compact('employee_project','employee_task','employee','experiences','documents','accounts_bank','total_leave_taken','total_leave_remaining',
                'leaves','awards','travels','complaints','tasks','projects','trainings')
            );
       
    }

    public function update_basic_info(Request $request, $id)
    {
        $user_auth = auth()->user();

            $this->validate($request, [
                'firstname'      => 'required|string|max:255',
                'lastname'       => 'required|string|max:255',
                'country'        => 'required|string|max:255',
                'gender'         => 'required',
                'phone'          => 'required',
                'email'          => 'required|string|email|max:255|unique:users',
                'email'          => Rule::unique('users')->ignore($user_auth->id),
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
            $data['marital_status'] = $request['marital_status'];
            $data['city'] = $request['city'];
            $data['province'] = $request['province'];
            $data['zipcode'] = $request['zipcode'];
            $data['address'] = $request['address'];

          
            $user_data = [];
            $user_data['username'] = $request['firstname'] .' '.$request['lastname'];
            $user_data['email'] = $request->email;

            \DB::transaction(function () use ($request , $user_auth , $user_data , $data) {

                User::whereId($user_auth->id)->update($user_data);
                Employee::find($user_auth->id)->update($data);
               
            }, 10);
         
            return response()->json(['success' => true]);
    }



    public function update_social_profile(Request $request, $id)
    {
        $user_auth = auth()->user();
       
        Employee::whereId($user_auth->id)->update([
            'facebook'    => $request->facebook,
            'skype'       => $request->skype,
            'whatsapp'    => $request->whatsapp,
            'twitter'     => $request->twitter,
            'linkedin'    => $request->linkedin,
        ]);

        return response()->json(['success' => true]);
       
    }


    public function storeExperiance(Request $request)
    {
        $user_auth = auth()->user();
        request()->validate([
            'title'           => 'required|string|max:255',
            'company_name'    => 'required|string|max:255',
            'start_date'      => 'required',
            'end_date'        => 'required',
            'employment_type'=> 'required',
        ]);

        EmployeeExperience::create([
            'company_name'    => $request['company_name'],
            'employee_id'     => $user_auth->id,
            'title'           => $request['title'],
            'start_date'      => $request['start_date'],
            'end_date'        => $request['end_date'],
            'employment_type' => $request['employment_type'],
            'location'        => $request['location'],
            'description'     => $request['description'],
        ]);

        return response()->json(['success' => true]);
    }


    public function updateExperiance(Request $request, $id)
    {
        $user_auth = auth()->user();

            request()->validate([
                'title'           => 'required|string|max:255',
                'company_name'    => 'required|string|max:255',
                'start_date'      => 'required',
                'end_date'        => 'required',
                'employment_type'=> 'required',
            ]);

            EmployeeExperience::whereId($user_auth->id)->update([
                'company_name'    => $request['company_name'],
                'employee_id'     => $user_auth->id,
                'title'           => $request['title'],
                'start_date'      => $request['start_date'],
                'end_date'        => $request['end_date'],
                'employment_type' => $request['employment_type'],
                'location'        => $request['location'],
                'description'     => $request['description'],
            ]);
        
            return response()->json(['success' => true]);

    }

    public function destroyExperiance($id)
    {
        $user_auth = auth()->user();

        EmployeeExperience::whereId($user_auth->id)->update([
            'deleted_at' => Carbon::now(),
        ]);

        return response()->json(['success' => true]);
    }



    public function storeAccount(Request $request)
    {
        $this->validate($request, [
            'bank_name'      => 'required|string|max:255',
            'bank_branch'    => 'required|string|max:255',
            'account_no'     => 'required|string|max:255',
        ]);

        EmployeeAccount::create($request->all());

        return response()->json(['success' => true]);
    }

    public function updateAccount(Request $request, $id)
    {
        $user_auth = auth()->user();

        $this->validate($request, [
            'bank_name'      => 'required|string|max:255',
            'bank_branch'    => 'required|string|max:255',
            'account_no'     => 'required|string|max:255',
        ]);

        EmployeeAccount::whereId($user_auth->id)->update($request->all());

        return response()->json(['success' => true]);

    }

    public function destroyAccount($id)
    {
        $user_auth = auth()->user();

        EmployeeAccount::whereId($user_auth->id)->update([
            'deleted_at' => Carbon::now(),
        ]);

        return response()->json(['success' => true]);

    }

    public function update_task_status(Request $request, $id)
    {
        $user_auth = auth()->user();
        if(EmployeeTask::where('employee_id' , $user_auth->id)->where('task_id' , $id)->exists()){
        
            $request->validate([
                'status'          => 'required',
            ]);

            Task::whereId($id)->update([
                'status'           => $request['status'],
            ]);

            return response()->json(['success' => true]);
        }

    }



    public function Get_leave_types()
    {
        $leave_types = LeaveType::where('deleted_at', '=', null)->orderBy('id', 'desc')->get();
        return response()->json($leave_types);
    }


    public function Request_leave(Request $request)
    {
        $user_auth = auth()->user();

            request()->validate([
                'leave_type_id'    => 'required',
                'start_date'       => 'required',
                'end_date'         => 'required|after_or_equal:start_date',
                'attachment'      => 'nullable|image|mimes:jpeg,png,jpg,bmp,gif,svg|max:2048',
            ]);

            if ($request->hasFile('attachment')) {


                $image = $request->file('attachment');
                $filename = time().'.'.$image->extension();  
                $image->move(public_path('/assets/images/leaves'), $filename);

            } else {
                $filename = 'no_image.png';
            }

            $employee_leave_info = Employee::find($user_auth->id);

            $start_date = new DateTime($request->start_date);
            $end_date = new DateTime($request->end_date);
            $day     = $start_date->diff($end_date);
            $days_diff    = $day->d +1;
            $leave_type = LeaveType::findOrFail($request['leave_type_id']);

          
            $leave_data= [];
            $leave_data['employee_id'] = $employee_leave_info->id;
            $leave_data['company_id'] = $employee_leave_info->company_id;
            $leave_data['department_id'] = $employee_leave_info->department_id;
            $leave_data['leave_type_id'] = $request['leave_type_id'];
            $leave_data['start_date'] = $request['start_date'];
            $leave_data['end_date'] = $request['end_date'];
            $leave_data['days'] = $days_diff;
            $leave_data['reason'] = $request['reason'];
            $leave_data['attachment'] = $filename;
            $leave_data['half_day'] = $request['half_day'];
            $leave_data['status'] = 'pending';

            if($days_diff > $employee_leave_info->remaining_leave)
            {
                return response()->json(['remaining_leave' => "remaining leaves are insufficient", 'isvalid' => false]);
            }
            elseif($request->status == 'approved'){
                $employee_leave_info->remaining_leave = $employee_leave_info->remaining_leave - $days_diff;
                $employee_leave_info->update();
            }

            Leave::create($leave_data);

            return response()->json(['success' => true ,'isvalid' => true]);

    }


}
