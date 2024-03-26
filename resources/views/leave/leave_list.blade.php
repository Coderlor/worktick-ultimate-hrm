@extends('layouts.master')
@section('main-content')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">


@endsection

<div class="breadcrumb">
    <h1>{{ __('translate.Leave') }}</h1>
    <ul>
        <li><a href="/leave">{{ __('translate.Request_Leave') }}</a></li>
        <li>{{ __('translate.Leave') }}</li>
    </ul>
</div>

<div class="separator-breadcrumb border-top"></div>

<div class="row" id="section_Leave_list">
    <div class="col-md-12">
        <div class="card text-left">
            <div class="card-header text-right bg-transparent">
                @can('leave_add')
                <a class="btn btn-primary btn-md m-1" @click="New_Leave"><i class="i-Add text-white mr-2"></i>
                    {{ __('translate.Create') }}
                </a>
                @endcan
                @can('leave_delete')
                <a v-if="selectedIds.length > 0" class="btn btn-danger btn-md m-1" @click="delete_selected()"><i
                        class="i-Close-Window text-white mr-2"></i> {{ __('translate.Delete') }}</a>
                @endcan
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="leave_list_table" class="display table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('translate.Employee') }}</th>
                                <th>{{ __('translate.Company') }}</th>
                                <th>{{ __('translate.Department') }}</th>
                                <th>{{ __('translate.Leave_Type') }}</th>
                                <th>{{ __('translate.Start_Date') }}</th>
                                <th>{{ __('translate.Finish_Date') }}</th>
                                <th>{{ __('translate.Days') }}</th>
                                <th>{{ __('translate.Status') }}</th>
                                <th>{{ __('translate.Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leaves as $leave)
                            <tr>
                                <td @click="selected_row( {{ $leave->id}})"></td>
                                <td>{{$leave->employee_name}}</td>
                                <td>{{$leave->company_name}}</td>
                                <td>{{$leave->department_name}}</td>
                                <td>{{$leave->leave_type_title}}</td>
                                <td>{{$leave->start_date}}</td>
                                <td>{{$leave->end_date}}</td>
                                <td>{{$leave->days}}</td>
                                <td>{{$leave->status}}</td>
                                <td>
                                    @can('leave_edit')
                                    <a @click="Edit_Leave( {{ $leave->id}})" class="ul-link-action text-success"
                                        data-toggle="tooltip" data-placement="top" title="Edit">
                                        <i class="i-Edit"></i>
                                    </a>
                                    @endcan
                                    @can('leave_delete')
                                    <a @click="Remove_Leave( {{ $leave->id}})" class="ul-link-action text-danger mr-1"
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

        <!-- Modal Add & Edit Leave -->
        <div class="modal fade" id="Leave_Modal" tabindex="-1" role="dialog" aria-labelledby="Leave_Modal"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 v-if="editmode" class="modal-title">{{ __('translate.Edit') }}</h5>
                        <h5 v-else class="modal-title">{{ __('translate.Create') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <form @submit.prevent="editmode?Update_Leave():Create_Leave()" enctype="multipart/form-data">

                            <div class="row">

                                <div class="form-group col-md-6">
                                    <label class="ul-form__label">{{ __('translate.Company') }} <span
                                            class="field_required">*</span></label>
                                    <v-select @input="Selected_Company"
                                        placeholder="{{ __('translate.Choose_Company') }}" v-model="leave.company_id"
                                        :reduce="label => label.value"
                                        :options="companies.map(companies => ({label: companies.name, value: companies.id}))">
                                    </v-select>

                                    <span class="error" v-if="errors && errors.company_id">
                                        @{{ errors.company_id[0] }}
                                    </span>
                                </div>


                                <div class="form-group col-md-6">
                                    <label class="ul-form__label">{{ __('translate.Department') }} <span
                                            class="field_required">*</span></label>
                                    <v-select @input="Selected_Department"
                                        placeholder="{{ __('translate.Choose_Department') }}"
                                        v-model="leave.department_id" :reduce="label => label.value"
                                        :options="departments.map(departments => ({label: departments.department, value: departments.id}))">
                                    </v-select>

                                    <span class="error" v-if="errors && errors.department_id">
                                        @{{ errors.department_id[0] }}
                                    </span>
                                </div>

                                <div class="form-group col-md-6">
                                    <label class="ul-form__label">{{ __('translate.Employee') }} <span
                                            class="field_required">*</span></label>
                                    <v-select @input="Selected_Employee"
                                        placeholder="{{ __('translate.Choose_Employee') }}" v-model="leave.employee_id"
                                        :reduce="label => label.value"
                                        :options="employees.map(employees => ({label: employees.username, value: employees.id}))">

                                    </v-select>
                                    <span class="error" v-if="errors && errors.employee_id">
                                        @{{ errors.employee_id[0] }}
                                    </span>
                                </div>


                                <div class="form-group col-md-6">
                                    <label class="ul-form__label">{{ __('translate.leave_type') }} <span
                                            class="field_required">*</span></label>
                                    <v-select @input="Selected_Leave_Type"
                                        placeholder="{{ __('translate.Choose_type') }}" v-model="leave.leave_type_id"
                                        :reduce="label => label.value"
                                        :options="leave_types.map(leave_types => ({label: leave_types.title, value: leave_types.id}))">

                                    </v-select>
                                    <span class="error" v-if="errors && errors.leave_type_id">
                                        @{{ errors.leave_type_id[0] }}
                                    </span>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="start_date" class="ul-form__label">{{ __('translate.Start_Date') }}
                                        <span class="field_required">*</span></label>

                                    <vuejs-datepicker id="start_date" name="start_date"
                                        placeholder="{{ __('translate.Enter_Start_date') }}" v-model="leave.start_date"
                                        input-class="form-control" format="yyyy-MM-dd"
                                        @closed="leave.start_date=formatDate(leave.start_date)">
                                    </vuejs-datepicker>

                                    <span class="error" v-if="errors && errors.start_date">
                                        @{{ errors.start_date[0] }}
                                    </span>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="end_date" class="ul-form__label">{{ __('translate.Finish_Date') }} <span
                                            class="field_required">*</span></label>

                                    <vuejs-datepicker id="end_date" name="end_date"
                                        placeholder="{{ __('translate.Enter_Finish_date') }}" v-model="leave.end_date"
                                        input-class="form-control" format="yyyy-MM-dd"
                                        @closed="leave.end_date=formatDate(leave.end_date)">
                                    </vuejs-datepicker>

                                    <span class="error" v-if="errors && errors.end_date">
                                        @{{ errors.end_date[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label class="ul-form__label">{{ __('translate.Status') }} <span
                                            class="field_required">*</span></label>
                                    <v-select @input="Selected_Status" placeholder="{{ __('translate.Choose_status') }}"
                                        v-model="leave.status" :reduce="(option) => option.value" :options="
                                                            [
                                                                {label: 'Approved', value: 'approved'},
                                                                {label: 'Pending', value: 'pending'},
                                                                {label: 'Rejected', value: 'rejected'},
                                                            ]">
                                    </v-select>

                                    <span class="error" v-if="errors && errors.status">
                                        @{{ errors.status[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label for="attachment"
                                        class="ul-form__label">{{ __('translate.Attachment') }}</label>
                                    <input name="attachment" @change="changeAttachement" type="file"
                                        class="form-control" id="attachment">
                                    <span class="error" v-if="errors && errors.attachment">
                                        @{{ errors.attachment[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label class="switch switch-primary mt-3">
                                        <span>{{ __('translate.half_day') }}</span>
                                        <input v-model="leave.half_day" type="checkbox" checked="">
                                        <span class="slider"></span>
                                    </label>
                                </div>

                                <div class="col-md-12">
                                    <label for="reason" class="ul-form__label">{{ __('translate.Leave_Reason') }}
                                    </label>
                                    <textarea type="text" v-model="leave.reason" class="form-control" name="reason"
                                        id="reason" placeholder="{{ __('translate.Enter_Reason_Leave') }}"></textarea>
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
        el: '#section_Leave_list',
        components: {
            vuejsDatepicker
        },
        data: {
            selectedIds:[],
            data: new FormData(),
            editmode: false,
            SubmitProcessing:false,
            employees:[],
            companies:[],
            departments:[],
            leave_types:[],
            errors:[],
            leaves: [], 
            leave: {
                company_id: "",
                department_id: "",
                employee_id: "",
                leave_type_id :"",
                start_date:"",
                end_date:"",
                days:"",
                reason:"",
                attachment:"",
                half_day:"",
                status:"",
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

            //------------------------------ Show Modal (Create Leave) -------------------------------\\
            New_Leave() {
                this.reset_Form();
                this.editmode = false;
                this.Get_Data_Create();
                $('#Leave_Modal').modal('show');
            },

            //------------------------------ Show Modal (Update Leave) -------------------------------\\
            Edit_Leave(leave_id) {
                this.editmode = true;
                this.reset_Form();
                this.Get_Data_Edit(leave_id);
            },


            Selected_Employee(value) {
                if (value === null) {
                    this.leave.employee_id = "";
                }
            },

            Selected_Leave_Type(value) {
                if (value === null) {
                    this.leave.leave_type_id = "";
                }
            },

            Selected_Status(value) {
                if (value === null) {
                    this.leave.status = "";
                }
            },

            Selected_Company(value) {
                if (value === null) {
                    this.leave.company_id = "";
                }
                this.employees = [];
                this.departments = [];
                this.leave.employee_id = "";
                this.leave.department_id = "";
                this.Get_departments_by_company(value);
            },

            Selected_Department(value) {
                if (value === null) {
                    this.leave.department_id = "";
                    this.leave.employee_id = "";
                }
                this.employee_id = [];
                this.leave.employee_id = "";
                this.Get_employees_by_department(value);
            },

            Selected_Employee(value) {
                if (value === null) {
                    this.leave.employee_id = "";
                }
            },


              //---------------------- Get_departments_by_company ------------------------------\\
            Get_departments_by_company(value) {
            axios
                .get("/core/Get_departments_by_company?id=" + value)
                .then(({ data }) => (this.departments = data));
            },

            //---------------------- Get_employees_by_department ------------------------------\\
            
            Get_employees_by_department(value) {
                axios
                .get("/Get_employees_by_department?id=" + value)
                .then(({ data }) => (this.employees = data));
            },


            
             //---------------------- Get_Data_Create  ------------------------------\\
             Get_Data_Create() {
                axios
                    .get("/leave/create")
                    .then(response => {
                        this.companies   = response.data.companies;
                        this.leave_types = response.data.leave_types;
                    })
                    .catch(error => {
                       
                    });
            },

              //---------------------- Get_Data_Edit  ------------------------------\\
              Get_Data_Edit(id) {
                axios
                    .get("/leave/"+id+"/edit")
                    .then(response => {
                        this.leave    = response.data.leave;
                        this.companies   = response.data.companies;
                        this.leave_types = response.data.leave_types;
                        this.Get_departments_by_company(this.leave.company_id);
                        this.Get_employees_by_department(this.leave.department_id);
                        this.leave.attachment = "";
                        $('#Leave_Modal').modal('show');
                    })
                    .catch(error => {
                       
                    });
            },


            //----------------------------- Reset Form ---------------------------\\
            reset_Form() {
                this.leave = {
                    id: "",
                    company_id: "",
                    department_id: "",
                    employee_id: "",
                    leave_type_id :"",
                    start_date:"",
                    end_date:"",
                    days:"",
                    reason:"",
                    attachment:"",
                    half_day:"",
                    status:"",
                };
                this.errors = {};
            },

            changeAttachement(e){
                let file = e.target.files[0];
                this.leave.attachment = file;
            },

          
             //------------------------ Create Leave ---------------------------\\
             Create_Leave() {
                var self = this;
                self.SubmitProcessing = true;
                self.data.append("company_id", self.leave.company_id);
                self.data.append("department_id", self.leave.department_id);
                self.data.append("employee_id", self.leave.employee_id);
                self.data.append("leave_type_id", self.leave.leave_type_id);
                self.data.append("start_date", self.leave.start_date);
                self.data.append("end_date", self.leave.end_date);
                self.data.append("reason", self.leave.reason);
                self.data.append("attachment", self.leave.attachment);
                self.data.append("half_day", self.leave.half_day?1:0);
                self.data.append("status", self.leave.status);

                axios
                    .post("/leave", self.data)
                    .then(response => {
                        if(response.data.isvalid == false){
                            self.SubmitProcessing = false;
                            self.errors = {};
                            toastr.error('{{ __('translate.remaining_leaves_are_insufficient') }}');
                        }
                        else{
                            self.SubmitProcessing = false;
                            window.location.href = '/leave'; 
                            toastr.success('{{ __('translate.Created_in_successfully') }}');
                            self.errors = {};
                        }
                })
                .catch(error => {
                    self.SubmitProcessing = false;
                    if (error.response.status == 422) {
                        self.errors = error.response.data.errors;
                    }
                    toastr.error('{{ __('translate.There_was_something_wronge') }}');
                });
            },

           //----------------------- Update Leave ---------------------------\\
            Update_Leave() {
                var self = this;
                self.SubmitProcessing = true;
                self.data.append("company_id", self.leave.company_id);
                self.data.append("department_id", self.leave.department_id);
                self.data.append("employee_id", self.leave.employee_id);
                self.data.append("leave_type_id", self.leave.leave_type_id);
                self.data.append("start_date", self.leave.start_date);
                self.data.append("end_date", self.leave.end_date);
                self.data.append("reason", self.leave.reason);
                self.data.append("attachment", self.leave.attachment);
                self.data.append("half_day", self.leave.half_day?1:0);
                self.data.append("status", self.leave.status);
                self.data.append("_method", "put");

                axios
                    .post("/leave/" + this.leave.id, self.data)
                    .then(response => {
                        if(response.data.isvalid == false){
                            self.SubmitProcessing = false;
                            self.errors = {};
                            toastr.error('{{ __('translate.remaining_leaves_are_insufficient') }}');
                        }
                        else{
                            self.SubmitProcessing = false;
                            window.location.href = '/leave'; 
                            toastr.success('{{ __('translate.Updated_in_successfully') }}');
                            self.errors = {};
                        }
                    })
                    .catch(error => {
                        self.SubmitProcessing = false;
                        if (error.response.status == 422) {
                            self.errors = error.response.data.errors;
                        }
                        toastr.error('{{ __('translate.There_was_something_wronge') }}');
                    });
            },

             //--------------------------------- Remove Leave ---------------------------\\
            Remove_Leave(id) {

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
                            .delete("/leave/" + id)
                            .then(() => {
                                window.location.href = '/leave'; 
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
                        .post("/leave/delete/by_selection", {
                            selectedIds: self.selectedIds
                        })
                            .then(() => {
                                window.location.href = '/leave'; 
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

        $('#leave_list_table').DataTable( {
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