@extends('layouts.master')
@section('main-content')
@section('page-css')
<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/styles/vendor/datepicker.min.css')}}">


@endsection

<div class="breadcrumb">
    <h1>{{ __('translate.Daily_Attendance') }}</h1>
    <ul>
        <li><a href="/daily_attendance">{{ __('translate.Attendance') }}</a></li>
        <li>{{ __('translate.Daily_Attendance') }}</li>
    </ul>
</div>

<div class="separator-breadcrumb border-top"></div>

<div class="row" id="section_daily_attendance">
    <div class="col-md-12">
        <div class="card text-left">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="ul-contact-list" class="display table daily_attendance_datatable">
                        <thead>
                            <tr>
                                <th>{{ __('translate.Employee') }}</th>
                                <th>{{ __('translate.Company') }}</th>
                                <th>{{ __('translate.Date') }}</th>
                                <th>{{ __('translate.Time_In') }}</th>
                                <th>{{ __('translate.Time_Out') }}</th>
                                <th>{{ __('translate.Time_Late') }}</th>
                                <th>{{ __('translate.Depart_early') }}</th>
                                <th>{{ __('translate.Overtime') }}</th>
                                <th>{{ __('translate.Work_Duration') }}</th>
                                <th>{{ __('translate.Rest_Duration') }}</th>
                                <th>{{ __('translate.Status') }}</th>
                            </tr>
                        </thead>


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

<script type="text/javascript">
    $(function () {
      "use strict";

     
        // init datatable.
        daily_attendance_datatable();
        function daily_attendance_datatable(){
            var dataTable = $('#ul-contact-list').DataTable({
                processing: true,
                responsive: true,
                serverSide: true,
                autoWidth: false,
                pageLength: 10,
                "order": [[ 0, "desc" ]],
                ajax: {
                    url: "{{ route('daily_attendance') }}",
                  
                },

                columns: [
                    {data: 'username', name: 'Employee'},
                    {data: 'company', name: 'Company'},
                    {data: 'date', name: 'Date'},
                    {data: 'clock_in', name: 'Time In'},
                    {data: 'clock_out', name: 'Time Out'},
                    {data: 'late_time', name: 'Time Late'},

                    {data: 'depart_early', name: 'Depart early'},
                    {data: 'overtime', name: 'Overtime'},
                    {data: 'total_work', name: 'Work Duration'},
                    {data: 'total_rest', name: 'Rest Duration'},
                    {data: 'status', name: 'Status'},
                ],
                dom: "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'f><'col-sm-12 col-md-4'B>>rtip",
                buttons: [
                    'csv', 'excel', 'pdf', 'print','colvis'
                ]
            });
        }

      
    });
</script>


@endsection