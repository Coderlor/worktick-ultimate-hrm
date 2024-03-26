<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OfficeShift;
use App\Models\Company;
use Carbon\Carbon;
use DateTime;

class OfficeShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_auth = auth()->user();
		if ($user_auth->can('office_shift_view')){

            $office_shifts = OfficeShift::where('deleted_at', '=', null)->orderBy('id', 'desc')->get();
            return view('hr.office_shift.office_shift_list', compact('office_shifts'));

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
		if ($user_auth->can('office_shift_add')){

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
		if ($user_auth->can('office_shift_add')){

            request()->validate([
                'name'           => 'required|string|max:255',
                'company_id'      => 'required',
            ]);

            $monday_in = new DateTime($request['monday_in']);
            $monday_out = new DateTime($request['monday_out']);
            $tuesday_in = new DateTime($request['tuesday_in']);
            $tuesday_out = new DateTime($request['tuesday_out']);
            $wednesday_in = new DateTime($request['wednesday_in']);
            $wednesday_out = new DateTime($request['wednesday_out']);
            $thursday_in = new DateTime($request['thursday_in']);
            $thursday_out = new DateTime($request['thursday_out']);
            $friday_in = new DateTime($request['friday_in']);
            $friday_out = new DateTime($request['friday_out']);
            $saturday_in = new DateTime($request['saturday_in']);
            $saturday_out = new DateTime($request['saturday_out']);
            $sunday_in = new DateTime($request['sunday_in']);
            $sunday_out = new DateTime($request['sunday_out']);

            OfficeShift::create([
                'company_id'     => $request['company_id'],
                'name'           => $request['name'],
                'monday_in'      => $request['monday_in']?$monday_in->format('H:iA'):Null,
                'monday_out'     => $request['monday_out']?$monday_out->format('H:iA'):Null,
                'tuesday_in'     => $request['tuesday_in']?$tuesday_in->format('H:iA'):Null,
                'tuesday_out'    => $request['tuesday_out']?$tuesday_out->format('H:iA'):Null,
                'wednesday_in'   => $request['wednesday_in']?$wednesday_in->format('H:iA'):Null,
                'wednesday_out'  => $request['wednesday_out']?$wednesday_out->format('H:iA'):Null,
                'thursday_in'    => $request['thursday_in']?$thursday_in->format('H:iA'):Null,
                'thursday_out'   => $request['thursday_out']?$thursday_out->format('H:iA'):Null,
                'friday_in'      => $request['friday_in']?$friday_in->format('H:iA'):Null,
                'friday_out'     => $request['friday_out']?$friday_out->format('H:iA'):Null,
                'saturday_in'    => $request['saturday_in']?$saturday_in->format('H:iA'):Null,
                'saturday_out'   => $request['saturday_out']?$saturday_out->format('H:iA'):Null,
                'sunday_in'      => $request['sunday_in']?$sunday_in->format('H:iA'):Null,
                'sunday_out'     => $request['sunday_out']?$sunday_out->format('H:iA'):Null,
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
		if ($user_auth->can('office_shift_edit')){

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
		if ($user_auth->can('office_shift_edit')){

            //monday_in
            if(strlen($request['monday_in']) == 5){
                $monday_in = new DateTime($request['monday_in']);
            }else{
                $monday_in =  new DateTime(substr($request['monday_in'], 0, -2));
            }

             //monday_out
            if(strlen($request['monday_out']) == 5){
                $monday_out = new DateTime($request['monday_out']);
            }else{
                $monday_out =  new DateTime(substr($request['monday_out'], 0, -2));
            }

            //tuesday_in
            if(strlen($request['tuesday_in']) == 5){
                $tuesday_in = new DateTime($request['tuesday_in']);
            }else{
                $tuesday_in =  new DateTime(substr($request['tuesday_in'], 0, -2));
            }

            //tuesday_out
            if(strlen($request['tuesday_out']) == 5){
                $tuesday_out = new DateTime($request['tuesday_out']);
            }else{
                $tuesday_out =  new DateTime(substr($request['tuesday_out'], 0, -2));
            }

            //wednesday_in
            if(strlen($request['wednesday_in']) == 5){
                $wednesday_in = new DateTime($request['wednesday_in']);
            }else{
                $wednesday_in =  new DateTime(substr($request['wednesday_in'], 0, -2));
            }

            //wednesday_out
            if(strlen($request['wednesday_out']) == 5){
                $wednesday_out = new DateTime($request['wednesday_out']);
            }else{
                $wednesday_out =  new DateTime(substr($request['wednesday_out'], 0, -2));
            }

            //thursday_in
            if(strlen($request['thursday_in']) == 5){
                $thursday_in = new DateTime($request['thursday_in']);
            }else{
                $thursday_in =  new DateTime(substr($request['thursday_in'], 0, -2));
            }

            //thursday_out
            if(strlen($request['thursday_out']) == 5){
                $thursday_out = new DateTime($request['thursday_out']);
            }else{
                $thursday_out =  new DateTime(substr($request['thursday_out'], 0, -2));
            }

            //friday_in
            if(strlen($request['friday_in']) == 5){
                $friday_in = new DateTime($request['friday_in']);
            }else{
                $friday_in =  new DateTime(substr($request['friday_in'], 0, -2));
            }

            //friday_out
            if(strlen($request['friday_out']) == 5){
                $friday_out = new DateTime($request['friday_out']);
            }else{
                $friday_out =  new DateTime(substr($request['friday_out'], 0, -2));
            }

            //saturday_in
            if(strlen($request['saturday_in']) == 5){
                $saturday_in = new DateTime($request['saturday_in']);
            }else{
                $saturday_in =  new DateTime(substr($request['saturday_in'], 0, -2));
            }

            //saturday_out
            if(strlen($request['saturday_out']) == 5){
                $saturday_out = new DateTime($request['saturday_out']);
            }else{
                $saturday_out =  new DateTime(substr($request['saturday_out'], 0, -2));
            }

            //sunday_in
            if(strlen($request['sunday_in']) == 5){
                $sunday_in = new DateTime($request['sunday_in']);
            }else{
                $sunday_in =  new DateTime(substr($request['sunday_in'], 0, -2));
            }

            //sunday_out
            if(strlen($request['sunday_out']) == 5){
                $sunday_out = new DateTime($request['sunday_out']);
            }else{
                $sunday_out =  new DateTime(substr($request['sunday_out'], 0, -2));
            }

            OfficeShift::whereId($id)->update([
                'company_id'     => $request['company_id'],
                'name'           => $request['name'],
                'monday_in'      => $request['monday_in']?$monday_in->format('H:iA'):Null,
                'monday_out'     => $request['monday_out']?$monday_out->format('H:iA'):Null,
                'tuesday_in'     => $request['tuesday_in']?$tuesday_in->format('H:iA'):Null,
                'tuesday_out'    => $request['tuesday_out']?$tuesday_out->format('H:iA'):Null,
                'wednesday_in'   => $request['wednesday_in']?$wednesday_in->format('H:iA'):Null,
                'wednesday_out'  => $request['wednesday_out']?$wednesday_out->format('H:iA'):Null,
                'thursday_in'    => $request['thursday_in']?$thursday_in->format('H:iA'):Null,
                'thursday_out'   => $request['thursday_out']?$thursday_out->format('H:iA'):Null,
                'friday_in'      => $request['friday_in']?$friday_in->format('H:iA'):Null,
                'friday_out'     => $request['friday_out']?$friday_out->format('H:iA'):Null,
                'saturday_in'    => $request['saturday_in']?$saturday_in->format('H:iA'):Null,
                'saturday_out'   => $request['saturday_out']?$saturday_out->format('H:iA'):Null,
                'sunday_in'      => $request['sunday_in']?$sunday_in->format('H:iA'):Null,
                'sunday_out'     => $request['sunday_out']?$sunday_out->format('H:iA'):Null,
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
		if ($user_auth->can('office_shift_delete')){


            OfficeShift::whereId($id)->update([
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
        if($user_auth->can('office_shift_delete')){
            $selectedIds = $request->selectedIds;
    
            foreach ($selectedIds as $office_shift_id) {
                OfficeShift::whereId($office_shift_id)->update([
                    'deleted_at' => Carbon::now(),
                ]);
            }
            return response()->json(['success' => true]);
        }
        return abort('403', __('You are not authorized'));
     }
}
