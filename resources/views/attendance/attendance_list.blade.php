@extends('layouts.master')
@section('main-content')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/styles/vendor/vue2-clock-picker/vue2-clock-picker.min.css')}}">


@endsection

<div class="breadcrumb">
    <h1>{{ __('translate.Attendance_List') }}</h1>
    <ul>
        <li><a href="/attendances">{{ __('translate.Attendances') }}</a></li>
        <li>{{ __('translate.Attendance_List') }}</li>
    </ul>
</div>

<div class="separator-breadcrumb border-top"></div>

<div class="row" id="section_attendance_list">
    <div class="col-md-12">
        <div class="card text-left">
            <div class="card-header text-right bg-transparent">
                @can('attendance_add')
                <a class="btn btn-primary btn-md m-1" @click="New_Attendance"><i
                        class="i-Add text-white mr-2"></i>{{ __('translate.Create') }}</a>
                @endcan
                @can('attendance_delete')
                <a v-if="selectedIds.length > 0" class="btn btn-danger btn-md m-1" @click="delete_selected()"><i
                        class="i-Close-Window text-white mr-2"></i> {{ __('translate.Delete') }}</a>
                @endcan
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="attendance_list_table" class="display table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('translate.Employee') }}</th>
                                <th>{{ __('translate.Company') }}</th>
                                <th>{{ __('translate.Date') }}</th>
                                <th>{{ __('translate.Time_In') }}</th>
                                <th>{{ __('translate.Time_Out') }}</th>
                                <th>{{ __('translate.Work_Duration') }}</th>
                                <th>{{ __('translate.Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendances as $attendance)
                            <tr>
                                <td @click="selected_row( {{ $attendance->id}})"></td>
                                <td>{{$attendance->employee->username}}</td>
                                <td>{{$attendance->company->name}}</td>
                                <td>{{$attendance->date}}</td>
                                <td>{{$attendance->clock_in}}</td>
                                <td>{{$attendance->clock_out}}</td>
                                <td>{{$attendance->total_work}}</td>

                                <td>
                                    @can('attendance_edit')
                                    <a @click="Edit_Attendance( {{ $attendance}})" class="ul-link-action text-success"
                                        data-toggle="tooltip" data-placement="top" title="Edit">
                                        <i class="i-Edit"></i>
                                    </a>
                                    @endcan
                                    @can('attendance_delete')
                                    <a @click="Remove_Attendance( {{ $attendance->id}})"
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

        <!-- Modal Add & Edit Attendance -->
        <div class="modal fade" id="Attendance_Modal" tabindex="-1" role="dialog" aria-labelledby="Attendance_Modal"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 v-if="editmode" class="modal-title">
                            <th>{{ __('translate.Edit') }}</th>
                        </h5>
                        <h5 v-else class="modal-title">
                            <th>{{ __('translate.Create') }}</th>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <form @submit.prevent="editmode?Update_Attendance():Create_Attendance()">
                            <div class="row">

                                <div class="col-md-6">
                                    <label class="ul-form__label">{{ __('translate.Company') }} <span
                                            class="field_required">*</span></label>
                                    <v-select @input="Selected_Company"
                                        placeholder="{{ __('translate.Choose_Company') }}"
                                        v-model="attendance.company_id" :reduce="label => label.value"
                                        :options="companies.map(companies => ({label: companies.name, value: companies.id}))">
                                    </v-select>

                                    <span class="error" v-if="errors && errors.company_id">
                                        @{{ errors.company_id[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label class="ul-form__label">{{ __('translate.Employee') }} <span
                                            class="field_required">*</span></label>
                                    <v-select @input="Selected_Employee"
                                        placeholder="{{ __('translate.Choose_Employee') }}"
                                        v-model="attendance.employee_id" :reduce="label => label.value"
                                        :options="employees.map(employees => ({label: employees.username, value: employees.id}))">

                                    </v-select>

                                    <span class="error" v-if="errors && errors.employee_id">
                                        @{{ errors.employee_id[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label for="date" class="ul-form__label">{{ __('translate.Date') }} <span
                                            class="field_required">*</span></label>

                                    <vuejs-datepicker id="date" name="date"
                                        placeholder="{{ __('translate.Enter_Attendance_date') }}"
                                        v-model="attendance.date" input-class="form-control" format="yyyy-MM-dd"
                                        @closed="attendance.date=formatDate(attendance.date)">
                                    </vuejs-datepicker>

                                    <span class="error" v-if="errors && errors.date">
                                        @{{ errors.date[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label for="clock_in" class="ul-form__label">{{ __('translate.Time_In') }} <span
                                            class="field_required">*</span></label>

                                    <vue-clock-picker v-model="attendance.clock_in"
                                        placeholder="{{ __('translate.Time_In') }}" name="clock_in" id="clock_in">
                                    </vue-clock-picker>
                                    <span class="error" v-if="errors && errors.clock_in">
                                        @{{ errors.clock_in[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label for="clock_out" class="ul-form__label">{{ __('translate.Time_Out') }} <span
                                            class="field_required">*</span></label>

                                    <vue-clock-picker v-model="attendance.clock_out"
                                        placeholder="{{ __('translate.Time_Out') }}" name="clock_out" id="clock_out">
                                    </vue-clock-picker>
                                    <span class="error" v-if="errors && errors.clock_out">
                                        @{{ errors.clock_out[0] }}
                                    </span>
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
<script src="{{asset('assets/js/vendor/vue2-clock-picker/vue2-clock-picker.plugin.js')}}"></script>
<script src="{{asset('assets/js/vendor/vuejs-datepicker/vuejs-datepicker.min.js')}}"></script>



<script>
    Vue.use(VueClockPickerPlugin)
        Vue.component('v-select', VueSelect.VueSelect)

        var app = new Vue({
        el: '#section_attendance_list',
        components: {
            vuejsDatepicker
        },
        data: {
            SubmitProcessing:false,
            editmode: false,
            selectedIds:[],
            companies: [],
            employees:[],
            errors:[],
            attendances: [], 
            attendance: {
                company_id: "",
                employee_id: "",
                date:"",
                clock_in:"",
                clock_out:"",
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

             //------------------------------ Show Modal (Create attendance) -------------------------------\\
             New_Attendance() {
                this.reset_Form();
                this.editmode = false;
                this.Get_all_companies();
                $('#Attendance_Modal').modal('show');
            },

              //------------------------------ Show Modal (Update attendance) -------------------------------\\
              Edit_Attendance(attendance) {
                this.editmode = true;
                this.reset_Form();
                this.Get_all_companies();
                this.Get_employees_by_company(attendance.company_id);
                this.attendance = attendance;
                $('#Attendance_Modal').modal('show');
            },

            Selected_Company(value) {
                if (value === null) {
                    this.attendance.company_id = "";
                }
                this.employees = [];
                this.attendance.employee_id = "";
                this.Get_employees_by_company(value);
            },

            Selected_Employee(value) {
                if (value === null) {
                    this.attendance.employee_id = "";
                }
            },

            //---------------------- Get_employees_by_company ------------------------------\\
            
            Get_employees_by_company(value) {
                axios
                .get("/Get_employees_by_company?id=" + value)
                .then(({ data }) => (this.employees = data));
            },

            //----------------------------- Reset Form ---------------------------\\
            reset_Form() {
                this.attendance = {
                    company_id: "",
                    employee_id: "",
                    date:"",
                    clock_in:"",
                    clock_out:"",
                };
                this.errors = {};
            },

             //---------------------- Get all companies  ------------------------------\\
             Get_all_companies() {
                axios
                    .get("/attendances/create")
                    .then(response => {
                        this.companies   = response.data.companies;
                    })
                    .catch(error => {
                       
                    });
            },

              //------------------------ Create Attendance ---------------------------\\
              Create_Attendance() {
                var self = this;
                self.SubmitProcessing = true;
                axios.post("/attendances", {
                    company_id: self.attendance.company_id,
                    employee_id: self.attendance.employee_id,
                    date: self.attendance.date,
                    clock_in: self.attendance.clock_in,
                    clock_out: self.attendance.clock_out,
                }).then(response => {
                        self.SubmitProcessing = false;
                        window.location.href = '/attendances'; 
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

           //----------------------- Update Attendance ---------------------------\\
            Update_Attendance() {
                var self = this;
                self.SubmitProcessing = true;
                axios.put("/attendances/" + self.attendance.id, {
                    company_id: self.attendance.company_id,
                    employee_id: self.attendance.employee_id,
                    date: self.attendance.date,
                    clock_in: self.attendance.clock_in,
                    clock_out: self.attendance.clock_out,
                }).then(response => {
                        self.SubmitProcessing = false;
                        window.location.href = '/attendances'; 
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


            //--------------------------------- Remove Attendance ---------------------------\\
            Remove_Attendance(id) {

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
                            .delete("/attendances/" + id)
                            .then(() => {
                                window.location.href = '/attendances'; 
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
                        .post("/attendances/delete/by_selection", {
                            selectedIds: self.selectedIds
                        })
                            .then(() => {
                                window.location.href = '/attendances'; 
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

        $('#attendance_list_table').DataTable( {
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