@extends('layouts.master')
@section('main-content')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">

@endsection

<div class="breadcrumb">
    <h1>{{ __('translate.Settings') }}</h1>
    <ul>
        <li><a href="/settings/permissions">{{ __('translate.Permissions') }}</a></li>
        <li>{{ __('translate.Settings') }}</li>
    </ul>
</div>

<div class="separator-breadcrumb border-top"></div>

<div class="row" id="section_Permissions_list">
    <div class="col-md-12">
        <div class="card text-left">
            <div class="card-header text-right bg-transparent">

                <a class="btn btn-primary btn-md m-1" href="{{route('permissions.create')}}"><i
                        class="i-Add text-white mr-2"></i> {{ __('translate.Create') }}</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="ul-contact-list" class="display table">
                        <thead>
                            <tr>
                                <th>{{ __('translate.Role_Name') }}</th>
                                <th>{{ __('translate.Description') }}</th>
                                <th>{{ __('translate.Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $role)
                            <tr>
                                <td>{{$role->name}}</td>
                                <td>{{$role->description}}</td>
                                @can('group_permission')
                                @if($role->id === 1 || $role->id === 2 || $role->id === 3)
                                <td>{{ __('translate.Cannot_change_Default_Permissions') }}</td>
                                @else
                                <td>
                                    <a href="/settings/permissions/{{$role->id}}/edit"
                                        class="ul-link-action text-success" data-toggle="tooltip" data-placement="top"
                                        title="Edit">
                                        <i class="i-Edit"></i>
                                    </a>
                                    <a @click="Remove_role( {{ $role->id}})" class="ul-link-action text-danger mr-1"
                                        data-toggle="tooltip" data-placement="top" title="Delete">
                                        <i class="i-Close-Window"></i>
                                    </a>
                                </td>
                                @endif
                                @endcan
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
        el: '#section_Permissions_list',
        data: {
            SubmitProcessing:false,
            errors:[],
        },
       
        methods: {

             //--------------------------------- Remove Role ---------------------------\\
             Remove_role(id) {

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
                            .delete("/settings/permissions/" + id)
                            .then(() => {
                                window.location.href = '/settings/permissions'; 
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

        $('#ul-contact-list').DataTable( {
            "processing": true, // for show progress bar
            "responsive": true,
            dom: "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'f><'col-sm-12 col-md-4'B>>rtip",
            buttons: [
                'csv', 'excel', 'pdf', 'print','colvis'
            ]
        });

    });
</script>
@endsection