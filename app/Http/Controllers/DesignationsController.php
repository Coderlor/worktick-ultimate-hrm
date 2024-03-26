<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; 
use App\Models\Designation;
use App\Models\Company;
use App\Models\Department;
use Carbon\Carbon;

class DesignationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_auth = auth()->user();
		if ($user_auth->can('designation_view')){

            $designations = Designation::with('department')
            ->where('deleted_at', '=', null)
            ->orderBy('id', 'desc')
            ->get();
            return view('core_company.designation.designation_list', compact('designations'));

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
		if ($user_auth->can('designation_add')){

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
		if ($user_auth->can('designation_add')){

            request()->validate([
                'designation'   => 'required|string|max:255',
                'company_id'   => 'required',
                'department'    => 'required',
            ]);

            Designation::create([
                'designation'   => $request['designation'],
                'company_id'        => $request['company_id'],
                'department_id' => $request['department'],
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
		if ($user_auth->can('designation_edit')){

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
		if ($user_auth->can('designation_edit')){

            request()->validate([
                'designation'   => 'required|string|max:255',
                'company_id'   => 'required',
                'department'    => 'required',
            ]);

            Designation::whereId($id)->update([
                'designation'   => $request['designation'],
                'company_id'        => $request['company_id'],
                'department_id' => $request['department'],
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
		if ($user_auth->can('designation_delete')){

            Designation::whereId($id)->update([
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
          if($user_auth->can('designation_delete')){
              $selectedIds = $request->selectedIds;
      
              foreach ($selectedIds as $designation_id) {
                    Designation::whereId($designation_id)->update([
                        'deleted_at' => Carbon::now(),
                    ]);
              }
              return response()->json(['success' => true]);
          }
          return abort('403', __('You are not authorized'));
       }



    public function Get_designations_by_department(Request $request)
    {
        $designations = Designation::where('department_id' , $request->id)
        ->where('deleted_at', '=', null)
        ->orderBy('id', 'desc')
        ->get();

        return response()->json($designations);
    }

}
