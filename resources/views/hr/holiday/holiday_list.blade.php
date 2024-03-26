@extends('layouts.master')
@section('main-content')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">


@endsection

<div class="breadcrumb">
    <h1>{{ __('translate.Holiday_List') }}</h1>
    <ul>
        <li><a href="/hr/holiday">{{ __('translate.Holidays') }}</a></li>
        <li>{{ __('translate.Holiday_List') }}</li>
    </ul>
</div>

<div class="separator-breadcrumb border-top"></div>

<div class="row" id="section_Holiday_list">
    <div class="col-md-12">
        <div class="card text-left">
            <div class="card-header text-right bg-transparent">
                @can('holiday_add')
                <a class="btn btn-primary btn-md m-1" @click="New_Holiday"><i class="i-Add text-white mr-2"></i>
                    {{ __('translate.Create') }}</a>
                @endcan
                @can('holiday_delete')
                <a v-if="selectedIds.length > 0" class="btn btn-danger btn-md m-1" @click="delete_selected()"><i
                        class="i-Close-Window text-white mr-2"></i> {{ __('translate.Delete') }}</a>
                @endcan
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="holiday_list_table" class="display table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('translate.Holiday') }}</th>
                                <th>{{ __('translate.Company') }}</th>
                                <th>{{ __('translate.Start_Date') }}</th>
                                <th>{{ __('translate.Finish_Date') }}</th>
                                <th>{{ __('translate.Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($holidays as $holiday)
                            <tr>
                                <td @click="selected_row( {{ $holiday->id}})"></td>
                                <td>{{$holiday->title}}</td>
                                <td>{{$holiday->company->name}}</td>
                                <td>{{$holiday->start_date}}</td>
                                <td>{{$holiday->end_date}}</td>
                                <td>
                                    @can('holiday_edit')
                                    <a @click="Edit_Holiday( {{ $holiday}})" class="ul-link-action text-success"
                                        data-toggle="tooltip" data-placement="top" title="Edit">
                                        <i class="i-Edit"></i>
                                    </a>
                                    @endcan
                                    @can('holiday_delete')
                                    <a @click="Remove_Holiday( {{ $holiday->id}})"
                                        class="ul-link-action text-danger mr-1" data-toggle="tooltip"
                                        data-placement="top" title="Delete">
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

        <!-- Modal Add & Edit Holiday -->
        <div class="modal fade" id="Holiday_Modal" tabindex="-1" role="dialog" aria-labelledby="Holiday_Modal"
            aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 v-if="editmode" class="modal-title">{{ __('translate.Edit') }}</h5>
                        <h5 v-else class="modal-title">{{ __('translate.Create') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <form @submit.prevent="editmode?Update_Holiday():Create_Holiday()">
                            <div class="row">

                                <div class="col-md-12">
                                    <label class="ul-form__label">{{ __('translate.Company') }} <span
                                            class="field_required">*</span></label>
                                    <v-select @input="Selected_Company"
                                        placeholder="{{ __('translate.Choose_Company') }}" v-model="holiday.company_id"
                                        :reduce="label => label.value"
                                        :options="companies.map(companies => ({label: companies.name, value: companies.id}))">
                                    </v-select>

                                    <span class="error" v-if="errors && errors.company_id">
                                        @{{ errors.company_id[0] }}
                                    </span>
                                </div>

                                <div class="col-md-12">
                                    <label for="title" class="ul-form__label">{{ __('translate.Holiday_Title') }} <span
                                            class="field_required">*</span></label>
                                    <input type="text" v-model="holiday.title" class="form-control" name="title"
                                        id="title" placeholder="{{ __('translate.Enter_title') }}">
                                    <span class="error" v-if="errors && errors.title">
                                        @{{ errors.title[0] }}
                                    </span>
                                </div>

                                <div class="col-md-12">
                                    <label for="start_date" class="ul-form__label">{{ __('translate.Start_Date') }}
                                        <span class="field_required">*</span></label>

                                    <vuejs-datepicker id="start_date" name="start_date"
                                        placeholder="{{ __('translate.Enter_Start_date') }}"
                                        v-model="holiday.start_date" input-class="form-control" format="yyyy-MM-dd"
                                        @closed="holiday.start_date=formatDate(holiday.start_date)">
                                    </vuejs-datepicker>

                                    <span class="error" v-if="errors && errors.start_date">
                                        @{{ errors.start_date[0] }}
                                    </span>
                                </div>

                                <div class="col-md-12">
                                    <label for="end_date" class="ul-form__label">{{ __('translate.Finish_Date') }} <span
                                            class="field_required">*</span></label>

                                    <vuejs-datepicker id="end_date" name="end_date"
                                        placeholder="{{ __('translate.Enter_Finish_date') }}" v-model="holiday.end_date"
                                        input-class="form-control" format="yyyy-MM-dd"
                                        @closed="holiday.end_date=formatDate(holiday.end_date)">
                                    </vuejs-datepicker>

                                    <span class="error" v-if="errors && errors.end_date">
                                        @{{ errors.end_date[0] }}
                                    </span>
                                </div>

                                <div class="col-md-12">
                                    <label for="Description"
                                        class="ul-form__label">{{ __('translate.Please_provide_any_details') }}</label>
                                    <textarea type="text" v-model="holiday.description" class="form-control"
                                        name="Description" id="Description"
                                        placeholder="{{ __('translate.Please_provide_any_details') }}"></textarea>
                                </div>

                            </div>


                            <div class="row mt-3">

                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary" :disabled="SubmitProcessing">
                                        {{ __('translate.Submit') }}
                                    </button>
                                    <div v-once class="typo__p" v-if="SubmitProcessing">
                                        <div class="spinner spinner-primary mt-3"></div>
                                    </div>
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
<script src="{{asset('assets/js/vendor/vuejs-datepicker/vuejs-datepicker.min.js')}}"></script>


<script>
    Vue.component('v-select', VueSelect.VueSelect)

        var app = new Vue({
        el: '#section_Holiday_list',
        components: {
            vuejsDatepicker
        },
        data: {
            selectedIds:[],
            editmode: false,
            SubmitProcessing:false,
            errors:[],
            companies:[],
            holidays: {}, 
            holiday: {
                title: "",
                company_id:"",
                start_date:"",
                end_date:"",
                description:"",
            }, 
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

            formatDate(d){
                var m1 = d.getMonth()+1;
                var m2 = m1 < 10 ? '0' + m1 : m1;
                var d1 = d.getDate();
                var d2 = d1 < 10 ? '0' + d1 : d1;
                return [d.getFullYear(), m2, d2].join('-');
            },

            //------------------------------ Show Modal (Create Holiday) -------------------------------\\
            New_Holiday() {
                this.reset_Form();
                this.editmode = false;
                this.Get_Data_Create();
                $('#Holiday_Modal').modal('show');
            },

            //------------------------------ Show Modal (Update Holiday) -------------------------------\\
            Edit_Holiday(holiday) {
                this.editmode = true;
                this.reset_Form();
                this.Get_Data_Edit(holiday.id);
                this.holiday = holiday;
                $('#Holiday_Modal').modal('show');
            },

            //---------------------- Get_Data_Create  ------------------------------\\
            Get_Data_Create() {
                axios
                    .get("/hr/holiday/create")
                    .then(response => {
                        this.companies   = response.data.companies;
                    })
                    .catch(error => {
                       
                    });
            },

             //---------------------- Get_Data_Edit  ------------------------------\\
             Get_Data_Edit(id) {
                axios
                    .get("/hr/holiday/"+id+"/edit")
                    .then(response => {
                        this.companies   = response.data.companies;
                    })
                    .catch(error => {
                       
                    });
            },

            Selected_Company(value) {
                if (value === null) {
                    this.holiday.company_id = "";
                }
            },


            //----------------------------- Reset Form ---------------------------\\
            reset_Form() {
                this.holiday = {
                    id: "",
                    title: "",
                    company_id:"",
                    start_date:"",
                    end_date:"",
                    description:"",
                };
                this.errors = {};
            },

            //------------------------ Create Holiday ---------------------------\\
            Create_Holiday() {
                var self = this;
                self.SubmitProcessing = true;
                axios.post("/hr/holiday", {
                    company_id: self.holiday.company_id,
                    title: self.holiday.title,
                    start_date: self.holiday.start_date,
                    end_date: self.holiday.end_date,
                    description: self.holiday.description,
                }).then(response => {
                        self.SubmitProcessing = false;
                        window.location.href = '/hr/holiday'; 
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

           //----------------------- Update Holiday ---------------------------\\
            Update_Holiday() {
                var self = this;
                self.SubmitProcessing = true;
                axios.put("/hr/holiday/" + self.holiday.id, {
                    title: self.holiday.title,
                    company_id: self.holiday.company_id,
                    start_date: self.holiday.start_date,
                    end_date: self.holiday.end_date,
                    description: self.holiday.description,
                }).then(response => {
                        self.SubmitProcessing = false;
                        window.location.href = '/hr/holiday'; 
                        toastr.success('{{ __('translate.Updated_in_successfully') }}');
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

             //--------------------------------- Remove holiday ---------------------------\\
            Remove_Holiday(id) {

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
                            .delete("/hr/holiday/" + id)
                            .then(() => {
                                window.location.href = '/hr/holiday'; 
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
                        .post("/hr/holiday/delete/by_selection", {
                            selectedIds: self.selectedIds
                        })
                            .then(() => {
                                window.location.href = '/hr/holiday'; 
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

        $('#holiday_list_table').DataTable( {
            "processing": true, // for show progress bar
            select: {
                style: 'multi',
                selector: '.select-checkbox',
                items: 'row',
            },
            columnDefs: [
                {
                    targets: 0,
                    className: 'select-checkbox'
                },
                {
                    targets: [0],
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