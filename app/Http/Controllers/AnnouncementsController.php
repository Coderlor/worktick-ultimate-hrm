<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use Carbon\Carbon;
use DB;

class AnnouncementsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_auth = auth()->user();
		if ($user_auth->can('announcement_view')){
            if ($user_auth->role_users_id == 1){
                $announcements = Announcement::
                join('companies','companies.id','=','announcements.company_id')
                ->leftjoin('departments','departments.id','=','announcements.department_id')
                ->where('announcements.deleted_at' , '=', null)
                ->select('announcements.*','companies.name AS company_name','departments.department AS department_name')
                ->orderBy('id', 'desc')
                ->get();
                return view('core_company.announcement.announcement_list', compact('announcements'));

            }else{
                $employee = Employee::findOrFail($user_auth->id);
                $announcements = Announcement::
                join('companies','companies.id','=','announcements.company_id')
                ->leftjoin('departments','departments.id','=','announcements.department_id')
                ->where('announcements.deleted_at' , '=', null)
                ->where('announcements.company_id', $employee->company_id)
                ->where('announcements.department_id', $employee->department_id)
                ->orWhere('announcements.department_id', '=', Null)
                ->select('announcements.*','companies.name AS company_name','departments.department AS department_name')
                ->orderBy('id', 'desc')
                ->get();

                return view('core_company.announcement.announcement_list', compact('announcements'));
            }

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
		if ($user_auth->can('announcement_add')){

            $companies = Company::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','name']);
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
		if ($user_auth->can('announcement_add')){

            request()->validate([
                'department'      => 'required',
                'title'           => 'required|string|max:255',
                'summary'         => 'required|string',
                'start_date'      => 'required',
                'end_date'        => 'required',
                'company_id'      => 'required',
                
            ]);

            Announcement::create([
                'title'           => $request['title'],
                'summary'         => $request['summary'],
                'start_date'      => $request['start_date'],
                'end_date'        => $request['end_date'],
                'description'     => $request['description'],
                'company_id'      => $request['company_id'],
                'department_id'   => $request['department']  =='null' ?Null:$request['department'],
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
		if ($user_auth->can('announcement_edit')){

            $companies = Company::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','name']);
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
		if ($user_auth->can('announcement_edit')){

            request()->validate([
                'department'      => 'required',
                'title'           => 'required|string|max:255',
                'summary'         => 'required|string',
                'start_date'      => 'required',
                'end_date'        => 'required',
                'company_id'   => 'required',
                
            ]);

            Announcement::whereId($id)->update([
                'title'           => $request['title'],
                'summary'         => $request['summary'],
                'start_date'      => $request['start_date'],
                'end_date'        => $request['end_date'],
                'description'     => $request['description'],
                'company_id'      => $request['company_id'],
                'department_id'   => $request['department'] =='null' ?Null:$request['department'],
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
		if ($user_auth->can('announcement_delete')){

            Announcement::whereId($id)->update([
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
         if($user_auth->can('announcement_delete')){
             $selectedIds = $request->selectedIds;
     
             foreach ($selectedIds as $announcement_id) {
                Announcement::whereId($announcement_id)->update([
                    'deleted_at' => Carbon::now(),
                ]);
             }
             return response()->json(['success' => true]);
         }
         return abort('403', __('You are not authorized'));
      }
}
