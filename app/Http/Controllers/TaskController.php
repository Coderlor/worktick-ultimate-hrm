<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Company;
use App\Models\Task;
use App\Models\TaskDiscussion;
use App\Models\TaskDocument;
use App\Models\Employee;
use App\Models\EmployeeTask;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_auth = auth()->user();
		if ($user_auth->can('task_view')){

            $count_not_started = Task::where('deleted_at', '=', null)
            ->where('status', '=', 'not_started')
            ->count();
            $count_in_progress = Task::where('deleted_at', '=', null)
            ->where('status', '=', 'progress')
            ->count();
            $count_cancelled = Task::where('deleted_at', '=', null)
            ->where('status', '=', 'cancelled')
            ->count();
            $count_completed = Task::where('deleted_at', '=', null)
            ->where('status', '=', 'completed')
            ->count();

            $tasks = Task::where('deleted_at', '=', null)->with('company:id,name','project:id,title')->orderBy('id', 'desc')->get();
            return view('task.task_list', compact('tasks','count_not_started','count_in_progress','count_cancelled','count_completed'));

        }
        return abort('403', __('You are not authorized'));
    }

    public function tasks_kanban()
    {
        $user_auth = auth()->user();
		if ($user_auth->can('kanban_task')){

            $tasks_not_started = Task::where('deleted_at', '=', null)
            ->where('status', '=', 'not_started')
            ->with('project:id,title')->orderBy('id', 'desc')->get();
            $tasks_in_progress = Task::where('deleted_at', '=', null)
            ->where('status', '=', 'progress')
            ->with('project:id,title')->orderBy('id', 'desc')->get();
            $tasks_cancelled = Task::where('deleted_at', '=', null)
            ->where('status', '=', 'cancelled')
            ->with('project:id,title')->orderBy('id', 'desc')->get();
            $tasks_completed = Task::where('deleted_at', '=', null)
            ->where('status', '=', 'completed')
            ->with('project:id,title')->orderBy('id', 'desc')->get();
            $tasks_hold = Task::where('deleted_at', '=', null)
            ->where('status', '=', 'hold')
            ->with('project:id,title')->orderBy('id', 'desc')->get();

            return view('task.kanban_task', compact('tasks_not_started','tasks_in_progress','tasks_cancelled','tasks_completed','tasks_hold'));

        }
        return abort('403', __('You are not authorized'));
    }

    public function task_change_status(Request $request)
    {
        $user_auth = auth()->user();
		if ($user_auth->can('kanban_task')){
            Task::whereId($request->task_id)->update([
                'status' => $request['status'],
            ]);
            return response()->json(['success' => true]);

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
		if ($user_auth->can('task_add')){

            $projects = Project::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','title']);
            $companies = Company::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','name']);
            
            return view('task.create_task', compact('projects','companies'));

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
		if ($user_auth->can('task_add')){

            $request->validate([
                'title'           => 'required|string|max:255',
                'summary'         => 'required|string|max:255',
                'project_id'      => 'required',
                'start_date'      => 'required',
                'end_date'        => 'required',
                'status'          => 'required',
                'company_id'      => 'required',
                'priority'          => 'required',
            ]);

            $task = Task::create([
                'title'            => $request['title'],
                'summary'          => $request['summary'],
                'start_date'       => $request['start_date'],
                'end_date'         => $request['end_date'],
                'project_id'       => $request['project_id'],
                'company_id'       => $request['company_id'],
                'status'           => $request['status'],
                'priority'         => $request['priority'],
                'task_progress'    => $request['task_progress'],
                'description'      => $request['description'],
            ]);

            $task->assignedEmployees()->sync($request['assigned_to']);

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
        $user_auth = auth()->user();
		if ($user_auth->can('task_details')){

            $task = Task::where('deleted_at', '=', null)->findOrFail($id);
            $discussions = TaskDiscussion::where('task_id' , $id)->where('deleted_at', '=', null)->with('User:id,username')->orderBy('id', 'desc')->get();
            $documents = TaskDocument::where('task_id' , $id)->where('deleted_at', '=', null)->orderBy('id', 'desc')->get();

            return view('task.task_details',
                compact('task','discussions','documents')
            );

        }
        return abort('403', __('You are not authorized'));

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
		if ($user_auth->can('task_edit')){

            $task = Task::where('deleted_at', '=', null)->findOrFail($id);
            $assigned_employees = EmployeeTask::where('task_id', $id)->pluck('employee_id')->toArray();
            $projects = Project::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','title']);
            $companies = Company::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','name']);
            $employees = Employee::where('company_id' , $task->company_id)->where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','username']);

            return view('task.edit_task', compact('task','projects','companies','employees','assigned_employees'));

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
		if ($user_auth->can('task_edit')){

            $request->validate([
                'title'           => 'required|string|max:255',
                'summary'         => 'required|string|max:255',
                'project_id'      => 'required',
                'start_date'      => 'required',
                'end_date'        => 'required',
                'status'          => 'required',
                'company_id'      => 'required',
                'priority'          => 'required',
            ]);

            Task::whereId($id)->update([
                'title'            => $request['title'],
                'summary'          => $request['summary'],
                'start_date'       => $request['start_date'],
                'end_date'         => $request['end_date'],
                'project_id'       => $request['project_id'],
                'company_id'       => $request['company_id'],
                'status'           => $request['status'],
                'priority'         => $request['priority'],
                'task_progress'    => $request['task_progress'],
                'description'      => $request['description'],
            ]);

            $task = Task::where('deleted_at', '=', null)->findOrFail($id);
            $task->assignedEmployees()->sync($request['assigned_to']);

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
		if ($user_auth->can('task_delete')){

            Task::whereId($id)->update([
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
         if($user_auth->can('task_delete')){
             $selectedIds = $request->selectedIds;
     
             foreach ($selectedIds as $task_id) {
                Task::whereId($task_id)->update([
                    'deleted_at' => Carbon::now(),
                ]);
             }
             return response()->json(['success' => true]);
         }
         return abort('403', __('You are not authorized'));
      }


    //---------------------Task Details -----------------------------\\

    public function Create_task_discussions(Request $request)
    {
        $user_auth = auth()->user();
		if ($user_auth->can('task_details')){

            $request->validate([
                'message'           => 'required|string',
            ]);

            TaskDiscussion::create([
                'message'            => $request['message'],
                'user_id'            => Auth::user()->id,
                'task_id'           => $request['task_id'],
            ]);

            return response()->json(['success' => true]);

        }
        return abort('403', __('You are not authorized'));
    }

    public function destroy_task_discussion($id)
    {
        $user_auth = auth()->user();
		if ($user_auth->can('task_details')){


            TaskDiscussion::whereId($id)->update([
                'deleted_at' => Carbon::now(),
            ]);

            return response()->json(['success' => true]);

        }
        return abort('403', __('You are not authorized'));
    }

    public function Create_task_documents(Request $request)
    {
        $user_auth = auth()->user();
		if ($user_auth->can('task_details')){

            $request->validate([
                'title'         => 'required|string|max:255',
                'attachment'    => 'required|file|mimes:pdf,docs,doc,pptx,jpeg,png,jpg,bmp,gif,svg|max:2048',

            ]);

            
            if ($request->hasFile('attachment')) {

                $image = $request->file('attachment');
                $attachment = time().'.'.$image->extension();  
                $image->move(public_path('/assets/images/tasks/documents'), $attachment);

            } else {
                $attachment = Null;
            }

            TaskDocument::create([
                'title'            => $request['title'],
                'task_id'          => $request['task_id'],
                'description'      => $request['description'],
                'attachment'       => $attachment,
            ]);

            return response()->json(['success' => true]);

        }
        return abort('403', __('You are not authorized'));
    }


    public function destroy_task_documents($id)
    {
        $user_auth = auth()->user();
		if ($user_auth->can('task_details')){

            TaskDocument::whereId($id)->update([
                'deleted_at' => Carbon::now(),
            ]);

            return response()->json(['success' => true]);

        }
        return abort('403', __('You are not authorized'));
    }


    public function update_task_status(Request $request, $id)
    {
        $user_auth = auth()->user();
		if ($user_auth->can('employee_edit')){

            $request->validate([
                'status'          => 'required',
            ]);

            Task::whereId($id)->update([
                'status'           => $request['status'],
            ]);

            return response()->json(['success' => true]);

        }
        return abort('403', __('You are not authorized'));
    }

}
