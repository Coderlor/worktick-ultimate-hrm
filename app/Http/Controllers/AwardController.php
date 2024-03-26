<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Award;
use App\Models\AwardType;
use App\Models\Company;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use File;

class AwardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_auth = auth()->user();
		if ($user_auth->can('award_view')){
                $awards = Award::
                join('companies','companies.id','=','awards.company_id')
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

                return view('hr.award.award_list', compact('awards'));

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
		if ($user_auth->can('award_add')){

            $award_types = AwardType::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','title']);
            $companies = Company::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','name']);
            return response()->json([
                'award_types'     => $award_types,
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
		if ($user_auth->can('award_add')){

            request()->validate([
                'cash'           => 'required|numeric',
                'gift'           => 'required|string|max:255',
                'company_id'            => 'required',
                'department_id'            => 'required',
                'employee_id'    => 'required',
                'award_type_id'  => 'required',
                'date'           => 'required',
                'photo'          => 'nullable|image|mimes:jpeg,png,jpg,bmp,gif,svg|max:2048',
            ]);

            if ($request->hasFile('photo')) {

                $image = $request->file('photo');
                $filename = time().'.'.$image->extension();  
                $image->move(public_path('/assets/images/awards'), $filename);

            } else {
                $filename = 'no_image.png';
            }

            Award::create([
                'cash'            => $request['cash'],
                'gift'            => $request['gift'],
                'company_id'     => $request['company_id'],
                'department_id'     => $request['department_id'],
                'employee_id'     => $request['employee_id'],
                'award_type_id'   => $request['award_type_id'],
                'date'            => $request['date'],
                'photo'           => $filename,
                'note'     => $request['note'],
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
		if ($user_auth->can('award_edit')){

            $award_types = AwardType::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','title']);
            $companies = Company::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','name']);
            return response()->json([
                'award_types'     => $award_types,
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
		if ($user_auth->can('award_edit')){

            request()->validate([
                'cash'           => 'required|numeric',
                'gift'           => 'required|string|max:255',
                'company_id'            => 'required',
                'department_id'            => 'required',
                'employee_id'    => 'required',
                'award_type_id'  => 'required',
                'date'           => 'required',
                'photo'          => 'nullable|image|mimes:jpeg,png,jpg,bmp,gif,svg|max:2048',
            ]);

            $award = Award::findOrFail($id);

            $currentPhoto = $award->photo;
            if ($request->photo != null) {
                if ($request->photo != $currentPhoto) {

                    $image = $request->file('photo');
                    $filename = time().'.'.$image->extension();  
                    $image->move(public_path('/assets/images/awards'), $filename);
                    $path = public_path() . '/assets/images/awards';

                    $userPhoto = $path . '/' . $currentPhoto;
                    if (file_exists($userPhoto)) {
                        if ($award->photo != 'no_image.png') {
                            @unlink($userPhoto);
                        }
                    }
                } else {
                    $filename = $currentPhoto;
                }
            }else{
                $filename = $currentPhoto;
            }

            Award::whereId($id)->update([
                'cash'            => $request['cash'],
                'gift'            => $request['gift'],
                'company_id'     => $request['company_id'],
                'department_id'     => $request['department_id'],
                'employee_id'     => $request['employee_id'],
                'award_type_id'   => $request['award_type_id'],
                'date'            => $request['date'],
                'photo'           => $filename,
                'note'            => $request['note'],
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
		if ($user_auth->can('award_delete')){

            Award::whereId($id)->update([
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
        if($user_auth->can('award_delete')){
            $selectedIds = $request->selectedIds;
    
            foreach ($selectedIds as $award_id) {
                Award::whereId($award_id)->update([
                    'deleted_at' => Carbon::now(),
                ]);
            }
            return response()->json(['success' => true]);
        }
        return abort('403', __('You are not authorized'));
     }

}
