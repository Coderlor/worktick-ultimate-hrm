<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Project;
use App\Models\Task;
use App\Models\Client;
use App\Models\Expense;
use App\Models\Deposit;
use App\Models\Department;
use App\Models\Award;
use App\Models\Announcement;
use App\Models\EmployeeProject;
use App\Models\EmployeeTask;
use Carbon\Carbon;
use DB;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard_admin()
    {
        $user_auth = auth()->user();
		if ($user_auth->role_users_id === 1){

            $count_employee = Employee::where('deleted_at', '=', null)->count();
            $count_clients = Client::where('deleted_at', '=', null)->count();
            $count_project = Project::where('deleted_at', '=', null)->count();
            $count_task = Task::where('deleted_at', '=', null)->count();

            $latest_employees = Employee::where('deleted_at', '=', null)
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

            //echart Employee count by department

            $employees_data = Employee::where('employees.deleted_at', '=', null)
            ->join('departments', 'departments.id', '=', 'employees.department_id')
            ->groupBy("departments.department")
            ->select([
                DB::raw("department As department"),
                DB::raw("count(*) AS count"),
            ])
            ->get(['department', 'count']);


            $department_name = [];
            $total_employee = [];
            foreach ($employees_data as $key => $value) {
                $department_name[] = $value['department'];
                $total_employee[] = $value['count'];
            }


        // Echart This Week Expenses & Deposits
        // Build an array of the dates we want to show, oldest first
        $dates = collect();
        foreach (range(-6, 0) as $i) {
            $date = Carbon::now()->addDays($i)->format('Y-m-d');
            $dates->put($date, 0);
        }

        $date_range = \Carbon\Carbon::today()->subDays(6);

        // Get the Expense counts
        $expenses = Expense::where('date', '>=', $date_range)
            ->where('deleted_at', '=', null)
            ->groupBy(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"))
            ->orderBy('date', 'asc')
            ->select([
                DB::raw(DB::raw("DATE_FORMAT(date,'%Y-%m-%d') as date")),
                DB::raw('SUM(amount) AS count'),
            ])
            ->pluck('count', 'date');

        // Get the Deposits counts
        $deposits = Deposit::where('date', '>=', $date_range)
        ->where('deleted_at', '=', null)
        ->groupBy(DB::raw("DATE_FORMAT(date,'%Y-%m-%d')"))
        ->orderBy('date', 'asc')
        ->get([
            DB::raw(DB::raw("DATE_FORMAT(date,'%Y-%m-%d') as date")),
            DB::raw('SUM(amount) AS count'),
        ])
        ->pluck('count', 'date');

        // Merge the two collections;
        $dates_expenses = $dates->merge($expenses);

        
        $expenses_data = [];
        $days = [];
        foreach ($dates_expenses as $key => $value) {
            $expenses_data[] = $value;
            // $days[] = $key;
        }
        
        // Merge the two collections;
        $dates_deposits = $dates->merge($deposits);
        $deposits_data = [];
        foreach ($dates_deposits as $key => $value) {
            $deposits_data[] = $value;
            $days[] = $key;
        }

            //echart Expense vs Deposit

            $expense_amount = Expense::where('deleted_at', '=', null)
            ->select(
                DB::raw("SUM(amount) as expense_amount"),
            )
            ->pluck('expense_amount');

            $deposit_amount = Deposit::where('deleted_at', '=', null)
            ->select(
                DB::raw("SUM(amount) as deposit_amount"),
            )
            ->pluck('deposit_amount');


            //echart Project by status

            $project_status =  Project::where('deleted_at', '=', null)
            ->groupBy('status')
            ->get([
                DB::raw(DB::raw("status As status_project")),
                DB::raw("count(*) As count"),
            ])
            ->pluck('count', 'status_project');

            $task_status =  Task::where('deleted_at', '=', null)
            ->groupBy('status')
            ->get([
                DB::raw(DB::raw("status As status_task")),
                DB::raw("count(*) As count"),
            ])
            ->pluck('count', 'status_task');

          
            return view('dashboard.dashboard', ([
                'project_status' => $project_status,
                'task_status' => $task_status,
                'count_employee' => $count_employee,
                'count_project' => $count_project,
                'count_task' => $count_task,
                'count_clients' => $count_clients,

                'expense_amount' => $expense_amount,
                'deposit_amount' => $deposit_amount,

                'latest_employees' => $latest_employees,

                'department_name' => $department_name,
                'total_employee' => $total_employee,

                'deposits_data' => $deposits_data,
                'expenses_data' => $expenses_data,
                'days' => $days,
            ]));

        }
        return abort('403', __('You are not authorized'));
    }


    public function dashboard_employee()
    {
        $user_auth = auth()->user();
        $employee = Employee::with('company:id,name','department:id,department','office_shift')->findOrFail($user_auth->id);

        // Date now
        $day_in_now = strtolower(Carbon::now()->format('l')) . '_in';
        $day_out_now = strtolower(Carbon::now()->format('l')) . '_out';

        //shift office
        $punch_in = $employee->office_shift->$day_in_now;
        $punch_out = $employee->office_shift->$day_out_now;
        $punch_name = $employee->office_shift->name;
        $employee_attendance =   Attendance::where('deleted_at', '=', null)
        ->where('date' , now()->format('Y-m-d'))
        ->where('employee_id', $employee->id)
        ->orderBy('id','desc')->first() ?? null;

        $count_awards = Award::where('deleted_at', '=', null)
        ->where('employee_id', $user_auth->id)
        ->count();
        
        $count_announcement = Announcement::where('start_date' , '<=' , now()->format('Y-m-d'))
        ->where('end_date' , '>=' , now()->format('Y-m-d'))
        ->where('deleted_at', '=', null)
        ->count();

        $count_projects = EmployeeProject::where('employee_id', $user_auth->id)->count();
        $count_tasks = EmployeeTask::where('employee_id', $user_auth->id)->count();

        $latest_projects = Project::where('deleted_at', '=', null)
        ->with('client:id,username','assignedEmployees') 
        ->join('employee_project', 'projects.id', '=', 'employee_project.project_id')
        ->where('employee_id', $user_auth->id)
        ->take(5)
        ->orderBy('id', 'desc')
        ->get();

        $latest_tasks = Task::where('deleted_at', '=', null)
        ->with('project:id,title','assignedEmployees') 
        ->join('employee_task', 'tasks.id', '=', 'employee_task.task_id')
        ->where('employee_id', $user_auth->id)
        ->take(5)
        ->orderBy('id', 'desc')
        ->get();


        $total_leave_taken =  $employee->total_leave - $employee->remaining_leave;
        $total_leave_remaining = $employee->remaining_leave;

        return view('dashboard.dashboard_employee', ([
            'total_leave_taken'=> $total_leave_taken,
            'total_leave_remaining'=> $total_leave_remaining,
            'employee' => $employee,
            'count_awards' => $count_awards,
            'count_projects' => $count_projects,
            'count_tasks' => $count_tasks,
            'count_announcement' => $count_announcement,
            'employee_attendance' => $employee_attendance,
            'punch_name' => $punch_name,
            'punch_in' => $punch_in,
            'punch_out' => $punch_out,
            'latest_projects' => $latest_projects,
            'latest_tasks' => $latest_tasks,
        ]));

    }

    public function dashboard_client()
    {
        $user_auth = auth()->user();

        
        $latest_projects = Project::where('deleted_at', '=', null)
        ->where('client_id', $user_auth->id)
        ->take(5)
        ->orderBy('id', 'desc')
        ->get();
        
        $client =  Client::with('projects')->findOrFail($user_auth->id);
        
        $projects =  $client->projects;
        $project_id = $projects->pluck('id');
        
        $count_projects = Project::where('client_id', $user_auth->id)
        ->where('deleted_at', '=', null)
        ->count();
       
        $count_tasks = Task::where('deleted_at', '=', null)
        ->whereIn('project_id' , $project_id)
        ->count();

         //echart Project by status

         $project_status =  Project::where('deleted_at', '=', null)
         ->where('client_id', $user_auth->id)
         ->groupBy('status')
         ->get([
             DB::raw(DB::raw("status As status_project")),
             DB::raw("count(*) As count"),
         ])
         ->pluck('count', 'status_project');

        return view('dashboard.dashboard_client', ([
            'count_projects' => $count_projects,
            'count_tasks' => $count_tasks,
            'latest_projects' => $latest_projects,
            'project_status' => $project_status,
        ]));

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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
