<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\Employee;
use App\Models\Company;
use Carbon\Carbon;

class ComplaintController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_auth = auth()->user();
		if ($user_auth->can('complaint_view')){

            $complaints = Complaint::with('company:id,name','EmployeeFrom:id,username','EmployeeAgainst:id,username')
            ->where('deleted_at', '=', null)
            ->orderBy('id', 'desc')
            ->get();

            return view('hr.complaint.complaint_list', compact('complaints'));

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
		if ($user_auth->can('complaint_add')){

            $companies = Company::where('deleted_at', '=', null)
            ->orderBy('id', 'desc')
            ->get(['id','name']);

            return response()->json([
                'companies'       => $companies,
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
		if ($user_auth->can('complaint_add')){

            request()->validate([
                'title'        => 'required|string|max:255',
                'date'         => 'required',
                'reason'         => 'required',
                'company_id'  => 'required',
                'employee_from'  => 'required',
                'employee_against'  => 'required',
            ]);

            Complaint::create([
                'company_id'     => $request['company_id'],
                'employee_from'     => $request['employee_from'],
                'employee_against'     => $request['employee_against'],
                'title'        => $request['title'],
                'date'         => $request['date'],
                'time'         => $request['time'],
                'reason'  => $request['reason'],
                'description'  => $request['description'],
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
		if ($user_auth->can('complaint_edit')){

            $companies = Company::where('deleted_at', '=', null)
            ->orderBy('id', 'desc')
            ->get(['id','name']);
            
            return response()->json([
                'companies'       => $companies,
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
		if ($user_auth->can('complaint_edit')){

            request()->validate([
                'title'        => 'required|string|max:255',
                'date'         => 'required',
                'reason'         => 'required',
                'company_id'  => 'required',
                'employee_from'  => 'required',
                'employee_against'  => 'required',
            ]);

            Complaint::whereId($id)->update([
                'company_id'     => $request['company_id'],
                'employee_from'     => $request['employee_from'],
                'employee_against'     => $request['employee_against'],
                'title'        => $request['title'],
                'date'         => $request['date'],
                'time'         => $request['time'],
                'reason'  => $request['reason'],
                'description'  => $request['description'],
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
		if ($user_auth->can('complaint_delete')){

            Complaint::whereId($id)->update([
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
            if($user_auth->can('complaint_delete')){
                $selectedIds = $request->selectedIds;
        
                foreach ($selectedIds as $complaint_id) {
                    Complaint::whereId($complaint_id)->update([
                        'deleted_at' => Carbon::now(),
                    ]);
                }
                return response()->json(['success' => true]);
            }
            return abort('403', __('You are not authorized'));
         }
}
