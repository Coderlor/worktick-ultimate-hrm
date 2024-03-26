@extends('layouts.master')
@section('main-content')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

<div class="breadcrumb">
    <h1>{{ __('translate.Settings') }}</h1>
    <ul>
        <li>{{ __('translate.Backup') }}</li>
        <li>{{ __('translate.Settings') }}</li>
    </ul>
</div>

<div class="separator-breadcrumb border-top"></div>

<div class="row" id="section_backup_list">
    <div class="col-md-12">
        <div class="card text-left">
            <span class="alert alert-danger m-3">{{__('translate.You_will_find_your_backup_on')}}
                <strong>/storage/app/public/backup</strong> {{__('translate.and_save_it_to_your_pc')}}</span>
            <div class="card-header text-right bg-transparent">


                <a :disabled="SubmitProcessing" id="generate_backup" @click="generate_backup"
                    class="btn btn-primary btn-md m-1"><i class="i-Add text-white mr-2"></i>
                    {{ __('translate.Generate_backup') }}</a>
                <div v-once class="typo__p" v-if="SubmitProcessing">
                    <div class="spinner spinner-primary mt-3"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="ul-contact-list" class="display table">
                        <thead>
                            <tr>
                                <th>{{ __('translate.Date') }}</th>
                                <th>{{ __('translate.File_size') }}</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>


                    </table>
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
        backup_datatable();
        function backup_datatable(){
            var dataTable = $('#ul-contact-list').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                responsive: true,
                buttons: [],
                pageLength: 5,
                "order": [[ 0, "desc" ]],
                ajax: {
                    url: "/settings/backup",
                },

                columns: [
                    {data: 'date', name: 'Date'},
                    {data: 'size', name: 'File Size'},
                ],
                dom: "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'f><'col-sm-12 col-md-4'B>>rtip",
               
            });
        }
       
    });
    </script>
    <script>
        var app = new Vue({
        el: '#section_backup_list',
        data: {
            SubmitProcessing:false,
            errors:[],
           
        },
       
        methods: {

     
            //------------------------ generate_backup ---------------------------\\
            generate_backup() {
                var self = this;
                self.SubmitProcessing = true;
                axios.get("/GenerateBackup").then(response => {
                        self.SubmitProcessing = false;
                        window.location.href = '/settings/backup'; 
                        toastr.success('{{ __('translate.Created_in_successfully') }}');
                        self.errors = {};
                })
                .catch(error => {
                    self.SubmitProcessing = false;
                    if (error.response.status == 422) {
                        self.errors = error.response.data.errors;
                    }
                    toastr.error('{{ __('translate.There_was_something_wronge') }}');
                });
            },

   
           
        },
        //-----------------------------Autoload function-------------------
        created() {
        }

    })

    </script>

    @endsection