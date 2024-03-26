@extends('layouts.master')
@section('main-content')
@section('page-css')
<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/styles/vendor/datepicker.min.css')}}">


@endsection

<div class="breadcrumb">
    <h1>{{ __('translate.Expense') }}</h1>
    <ul>
        <li><a href="/report/expense">{{ __('translate.Expense_Report') }}</a></li>
        <li>{{ __('translate.Expense') }}</li>
    </ul>
</div>

<div class="separator-breadcrumb border-top"></div>

<div class="row" id="section_expense_report">
    <div class="col-md-12">
        <div class="card text-left">
            <div class="card-header text-right bg-transparent">

                <a class="btn btn-primary btn-md m-1" id="Show_Modal_Filter"><i class="i-Filter-2 text-white mr-2"></i>
                    {{ __('translate.Filter') }}</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="ul-contact-list" class="display table expense_datatable">
                        <thead>
                            <tr>
                                <th>{{ __('translate.Account_Name') }}</th>
                                <th>{{ __('translate.Expense_Ref') }}</th>
                                <th>{{ __('translate.Date') }}</th>
                                <th>{{ __('translate.Amount') }}</th>
                                <th>{{ __('translate.Category') }}</th>
                                <th>{{ __('translate.Payment_method') }}</th>
                            </tr>
                        </thead>


                    </table>
                </div>
            </div>
        </div>
        <!-- Modal Filter Expense -->
        <div class="modal fade" id="Filter_Expense_Modal" tabindex="-1" role="dialog"
            aria-labelledby="Filter_Expense_Modal" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('translate.Filter') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <form method="POST" id="Filter_Expense_report">
                            @csrf
                            <div class="row">

                                <div class="col-md-6">
                                    <label for="expense_ref" class="ul-form__label">{{ __('translate.Expense_Ref') }}
                                    </label>
                                    <input type="text" class="form-control" name="expense_ref" id="expense_ref"
                                        placeholder="{{ __('translate.Enter_Expense_Ref') }}">
                                </div>

                                <div class="col-md-6">
                                    <label for="account_id" class="ul-form__label">{{ __('translate.Account') }}
                                    </label>
                                    <select name="account_id" id="account_id" class="form-control">
                                        <option value="0">{{ __('translate.All') }}</option>
                                        @foreach ($accounts as $account)
                                        <option value="{{$account->id}}">{{$account->account_name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="expense_category_id"
                                        class="ul-form__label">{{ __('translate.Category') }} </label>
                                    <select name="expense_category_id" id="expense_category_id" class="form-control">
                                        <option value="0">{{ __('translate.All') }}</option>
                                        @foreach ($categories as $category)
                                        <option value="{{$category->id}}">{{$category->title}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="payment_method_id"
                                        class="ul-form__label">{{ __('translate.Payment_method') }} </label>
                                    <select name="payment_method_id" id="payment_method_id" class="form-control">
                                        <option value="0">{{ __('translate.All') }}</option>
                                        @foreach ($payment_methods as $payment_method)
                                        <option value="{{$payment_method->id}}">{{$payment_method->title}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="start_date" class="ul-form__label">{{ __('translate.From_Date') }}
                                    </label>
                                    <input type="text" class="form-control date" name="start_date" id="start_date"
                                        placeholder="{{ __('translate.From_Date') }}" value="">
                                </div>

                                <div class="col-md-6">
                                    <label for="end_date" class="ul-form__label">{{ __('translate.To_Date') }} </label>
                                    <input type="text" class="form-control date" name="end_date" id="end_date"
                                        placeholder="{{ __('translate.To_Date') }}" value="">
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

<script type="text/javascript">
    $(function () {
      "use strict";

      $(document).ready(function () {

            $("#start_date,#end_date").datepicker({
                format: 'yyyy-mm-dd',
                changeMonth: true,
                changeYear: true,
                autoclose: true,
                todayHighlight: true,
                        });
            var lastDate = new Date();
            $("#end_date").datepicker("setDate" , lastDate);
            lastDate.setDate(lastDate.getDate() - 365);
            $("#start_date").datepicker("setDate" , lastDate);

        });


        // init datatable.
        expense_datatable();
        function expense_datatable(start_date ='' ,end_date ='', expense_ref='' , account_id='' , expense_category_id='' , payment_method_id=''){
            var dataTable = $('#ul-contact-list').DataTable({
                processing: true,
                responsive: true,
                serverSide: true,
                autoWidth: false,
                pageLength: 10,
                "order": [[ 0, "desc" ]],
                ajax: {
                    url: "{{ route('expense_report_index') }}",
                    data: {
                        start_date: start_date === null?'':start_date,
                        end_date: end_date === null?'':end_date,
                        expense_ref: expense_ref === null?'':expense_ref,
                        account_id: account_id == '0'?'':account_id,
                        expense_category_id: expense_category_id == '0'?'':expense_category_id,
                        payment_method_id: payment_method_id == '0'?'':payment_method_id,
                        "_token": "{{ csrf_token()}}"
                    },
                },

                columns: [
                    {data: 'account.account_name', name: 'Account Name'},
                    {data: 'expense_ref', name: 'Expense Ref'},
                    {data: 'date', name: 'Date'},
                    {data: 'amount', name: 'Amount'},
                    {data: 'expense_category.title', name: 'Category'},
                    {data: 'payment_method.title', name: 'Payment method'},
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
            var lastDate = new Date();
            $("#end_date").datepicker("setDate" , lastDate);
            lastDate.setDate(lastDate.getDate() - 365);
            $("#start_date").datepicker("setDate" , lastDate);
            var expense_ref = $('#expense_ref').val('');
            let account_id = $('#account_id').val('0');
            let expense_category_id = $('#expense_category_id').val('0');
            let payment_method_id = $('#payment_method_id').val('0');

            expense_datatable(start_date ='',end_date ='', expense_ref, account_id, expense_category_id, payment_method_id);
        });

         // Show Modal Filter

        $('#Show_Modal_Filter').on('click' , function (e) {
            $('#Filter_Expense_Modal').modal('show');
        });


         // Submit Filter

        $('#Filter_Expense_report').on('submit' , function (e) {
            e.preventDefault();
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            var expense_ref = $('#expense_ref').val();
            let account_id = $('#account_id').val();
            let expense_category_id = $('#expense_category_id').val();
            let payment_method_id = $('#payment_method_id').val();
      
            $('#ul-contact-list').DataTable().destroy();
            expense_datatable(start_date,end_date, expense_ref, account_id, expense_category_id, payment_method_id);

            $('#Filter_Expense_Modal').modal('hide');
           
               
        });
       
    });
</script>


@endsection