<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use Carbon\Carbon;
use DB;

class DepartmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_auth = auth()->user();
		if ($user_auth->can('department_view')){

            $departments = Department::
            leftjoin('employees','employees.id','=','departments.department_head')
            ->join('companies','companies.id','=','departments.company_id')
            ->where('departments.deleted_at' , '=', null)
            ->select('departments.*','employees.username AS employee_head','companies.name AS company_name')
            ->orderBy('id', 'desc')
            ->get();
            return view('core_company.department.department_list', compact('departments'));

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
		if ($user_auth->can('department_add')){

            $companies = Company::where('deleted_at', '=', null)
            ->orderBy('id', 'desc')
            ->get(['id','name']);

            return response()->json([
                'companies' =>$companies,
            ]);

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
		if ($user_auth->can('department_add')){

            request()->validate([
                'department'   => 'required|string|max:255',
                'company_id'   => 'required',
            ]);

            Department::create([
                'department'        => $request['department'],
                'company_id'        => $request['company_id'],
                'department_head'   => $request['department_head']?$request['department_head']:Null,
            ]);

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
		if ($user_auth->can('department_edit')){

            $companies = Company::where('deleted_at', '=', null)
            ->orderBy('id', 'desc')
            ->get(['id','name']);
            
            return response()->json([
                'companies' =>$companies,
            ]);

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
		if ($user_auth->can('department_edit')){

            request()->validate([
                'department'   => 'required|string|max:255',
                'company_id'   => 'required',
            ]);

            Department::whereId($id)->update([
                'department'        => $request['department'],
                'company_id'        => $request['company_id'],
                'department_head'   => $request['department_head']?$request['department_head']:Null,
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
		if ($user_auth->can('department_delete')){

            Department::whereId($id)->update([
                'deleted_at' => Carbon::now(),
            ]);
            return response()->json(['success' => true]);

        }
        return abort('403', __('You are not authorized'));
    }

      //-------------- Delete by selection  ---------------\\

      public function delete_by_selection(Request $request)
      {
         $user_auth = auth()->user();
         if($user_auth->can('department_delete')){
             $selectedIds = $request->selectedIds;
     
             foreach ($selectedIds as $department_id) {
                Department::whereId($department_id)->update([
                    'deleted_at' => Carbon::now(),
                ]);
             }
             return response()->json(['success' => true]);
         }
         return abort('403', __('You are not authorized'));
      }

    public function Get_all_Departments()
    {
        $departments = Department::where('deleted_at', '=', null)
        ->orderBy('id', 'desc')
        ->get(['id','department']);

        return response()->json($departments);
    }


    
    public function Get_departments_by_company(Request $request)
    {
        $departments = Department::where('company_id' , $request->id)
        ->where('deleted_at', '=', null)
        ->orderBy('id', 'desc')
        ->get();

        return response()->json($departments);
    }
}
