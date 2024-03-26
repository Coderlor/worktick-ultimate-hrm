@extends('layouts.master')
@section('main-content')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">


@endsection

<div class="breadcrumb">
    <h1>{{ __('translate.Task_List') }}</h1>
    <ul>
        <li><a href="/tasks">{{ __('translate.Tasks') }}</a></li>
        <li>{{ __('translate.Task_List') }}</li>
    </ul>
</div>

<div class="separator-breadcrumb border-top"></div>

<div class="row">
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
            <div class="card-body text-center">
                <i class="i-Yes"></i>
                <div class="content">
                    <p class="text-muted mt-2 mb-0">{{ __('translate.Completed') }}</p>
                    <p class="text-primary text-24 line-height-1 mb-2">{{$count_completed}}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
            <div class="card-body text-center">
                <i class="i-Loading-3"></i>
                <div class="content">
                    <p class="text-muted mt-2 mb-0">{{ __('translate.In_Progress') }}</p>
                    <p class="text-primary text-24 line-height-1 mb-2">{{$count_in_progress}}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
            <div class="card-body text-center">
                <i class="i-Pause"></i>
                <div class="content">
                    <p class="text-muted mt-2 mb-0">{{ __('translate.Not_Started') }}</p>
                    <p class="text-primary text-24 line-height-1 mb-2">{{$count_not_started}}</p>
                </div>
            </div>
        </div>
    </div>



    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
            <div class="card-body text-center">
                <i class="i-Close"></i>
                <div class="content">
                    <p class="text-muted mt-2 mb-0">{{ __('translate.Cancelled') }}</p>
                    <p class="text-primary text-24 line-height-1 mb-2">{{$count_cancelled}}</p>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row" id="section_Task_list">
    <div class="col-md-12">
        <div class="card text-left">
            <div class="card-header text-right bg-transparent">
                <a class="btn btn-light btn-md m-1" href="{{route('tasks_kanban')}}">
                    <i class="i-Two-Windows text-white mr-2"></i> {{ __('translate.Kanban_View') }}</a>
                @can('task_add')
                <a class="btn btn-primary btn-md m-1" href="{{route('tasks.create')}}"><i
                        class="i-Add text-white mr-2"></i> {{ __('translate.Create') }}</a>
                @endcan
                @can('task_delete')
                <a v-if="selectedIds.length > 0" class="btn btn-danger btn-md m-1" @click="delete_selected()"><i
                        class="i-Close-Window text-white mr-2"></i> {{ __('translate.Delete') }}</a>
                @endcan
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="task_list_table" class="display table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>#</th>
                                <th>{{ __('translate.Task') }}</th>
                                <th>{{ __('translate.Company') }}</th>
                                <th>{{ __('translate.Project') }}</th>
                                <th>{{ __('translate.Start_Date') }}</th>
                                <th>{{ __('translate.Finish_Date') }}</th>
                                <th>{{ __('translate.Status') }}</th>
                                <th>{{ __('translate.Progress') }}</th>
                                <th>{{ __('translate.Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tasks as $task)
                            <tr>
                                <td></td>
                                <td @click="selected_row( {{ $task->id}})"></td>
                                <td><a href="/tasks/{{$task->id}}">{{$task->title}}</a></td>
                                <td>{{$task->company->name}}</td>
                                <td><a href="/projects/{{$task->project->id}}">{{$task->project->title}}</a></td>
                                <td>{{$task->start_date}}</td>
                                <td>{{$task->end_date}}</td>
                                <td>
                                    @if($task->status == 'completed')
                                    <span class="badge badge-success m-2">{{ __('translate.Completed') }}</span>
                                    @elseif($task->status == 'not_started')
                                    <span class="badge badge-warning m-2">{{ __('translate.Not_Started') }}</span>
                                    @elseif($task->status == 'progress')
                                    <span class="badge badge-primary m-2">{{ __('translate.In_Progress') }}</span>
                                    @elseif($task->status == 'cancelled')
                                    <span class="badge badge-danger m-2">{{ __('translate.Cancelled') }}</span>
                                    @elseif($task->status == 'hold')
                                    <span class="badge badge-secondary m-2">{{ __('translate.On_Hold') }}</span>
                                    @endif
                                </td>
                                <td>{{$task->task_progress}} %</td>
                                <td>
                                    @can('task_details')
                                    <a href="/tasks/{{$task->id}}" class="ul-link-action text-info"
                                        data-toggle="tooltip" data-placement="top" title="Show">
                                        <i class="i-Eye"></i>
                                    </a>
                                    @endcan

                                    @can('task_edit')
                                    <a href="/tasks/{{$task->id}}/edit" class="ul-link-action text-success"
                                        data-toggle="tooltip" data-placement="top" title="Edit">
                                        <i class="i-Edit"></i>
                                    </a>
                                    @endcan

                                    @can('task_delete')
                                    <a @click="Remove_Task( {{ $task->id}})" class="ul-link-action text-danger mr-1"
                                        data-toggle="tooltip" data-placement="top" title="Delete">
                                        <i class="i-Close-Window"></i>
                                    </a>
                                    @endcan

                                </td>
                            </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-js')

<script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
<script src="{{asset('assets/js/datatables.script.js')}}"></script>


<script>
    var app = new Vue({
        el: '#section_Task_list',
        data: {
            SubmitProcessing:false,
            selectedIds:[],
        },
       
        methods: {

            //---- Event selected_row
            selected_row(id) {
                //in here you can check what ever condition  before append to array.
                if(this.selectedIds.includes(id)){
                    const index = this.selectedIds.indexOf(id);
                    this.selectedIds.splice(index, 1);
                }else{
                    this.selectedIds.push(id)
                }
            },

            //--------------------------------- Remove Task ---------------------------\\
            Remove_Task(id) {

                swal({
                    title: '{{ __('translate.Are_you_sure') }}',
                    text: '{{ __('translate.You_wont_be_able_to_revert_this') }}',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#0CC27E',
                    cancelButtonColor: '#FF586B',
                    confirmButtonText: '{{ __('translate.Yes_delete_it') }}',
                    cancelButtonText: '{{ __('translate.No_cancel') }}',
                    confirmButtonClass: 'btn btn-primary mr-5',
                    cancelButtonClass: 'btn btn-danger',
                    buttonsStyling: false
                }).then(function () {
                        axios
                            .delete("/tasks/" + id)
                            .then(() => {
                                window.location.href = '/tasks'; 
                                toastr.success('{{ __('translate.Deleted_in_successfully') }}');

                            })
                            .catch(() => {
                                toastr.error('{{ __('translate.There_was_something_wronge') }}');
                            });
                    });
                },

                       
                 //--------------------------------- delete_selected ---------------------------\\
            delete_selected() {
                var self = this;
                swal({
                    title: '{{ __('translate.Are_you_sure') }}',
                    text: '{{ __('translate.You_wont_be_able_to_revert_this') }}',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#0CC27E',
                    cancelButtonColor: '#FF586B',
                    confirmButtonText: '{{ __('translate.Yes_delete_it') }}',
                    cancelButtonText: '{{ __('translate.No_cancel') }}',
                    confirmButtonClass: 'btn btn-primary mr-5',
                    cancelButtonClass: 'btn btn-danger',
                    buttonsStyling: false
                }).then(function () {
                        axios
                        .post("/tasks/delete/by_selection", {
                            selectedIds: self.selectedIds
                        })
                            .then(() => {
                                window.location.href = '/tasks'; 
                                toastr.success('{{ __('translate.Deleted_in_successfully') }}');

                            })
                            .catch(() => {
                                toastr.error('{{ __('translate.There_was_something_wronge') }}');
                            });
                    });
            },






           
        },
        //-----------------------------Autoload function-------------------
        created() {
        }

    })

</script>

<script type="text/javascript">
    $(function () {
      "use strict";

    $('#task_list_table').DataTable( {
        "processing": true, // for show progress bar
        select: {
            style: 'multi',
            selector: '.select-checkbox',
            items: 'row',
        },
        responsive: {
            details: {
                type: 'column',
                target: 0
            }
        },
        columnDefs: [{
            targets: 0,
                className: 'control'
            },
            {
                targets: 1,
                className: 'select-checkbox'
            },
            {
                targets: [0, 1],
                orderable: false
            }
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
});
</script>
@endsection