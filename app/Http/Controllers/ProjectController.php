<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Task;
use App\Models\Project;
use App\Models\ProjectDiscussion;
use App\Models\ProjectDocument;
use App\Models\ProjectIssue;
use App\Models\EmployeeProject;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_auth = auth()->user();
		if ($user_auth->can('project_view')){
            $count_not_started = Project::where('deleted_at', '=', null)
            ->where('status', '=', 'not_started')
            ->count();
            $count_in_progress = Project::where('deleted_at', '=', null)
            ->where('status', '=', 'progress')
            ->count();
            $count_cancelled = Project::where('deleted_at', '=', null)
            ->where('status', '=', 'cancelled')
            ->count();
            $count_completed = Project::where('deleted_at', '=', null)
            ->where('status', '=', 'completed')
            ->count();

            $projects = Project::where('deleted_at', '=', null)
            ->with('company:id,name','client:id,username')
            ->orderBy('id', 'desc')
            ->get();
           
            return view('project.project_list', compact('projects','count_not_started','count_in_progress','count_cancelled','count_completed'));

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
		if ($user_auth->can('project_add')){

            $clients = Client::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','username']);
            $companies = Company::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','name']);
            return view('project.create_project', compact('clients','companies'));

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
		if ($user_auth->can('project_add')){

            $request->validate([
                'title'           => 'required|string|max:255',
                'summary'         => 'required|string|max:255',
                'client'          => 'required',
                'company_id'      => 'required',
                'assigned_to'     => 'required',
                'start_date'      => 'required',
                'end_date'        => 'required',
                'priority'        => 'required',
                'status'          => 'required',
            ]);

            $project  = Project::create([
                'title'            => $request['title'],
                'summary'          => $request['summary'],
                'start_date'       => $request['start_date'],
                'end_date'         => $request['end_date'],
                'company_id'       => $request['company_id'],
                'client_id'        => $request['client'],
                'priority'         => $request['priority'],
                'status'           => $request['status'],
                'project_progress' => $request['project_progress'],
                'description'      => $request['description'],
            ]);

            $project->assignedEmployees()->sync($request['assigned_to']);

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
		if ($user_auth->can('project_details')){

            $project = Project::where('deleted_at', '=', null)->findOrFail($id);
            $discussions = ProjectDiscussion::where('project_id' , $id)
            ->where('deleted_at', '=', null)
            ->with('User:id,username')
            ->orderBy('id', 'desc')
            ->get();

            $issues = ProjectIssue::where('project_id' , $id)
            ->where('deleted_at', '=', null)
            ->orderBy('id', 'desc')
            ->get();

            $documents = ProjectDocument::where('project_id' , $id)
            ->where('deleted_at', '=', null)
            ->orderBy('id', 'desc')
            ->get();

            $tasks = Task::where('project_id' , $id)
            ->where('deleted_at', '=', null)
            ->orderBy('id', 'desc')
            ->get();

            $companies = Company::where('deleted_at', '=', null)
            ->orderBy('id', 'desc')
            ->get(['id','name']);

            return view('project.project_details',
                compact('project','discussions','issues','documents','companies','tasks')
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
		if ($user_auth->can('project_edit')){

            $project = Project::where('deleted_at', '=', null)->findOrFail($id);
            $assigned_employees = EmployeeProject::where('project_id', $id)->pluck('employee_id')->toArray();
            $clients = Client::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','username']);
            $companies = Company::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','name']);
            $employees = Employee::where('company_id' , $project->company_id)->where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','username']);

            return view('project.edit_project', compact('project','companies','clients','employees','assigned_employees'));

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
		if ($user_auth->can('project_edit')){

            $request->validate([
                'title'           => 'required|string|max:255',
                'summary'         => 'required|string|max:255',
                'client'          => 'required',
                'company_id'      => 'required',
                'assigned_to'     => 'required',
                'start_date'      => 'required',
                'end_date'        => 'required',
                'priority'        => 'required',
                'status'          => 'required',
            ]);

            Project::whereId($id)->update([
                'title'            => $request['title'],
                'summary'          => $request['summary'],
                'start_date'       => $request['start_date'],
                'end_date'         => $request['end_date'],
                'company_id'       => $request['company_id'],
                'client_id'        => $request['client'],
                'priority'         => $request['priority'],
                'status'           => $request['status'],
                'project_progress' => $request['project_progress'],
                'description'      => $request['description'],
            ]);

            $project = Project::where('deleted_at', '=', null)->findOrFail($id);
            $project->assignedEmployees()->sync($request['assigned_to']);

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
		if ($user_auth->can('project_delete')){

            Project::whereId($id)->update([
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
         if($user_auth->can('project_delete')){
             $selectedIds = $request->selectedIds;
     
             foreach ($selectedIds as $project_id) {
                Project::whereId($project_id)->update([
                    'deleted_at' => Carbon::now(),
                ]);
             }
             return response()->json(['success' => true]);
         }
         return abort('403', __('You are not authorized'));
      }


    //-----------Project Details--------------------------------\\

    public function Create_project_discussions(Request $request)
    {
        $user_auth = auth()->user();
		if ($user_auth->can('project_details')){

            $request->validate([
                'message'           => 'required|string',
            ]);

            ProjectDiscussion::create([
                'message'            => $request['message'],
                'user_id'            => Auth::user()->id,
                'project_id'        => $request['project_id'],
            ]);

            return response()->json(['success' => true]);

        }
        return abort('403', __('You are not authorized'));
    }

    public function destroy_project_discussion($id)
    {
        $user_auth = auth()->user();
		if ($user_auth->can('project_details')){

            ProjectDiscussion::whereId($id)->update([
                'deleted_at' => Carbon::now(),
            ]);

            return response()->json(['success' => true]);

        }
        return abort('403', __('You are not authorized'));
    }


    public function Create_project_issues(Request $request)
    {
        $user_auth = auth()->user();
		if ($user_auth->can('project_details')){

            $request->validate([
                'title'         => 'required|string|max:255',
                'comment'       => 'required',
                'attachment'    => 'nullable|file|mimes:pdf,docs,doc,pptx,jpeg,png,jpg,bmp,gif,svg|max:2048',

            ]);

            
            if ($request->hasFile('attachment')) {

                $image = $request->file('attachment');
                $attachment = time().'.'.$image->extension();  
                $image->move(public_path('/assets/images/projects/issues'), $attachment);

            } else {
                $attachment = Null;
            }

            ProjectIssue::create([
                'title'            => $request['title'],
                'project_id'       => $request['project_id'],
                'comment'          => $request['comment'],
                'label'            => $request['label'],
                'status'           => 'pending',
                'attachment'       => $attachment,
            ]);

            return response()->json(['success' => true]);

        }
        return abort('403', __('You are not authorized'));
    }

    public function Update_project_issues(Request $request , $id)
    {
        $user_auth = auth()->user();
		if ($user_auth->can('project_details')){

            $request->validate([
                'title'         => 'required|string|max:255',
                'comment'       => 'required',
                'attachment'    => 'nullable|file|mimes:pdf,docs,doc,pptx,jpeg,png,jpg,bmp,gif,svg|max:2048',

            ]);

            //upload attachment

            $project_issue = ProjectIssue::findOrFail($id);
        
            $currentAttachment = $project_issue->attachment;

            if ($request->attachment != null) {
                if ($request->attachment != $currentAttachment) {

                    $image = $request->file('attachment');
                    $attachment = time().'.'.$image->extension();  
                    $image->move(public_path('/assets/images/projects/issues'), $attachment);
                    $path = public_path() . '/assets/images/projects/issues';
                    $project_issue_attachment = $path . '/' . $currentAttachment;
                    if (file_exists($project_issue_attachment)) {
                            @unlink($project_issue_attachment);
                    }
                } else {
                    $attachment = $currentAttachment;
                }
            }else{
                $attachment = $currentAttachment;
            }

            ProjectIssue::whereId($id)->update([
                'title'            => $request['title'],
                'project_id'       => $request['project_id'],
                'comment'          => $request['comment'],
                'label'            => $request['label'],
                'status'           => $request['status'],
                'attachment'       => $attachment,
            ]);

            return response()->json(['success' => true]);

        }
        return abort('403', __('You are not authorized'));
    }


    public function destroy_project_issues($id)
    {
        $user_auth = auth()->user();
		if ($user_auth->can('project_details')){

            ProjectIssue::whereId($id)->update([
                'deleted_at' => Carbon::now(),
            ]);

            return response()->json(['success' => true]);

        }
        return abort('403', __('You are not authorized'));
    }

    
    public function Create_project_documents(Request $request)
    {
        $user_auth = auth()->user();
		if ($user_auth->can('project_details')){

            $request->validate([
                'title'         => 'required|string|max:255',
                'attachment'    => 'required|file|mimes:pdf,docs,doc,pptx,jpeg,png,jpg,bmp,gif,svg|max:2048',

            ]);

            
            if ($request->hasFile('attachment')) {

                $image = $request->file('attachment');
                $attachment = time().'.'.$image->extension();  
                $image->move(public_path('/assets/images/projects/documents'), $attachment);

            } else {
                $attachment = Null;
            }

            ProjectDocument::create([
                'title'            => $request['title'],
                'project_id'       => $request['project_id'],
                'description'      => $request['description'],
                'attachment'       => $attachment,
            ]);

            return response()->json(['success' => true]);

        }
        return abort('403', __('You are not authorized'));
    }


    public function destroy_project_documents($id)
    {
        $user_auth = auth()->user();
		if ($user_auth->can('project_details')){

            ProjectDocument::whereId($id)->update([
                'deleted_at' => Carbon::now(),
            ]);

            return response()->json(['success' => true]);

        }
        return abort('403', __('You are not authorized'));
    }
}
