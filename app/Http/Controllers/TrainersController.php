<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Trainer;
use Carbon\Carbon;

class TrainersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_auth = auth()->user();
		if ($user_auth->can('trainer')){

            $trainers = Trainer::where('deleted_at', '=', null)->with('company:id,name')->orderBy('id', 'desc')->get();
            return view('training.trainers_list', compact('trainers'));

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
		if ($user_auth->can('trainer')){

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
		if ($user_auth->can('trainer')){

            request()->validate([
                'name'      => 'required|string|max:255',
                'email'     => 'required|string|email|max:255',
                'phone'     => 'nullable',
                'address'   => 'nullable|string|max:255',
                'company_id'      => 'required',
            ]);

            Trainer::create([
                'name'      => $request['name'],
                'email'     => $request['email'],
                'phone'     => $request['phone'],
                'address'   => $request['address'],
                'company_id'   => $request['company_id'],
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
		if ($user_auth->can('trainer')){

            request()->validate([
                'name'      => 'required|string|max:255',
                'email'     => 'required|string|email|max:255',
                'phone'     => 'nullable',
                'address'   => 'nullable|string|max:255',
                'company_id'      => 'required',
            ]);

            Trainer::whereId($id)->update([
                'name'      => $request['name'],
                'email'     => $request['email'],
                'phone'     => $request['phone'],
                'address'   => $request['address'],
                'company_id'   => $request['company_id'],
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
		if ($user_auth->can('trainer')){

            Trainer::whereId($id)->update([
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
         if($user_auth->can('trainer')){
             $selectedIds = $request->selectedIds;
     
             foreach ($selectedIds as $trainer_id) {
                Trainer::whereId($trainer_id)->update([
                    'deleted_at' => Carbon::now(),
                ]);
             }
             return response()->json(['success' => true]);
         }
         return abort('403', __('You are not authorized'));
      }



    
    public function Get_all_trainers()
    {
        $trainers = Trainer::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','name']);

        return response()->json($trainers);
    }
}
