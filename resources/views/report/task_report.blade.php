@extends('layouts.master')
@section('main-content')
@section('page-css')
<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">


@endsection

<div class="breadcrumb">
    <h1>{{ __('translate.Task') }}</h1>
    <ul>
        <li><a href="/report/task">{{ __('translate.Task_Report') }}</a></li>
        <li>{{ __('translate.Task') }}</li>
    </ul>
</div>

<div class="separator-breadcrumb border-top"></div>

<div class="row" id="section_Task_report">
    <div class="col-md-12">
        <div class="card text-left">
            <div class="card-header text-right bg-transparent">

                <a class="btn btn-primary btn-md m-1" id="Show_Modal_Filter"><i class="i-Filter-2 text-white mr-2"></i>
                    {{ __('translate.Filter') }}</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="ul-contact-list" class="display table task_datatable">
                        <thead>
                            <tr>
                                <th>{{ __('translate.Task') }}</th>
                                <th>{{ __('translate.Company') }}</th>
                                <th>{{ __('translate.Project') }}</th>
                                <th>{{ __('translate.Start_Date') }}</th>
                                <th>{{ __('translate.Finish_Date') }}</th>
                                <th>{{ __('translate.Priority') }}</th>
                                <th>{{ __('translate.Status') }}</th>
                                <th>{{ __('translate.Progress') }}</th>
                            </tr>
                        </thead>


                    </table>
                </div>
            </div>
        </div>
        <!-- Modal Filter Task -->
        <div class="modal fade" id="Filter_Task_Modal" tabindex="-1" role="dialog" aria-labelledby="Filter_Task_Modal"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('translate.Filter') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <form method="POST" id="Filter_task_report">
                            @csrf
                            <div class="row">

                                <div class="col-md-6">
                                    <label for="title" class="ul-form__label">{{ __('translate.Title') }} </label>
                                    <input type="text" class="form-control" name="title" id="title"
                                        placeholder="{{ __('translate.Enter_title') }}">
                                </div>

                                <div class="col-md-6">
                                    <label for="company_id" class="ul-form__label">{{ __('translate.Company') }}
                                    </label>
                                    <select name="company_id" id="company_id" class="form-control">
                                        <option value="0">{{ __('translate.All') }}</option>
                                        @foreach ($companies as $company)
                                        <option value="{{$company->id}}">{{$company->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="project_id" class="ul-form__label">{{ __('translate.Project') }}
                                    </label>
                                    <select name="project_id" id="project_id" class="form-control">
                                        <option value="0">{{ __('translate.All') }}</option>
                                        @foreach ($projects as $project)
                                        <option value="{{$project->id}}">{{$project->title}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="status" class="ul-form__label">{{ __('translate.Status') }} </label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="0">{{ __('translate.All') }}</option>
                                        <option value="not_started">{{ __('translate.Not_Started ') }}</option>
                                        <option value="progress">{{ __('translate.In_Progress') }}</option>
                                        <option value="cancelled">{{ __('translate.Cancelled') }}</option>
                                        <option value="hold">{{ __('translate.On_Hold') }}</option>
                                        <option value="completed">{{ __('translate.Completed') }}</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="priority" class="ul-form__label">{{ __('translate.Priority') }} </label>
                                    <select name="priority" id="priority" class="form-control">
                                        <option value="0">{{ __('translate.All') }}</option>
                                        <option value="urgent">{{ __('translate.Urgent') }}</option>
                                        <option value="high">{{ __('translate.High') }}</option>
                                        <option value="medium">{{ __('translate.Medium') }}</option>
                                        <option value="low">{{ __('translate.Low') }}</option>
                                    </select>
                                </div>

                            </div>

                            <div class="row mt-3">

                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-outline-success">
                                        {{ __('translate.Filter') }}
                                    </button>

                                    <button id="Clear_Form" class="btn btn-outline-danger">
                                        {{ __('translate.Clear') }}
                                    </button>
                                </div>
                            </div>


                        </form>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-js')

<script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
<script src="{{asset('assets/js/datatables.script.js')}}"></script>

<script type="text/javascript">
    $(function () {
      "use strict";


        // init datatable.

        task_datatable();
        function task_datatable(title ='' , project_id ='' , company_id='' , priority='' , status=''){
            var dataTable = $('#ul-contact-list').DataTable({
                processing: true,
                responsive: true,
                serverSide: true,
                autoWidth: false,
                pageLength: 10,
                "order": [[ 0, "desc" ]],
                ajax: {
                    url: "{{ route('task_report_index') }}",
                    data: {
                        title: title === null?'':title,
                        project_id: project_id == '0'?'':project_id,
                        company_id: company_id == '0'?'':company_id,
                        priority: priority == '0'?'':priority,
                        status: status == '0'?'':status,
                        "_token": "{{ csrf_token()}}"
                    },
                },

                columns: [
                    {data: 'title', name: 'Task'},
                    {data: 'project.title', name: 'Project'},
                    {data: 'company.name', name: 'Company'},
                    {data: 'start_date', name: 'Start Date'},
                    {data: 'end_date', name: 'Finish Date'},
                    {data: 'priority', name: 'Priority'},
                    {data: 'status', name: 'Status'},
                    {
                        data: 'task_progress.' , 
                        name: 'Progress',
                        render: function(data){
                            return data + '%';
                        }
                    },
                ],
                dom: "<'row'<'col-sm-12 col-md-7'lB><'col-sm-12 col-md-5 p-0'f>>rtip",
                oLanguage:
                    { 
                    sLengthMenu: "_MENU_", 
                    sSearch: '',
                    sSearchPlaceholder: "Search..."
                },
                buttons: [
                {
                    extend: 'collection',
                    text: 'EXPORT',
                    buttons: [
                        'csv','excel', 'pdf', 'print'
                    ]
                }]
            });
        }

         // Clear Filter

         $('#Clear_Form').on('click' , function (e) {
            var title = $('#title').val('0');
            let project_id = $('#project_id').val('0');
            let company_id = $('#company_id').val('0');
            let priority = $('#priority').val('0');
            let status = $('#status').val('0');

            task_datatable(title, project_id, company_id, priority, status);
        });
        

         // Show Modal Filter

        $('#Show_Modal_Filter').on('click' , function (e) {
            $('#Filter_Task_Modal').modal('show');
        });


         // Submit Filter

        $('#Filter_task_report').on('submit' , function (e) {
            e.preventDefault();
            var title = $('#title').val();
            let project_id = $('#project_id').val();
            let company_id = $('#company_id').val();
            let priority = $('#priority').val();
            let status = $('#status').val();
      
            $('#ul-contact-list').DataTable().destroy();
            task_datatable(title, project_id, company_id, priority, status);

            $('#Filter_Task_Modal').modal('hide');
           
               
        });
       
    });
</script>


@endsection