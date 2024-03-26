<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Attendance;
use App\Models\Project;
use App\Models\Task;
use App\Models\Client;
use App\Models\Expense;
use App\Models\Deposit;
use App\Models\Employee;
use App\Models\ExpenseCategory;
use App\Models\Account;
use App\Models\PaymentMethod;
use App\Models\DepositCategory;
use Carbon\Carbon;
use DB;
use App\utils\helpers;
use DataTables;

class ReportController extends Controller
{

    /* Attendance report */

    public function attendance_report_index(Request $request){

        $user_auth = auth()->user();
		if ($user_auth->can('attendance_report')){

            $employees = Employee::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','username']);

            if ($request->ajax()) {
                $helpers = new helpers();
                $param = array(0 => '=');
                $columns = array(0 => 'employee_id');
                $end_date_default = Carbon::now()->format('Y-m-d');
                $start_date_default = Carbon::now()->subYear()->format('Y-m-d');
                $start_date = empty($request->start_date)?$start_date_default:$request->start_date;
                $end_date = empty($request->end_date)?$end_date_default:$request->end_date;

                $attendances = Attendance::where('deleted_at', '=', null)
                ->whereBetween('date', array($start_date, $end_date))
                ->with('company:id,name','employee:id,username')
                ->orderBy('id', 'desc');
                
                //Multiple Filter
                $attendances_Filtred = $helpers->filter($attendances, $columns, $param, $request)->get();
                return Datatables::of($attendances_Filtred)
                        ->addIndexColumn()
                        ->make(true);
            }

            return view('report.attendance_report',compact('employees'));

        }
        return abort('403', __('You are not authorized'));

    }


    /* Employee report */
    public function employee_report_index(Request $request){
        
        $user_auth = auth()->user();
		if ($user_auth->can('employee_report')){

            $companies = Company::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','name']);

            if ($request->ajax()) {
                $helpers = new helpers();
                $param = array(0 => '=' , 1=> '=' , 2=> '=');
                $columns = array(0 => 'company_id' , 1 => 'department_id' , 2 => 'designation_id');
                
                $employees = Employee::where('deleted_at', '=', null)
                ->with('company:id,name','department:id,department','designation:id,designation','office_shift:id,name')
                ->orderBy('id', 'desc');
                //Multiple Filter
                $employees_Filtred = $helpers->filter($employees, $columns, $param, $request)->get();
                return Datatables::of($employees_Filtred)
                        ->addIndexColumn()
                        ->make(true);
            }

            return view('report.employee_report',compact('companies'));

        }
        return abort('403', __('You are not authorized'));
    }


    /* Project report */
    public function project_report_index(Request $request){

        $user_auth = auth()->user();
		if ($user_auth->can('project_report')){

            $clients = Client::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','username']);
            $companies = Company::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','name']);

            if ($request->ajax()) {
                $helpers = new helpers();
                $param = array(0 => 'like' , 1=> '=' , 2=> '=' , 3=> 'like' , 4=> 'like');
                $columns = array(0 => 'title' , 1 => 'client_id' , 2 => 'company_id' , 3 => 'priority' , 4 => 'status');
                
                $projects = Project::where('deleted_at', '=', null)
                ->with('company:id,name','client:id,username')->orderBy('id', 'desc');
                //Multiple Filter
                $projects_Filtred = $helpers->filter($projects, $columns, $param, $request)->get();
        
                return Datatables::of($projects_Filtred)
                        ->addIndexColumn()
                        ->make(true);
            }

            return view('report.project_report',compact('clients','companies'));

        }
        return abort('403', __('You are not authorized'));
    }

  


    /* Task report */
    public function task_report_index(Request $request){

        $user_auth = auth()->user();
		if ($user_auth->can('task_report')){

            $projects = Project::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','title']);
            $companies = Company::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','name']);

            if ($request->ajax()) {
                $helpers = new helpers();
                $param = array(0 => 'like' , 1=> '=' , 2=> '=' , 3=> 'like' , 4=> 'like');
                $columns = array(0 => 'title' , 1 => 'project_id' , 2 => 'company_id' , 3 => 'priority' , 4 => 'status');
                
                $tasks = Task::where('deleted_at', '=', null)->with('company:id,name','project:id,title')->orderBy('id', 'desc');
                //Multiple Filter
                $tasks_Filtred = $helpers->filter($tasks, $columns, $param, $request)->get();
        
                return Datatables::of($tasks_Filtred)
                        ->addIndexColumn()
                        ->make(true);
            }

            return view('report.task_report',compact('projects','companies'));

        }
        return abort('403', __('You are not authorized'));

    }


    /* Expense report */

    public function expense_report_index(Request $request){

        $user_auth = auth()->user();
		if ($user_auth->can('expense_report')){

            $accounts = Account::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','account_name']);
            $categories = ExpenseCategory::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','title']);
            $payment_methods = PaymentMethod::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','title']);

            if ($request->ajax()) {
                $helpers = new helpers();
                $param = array(0=> 'like' , 1=> '=' , 2=> '=' , 3 => '=');
                $columns = array(0 => 'expense_ref' , 1 => 'account_id' , 2 => 'expense_category_id' , 3 => 'payment_method_id');
                $end_date_default = Carbon::now()->format('Y-m-d');
                $start_date_default = Carbon::now()->subYear()->format('Y-m-d');
                $start_date = empty($request->start_date)?$start_date_default:$request->start_date;
                $end_date = empty($request->end_date)?$end_date_default:$request->end_date;

                $expenses = Expense::where('deleted_at', '=', null)
                ->whereBetween('date', array($start_date, $end_date))
                ->with('account:id,account_name','payment_method:id,title','expense_category:id,title')->orderBy('id', 'desc');
                //Multiple Filter
                $expenses_Filtred = $helpers->filter($expenses, $columns, $param, $request)->get();
                return Datatables::of($expenses_Filtred)
                        ->addIndexColumn()
                        ->make(true);
            }

            return view('report.expense_report',compact('accounts','categories','payment_methods'));

        }
        return abort('403', __('You are not authorized'));

    }


    /* Deposit report */

    public function deposit_report_index(Request $request){

        $user_auth = auth()->user();
		if ($user_auth->can('deposit_report')){

            $accounts = Account::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','account_name']);
            $categories = DepositCategory::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','title']);
            $payment_methods = PaymentMethod::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','title']);

            if ($request->ajax()) {
                $helpers = new helpers();
                $param = array(0=> 'like' , 1=> '=' , 2=> '=' , 3 => '=');
                $columns = array(0 => 'deposit_ref' , 1 => 'account_id' , 2 => 'deposit_category_id' , 3 => 'payment_method_id');

                $end_date_default = Carbon::now()->format('Y-m-d');
                $start_date_default = Carbon::now()->subYear()->format('Y-m-d');
                $start_date = empty($request->start_date)?$start_date_default:$request->start_date;
                $end_date = empty($request->end_date)?$end_date_default:$request->end_date;

                $deposits = Deposit::where('deleted_at', '=', null)
                ->whereBetween('date', array($start_date, $end_date))
                ->with('account:id,account_name','payment_method:id,title','deposit_category:id,title')->orderBy('id', 'desc');
                //Multiple Filter
                $deposits_Filtred = $helpers->filter($deposits, $columns, $param, $request)->get();
                return Datatables::of($deposits_Filtred)
                        ->addIndexColumn()
                        ->make(true);
            }

            return view('report.deposit_report',compact('accounts','categories','payment_methods'));

        }
        return abort('403', __('You are not authorized'));
    }


    public function fetchDepartment(Request $request){
            
        $value = $request->get('value');
        $dependent  = $request->get('dependent');
        $data = Department::where('company_id' ,$value)->where('deleted_at', '=', null)->groupBy('department')->get();
        $output = '';
        
        foreach ($data as $row)
        {
            $output .= '<option value=' . $row->id . '>' . $row->$dependent . '</option>';
        }

        return $output;
    }


    public function fetchDesignation(Request $request){
        $value = $request->get('value');
        $designation_name  = $request->get('designation_name');
        $data = Designation::where('department_id' ,$value)->where('deleted_at', '=', null)->groupBy('designation')->get();
        $output = '';
        
        foreach ($data as $row)
        {
            $output .= '<option value=' . $row->id . '>' . $row->$designation_name . '</option>';
        }

        return $output;
    }

    
}
