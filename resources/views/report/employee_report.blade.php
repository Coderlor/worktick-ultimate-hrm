@extends('layouts.master')
@section('main-content')
@section('page-css')
<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/styles/vendor/datepicker.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/styles/vendor/bootstrap-select.min.css')}}">

@endsection

<div class="breadcrumb">
    <h1>{{ __('translate.Employee') }}</h1>
    <ul>
        <li><a href="/report/employee">{{ __('translate.Employee_Report') }}</a></li>
        <li>{{ __('translate.Employee') }}</li>
    </ul>
</div>

<div class="separator-breadcrumb border-top"></div>

<div class="row" id="section_Employee_report">
    <div class="col-md-12">
        <div class="card text-left">
            <div class="card-header text-right bg-transparent">

                <a class="btn btn-primary btn-md m-1" id="Show_Modal_Filter"><i class="i-Filter-2 text-white mr-2"></i>
                    {{ __('translate.Filter') }}</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="ul-contact-list" class="display table employee_datatable">
                        <thead>
                            <tr>
                                <th>{{ __('translate.FullName') }}</th>
                                <th>{{ __('translate.Phone') }}</th>
                                <th>{{ __('translate.Employment_Type') }}</th>
                                <th>{{ __('translate.Company') }}</th>
                                <th>{{ __('translate.Department') }}</th>
                                <th>{{ __('translate.Designation') }}</th>
                                <th>{{ __('translate.Office_Shift') }}</th>
                            </tr>
                        </thead>


                    </table>
                </div>
            </div>
        </div>
        <!-- Modal Filter employee -->
        <div class="modal fade" id="Filter_Employee_Modal" tabindex="-1" role="dialog"
            aria-labelledby="Filter_Employee_Modal" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('translate.Filter') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <form method="POST" id="Filter_Employee_report">
                            @csrf
                            <div class="row">

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="company_id">{{ __('translate.Company') }}</label>
                                        <select class="form-control selectpicker select_company" name="company_id"
                                            id="company_id" data-dependent="department" data-placeholder="Company"
                                            data-column="1" required="" tabindex="-1" aria-hidden="true">
                                            <option value="0">{{ __('translate.All') }}</option>
                                            @foreach($companies as $company)
                                            <option value="{{$company->id}}">{{$company->name}}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="department_id">{{ __('translate.Department') }}</label>
                                        <select class="form-control selectpicker designation default_department"
                                            name="department_id" data-designation_name="designation" id="department_id"
                                            data-placeholder="Department" required="" tabindex="-1" aria-hidden="true">
                                            <option value="0">{{ __('translate.All') }}</option>

                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="designation_id">{{ __('translate.Designation') }}</label>
                                        <select class="form-control selectpicker default_designation"
                                            name="designation_id" id="designation_id" data-placeholder="Designation"
                                            required="" tabindex="-1" aria-hidden="true">
                                            <option value="0">{{ __('translate.All') }}</option>
                                        </select>
                                    </div>
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
<script src="{{asset('assets/js/datepicker.min.js')}}"></script>
<script src="{{asset('assets/js/vendor/bootstrap-select.min.js')}}"></script>

<script type="text/javascript">
    $(function () {
      "use strict";

      $(document).ready(function () {

            let date = $('.date');
            date.datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
            });

        });


        // init datatable.

        employee_datatable();
        function employee_datatable(company_id ='' , department_id ='' , designation_id=''){
            var dataTable = $('#ul-contact-list').DataTable({
                processing: true,
                responsive: true,
                serverSide: true,
                autoWidth: false,
                pageLength: 10,
                "order": [[ 0, "desc" ]],
                ajax: {
                    url: "{{ route('employee_report_index') }}",
                    data: {
                        company_id: company_id == '0'?'':company_id,
                        department_id: department_id == '0'?'':department_id,
                        designation_id: designation_id == '0'?'':designation_id,

                        "_token": "{{ csrf_token()}}"
                    },
                },

                columns: [
                    {data: 'username', name: 'username'},
                    {data: 'phone', name: 'Phone'},
                    {data: 'employment_type', name: 'Employment Type'},
                    {data: 'company.name', name: 'Company'},
                    {data: 'department.department', name: 'Department'},
                    {data: 'designation.designation', name: 'Designation'},
                    {data: 'office_shift.name', name: 'Office Shift'},
                   
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
            var company_id = $('#company_id').val('0');
            let department_id = $('#department_id').val('0');
            let designation_id = $('#designation_id').val('0');

            employee_datatable(company_id, department_id, designation_id);
        });

         // Show Modal Filter

        $('#Show_Modal_Filter').on('click' , function (e) {
            $('#Filter_Employee_Modal').modal('show');
        });


         // Submit Filter

        $('#Filter_Employee_report').on('submit' , function (e) {
            e.preventDefault();
            var company_id = $('#company_id').val();
            let department_id = $('#department_id').val();
            let designation_id = $('#designation_id').val();
      
            $('#ul-contact-list').DataTable().destroy();
            employee_datatable(company_id, department_id, designation_id);

            $('#Filter_Employee_Modal').modal('hide');
        });


        $('.select_company').change(function() {
                if($(this).val() !==''){
                    let value = $(this).val();
                    let dependent = $(this).data('dependent');
                    let token = $('input[name="_token"]').val();
                    $.ajax({
                        method:'POST',
                        url: "{{ route('fetchDepartment') }}",
                        data: {
                            value: value,
                            dependent: dependent,
                            "_token": "{{ csrf_token()}}"
                        },
                        success:function(data){
                            $('#department_id').selectpicker("destroy");
                            $('#department_id').html(data);
                            $('.default_department').prepend('<option value="0" selected>All</option>');
                            $('#department_id').selectpicker();
                        }

                    });
                 }
        });

        $('.designation').change(function () {
            if ($(this).val() !== '') {
                let value = $(this).val();
                let designation_name = $(this).data('designation_name');
                let _token = $('input[name="_token"]').val();
                $.ajax({
                    url: "{{ route('fetchDesignation') }}",
                    method: "POST",
                    data: {value: value, _token: _token, designation_name: designation_name},
                    success: function (result) {
                        $('#designation_id').selectpicker("destroy");
                        $('#designation_id').html(result);
                        $('.default_designation').prepend('<option value="0" selected>All</option>');
                        $('#designation_id').selectpicker();

                    }
                });
            }
        });

       
    });
</script>


@endsection