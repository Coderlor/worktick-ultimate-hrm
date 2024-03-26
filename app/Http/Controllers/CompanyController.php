<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Company;
use Carbon\Carbon;
use DB;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_auth = auth()->user();
		if ($user_auth->can('company_view')){

            $companies = Company::where('deleted_at', '=', null) 
            ->orderBy('id', 'desc')
            ->get();
            return view('core_company.company.company_list', compact('companies'));

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
		if ($user_auth->can('company_add')){

            request()->validate([
                'name'      => 'required|string|max:255',
            ]);

            Company::create([
                'name'        => $request['name'],
                'email'   => $request['email'],
                'phone'   => $request['phone'],
                'country'   => $request['country'],
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
		if ($user_auth->can('company_edit')){

            request()->validate([
                'name'      => 'required|string|max:255',
            ]);

            Company::whereId($id)->update([
                'name'        => $request['name'],
                'email'   => $request['email'],
                'phone'   => $request['phone'],
                'country'   => $request['country'],
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
		if ($user_auth->can('company_delete')){

            Company::whereId($id)->update([
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
        if($user_auth->can('company_delete')){
            $selectedIds = $request->selectedIds;
    
            foreach ($selectedIds as $company_id) {
                Company::whereId($company_id)->update([
                    'deleted_at' => Carbon::now(),
                ]);
            }
            return response()->json(['success' => true]);
        }
        return abort('403', __('You are not authorized'));
     }

    public function Get_all_Company()
    {
        $companies = Company::where('deleted_at', '=', null)
        ->orderBy('id', 'desc')
        ->get(['id','name']);

        return response()->json($companies);
    }
}
