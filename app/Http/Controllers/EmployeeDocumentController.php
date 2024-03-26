<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmployeeDocument;
use App\Models\Employee;
use Carbon\Carbon;

class EmployeeDocumentController extends Controller
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
                'attachment'      => 'required|file|mimes:pdf,docs,doc,pptx,jpeg,png,jpg,bmp,gif,svg,txt|max:2048',
            ]);



            $document = $request->file('attachment');
            $filename = time().'.'.$document->extension();  
            $document->move(public_path('/assets/employee/documents'), $filename);

            EmployeeDocument::create([
                'employee_id'     => $request['employee_id'],
                'title'    => $request['title'],
                'description'  => $request['description'],
                'attachment'    => $filename,
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
                'attachment'      => 'nullable|file|mimes:pdf,docs,doc,pptx,jpeg,png,jpg,bmp,gif,svg,txt|max:2048',
            ]);

            $EmployeeDocument = EmployeeDocument::findOrFail($id);

            $CurrentAttachement = $EmployeeDocument->attachment;
            if ($request->attachment != null) {
                if ($request->attachment != $CurrentAttachement) {

                    $attach = $request->file('attachment');
                    $filename = time().'.'.$attach->extension();  
                    $attach->move(public_path('/assets/employee/documents'), $filename);
                    $path = public_path() . '/assets/employee/documents';
                    $Document = $path . '/' . $CurrentAttachement;
                    if (file_exists($Document)) {
                            @unlink($Document);
                    }
                } else {
                    $filename = $CurrentAttachement;
                }
            }else{
                $filename = $CurrentAttachement;
            }

            EmployeeDocument::whereId($id)->update([
                'employee_id'     => $request['employee_id'],
                'title'    => $request['title'],
                'description'   => $request['description'],
                'attachment'    => $filename,
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

            EmployeeDocument::whereId($id)->update([
                'deleted_at' => Carbon::now(),
            ]);

            return response()->json(['success' => true]);

        }
        return abort('403', __('You are not authorized'));
    }
}
