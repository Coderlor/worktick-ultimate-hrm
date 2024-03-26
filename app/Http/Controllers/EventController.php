<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Employee;
use App\Models\Company;
use App\Models\Department;
use Carbon\Carbon;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_auth = auth()->user();
		if ($user_auth->can('event_view')){

            $events = Event::where('deleted_at', '=', null)->orderBy('id', 'desc')->get();
            return view('hr.event.event_list', compact('events'));

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
		if ($user_auth->can('event_add')){

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
		if ($user_auth->can('event_add')){

            request()->validate([
                'title'           => 'required|string|max:255',
                'date'            => 'required',
                'time'            => 'required',
                'status'            => 'required',
                'company_id'            => 'required',
                'department_id'            => 'required',
            ]);

            Event::create([
                'title'           => $request['title'],
                'date'            => $request['date'],
                'time'            => $request['time'],
                'note'            => $request['note'],
                'status'            => $request['status'],
                'company_id'     => $request['company_id'],
                'department_id'     => $request['department_id'],
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
		if ($user_auth->can('event_edit')){

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
		if ($user_auth->can('event_edit')){

            request()->validate([
                'title'           => 'required|string|max:255',
                'date'            => 'required',
                'time'            => 'required',
                'status'            => 'required',
                'company_id'            => 'required',
                'department_id'            => 'required',
                
            ]);

            Event::whereId($id)->update([
                'title'           => $request['title'],
                'date'            => $request['date'],
                'time'            => $request['time'],
                'note'            => $request['note'],
                'status'            => $request['status'],
                'company_id'     => $request['company_id'],
                'department_id'     => $request['department_id'],
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
		if ($user_auth->can('event_delete')){

            Event::whereId($id)->update([
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
          if($user_auth->can('event_delete')){
              $selectedIds = $request->selectedIds;
      
              foreach ($selectedIds as $event_id) {
                Event::whereId($event_id)->update([
                    'deleted_at' => Carbon::now(),
                ]);
              }
              return response()->json(['success' => true]);
          }
          return abort('403', __('You are not authorized'));
       }

}
