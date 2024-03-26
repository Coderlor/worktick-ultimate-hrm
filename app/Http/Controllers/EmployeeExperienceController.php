<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmployeeExperience;
use App\Models\Employee;
use Carbon\Carbon;

class EmployeeExperienceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
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
		if ($user_auth->can('employee_details')){

            request()->validate([
                'title'           => 'required|string|max:255',
                'company_name'    => 'required|string|max:255',
                'start_date'      => 'required',
                'end_date'        => 'required',
                'employment_type'=> 'required',
            ]);

            EmployeeExperience::create([
                'company_name'    => $request['company_name'],
                'employee_id'     => $request['employee_id'],
                'title'           => $request['title'],
                'start_date'      => $request['start_date'],
                'end_date'        => $request['end_date'],
                'employment_type' => $request['employment_type'],
                'location'        => $request['location'],
                'description'     => $request['description'],
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
		if ($user_auth->can('employee_details')){

            request()->validate([
                'title'           => 'required|string|max:255',
                'company_name'    => 'required|string|max:255',
                'start_date'      => 'required',
                'end_date'        => 'required',
                'employment_type'=> 'required',
            ]);

            EmployeeExperience::whereId($id)->update([
                'company_name'    => $request['company_name'],
                'employee_id'     => $request['employee_id'],
                'title'           => $request['title'],
                'start_date'      => $request['start_date'],
                'end_date'        => $request['end_date'],
                'employment_type' => $request['employment_type'],
                'location'        => $request['location'],
                'description'     => $request['description'],
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
		if ($user_auth->can('employee_details')){

            EmployeeExperience::whereId($id)->update([
                'deleted_at' => Carbon::now(),
            ]);

            return response()->json(['success' => true]);

        }
        return abort('403', __('You are not authorized'));
    }
}
