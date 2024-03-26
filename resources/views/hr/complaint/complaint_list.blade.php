@extends('layouts.master')
@section('main-content')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/styles/vendor/vue2-clock-picker/vue2-clock-picker.min.css')}}">


@endsection

<div class="breadcrumb">
    <h1>{{ __('translate.Complaint_List') }}</h1>
    <ul>
        <li><a href="/hr/complaint">{{ __('translate.Complaints') }}</a></li>
        <li>{{ __('translate.Complaint_List') }}</li>
    </ul>
</div>

<div class="separator-breadcrumb border-top"></div>

<div class="row" id="section_Complaint_list">
    <div class="col-md-12">
        <div class="card text-left">
            <div class="card-header text-right bg-transparent">
                @can('complaint_add')
                <a class="btn btn-primary btn-md m-1" @click="New_Complaint"><i class="i-Add text-white mr-2"></i>
                    {{ __('translate.Create') }}</a>
                @endcan
                @can('complaint_delete')
                <a v-if="selectedIds.length > 0" class="btn btn-danger btn-md m-1" @click="delete_selected()"><i
                        class="i-Close-Window text-white mr-2"></i> {{ __('translate.Delete') }}</a>
                @endcan
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="complaint_list_table" class="display table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('translate.Complaint') }}</th>
                                <th>{{ __('translate.Company') }}</th>
                                <th>{{ __('translate.From_Employee') }}</th>
                                <th>{{ __('translate.Employee_against') }}</th>
                                <th>{{ __('translate.Date') }}</th>
                                <th>{{ __('translate.Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($complaints as $complaint)
                            <tr>
                                <td @click="selected_row( {{ $complaint->id}})"></td>
                                <td>{{$complaint->title}}</td>
                                <td>{{$complaint->company->name}}</td>
                                <td>{{$complaint->EmployeeFrom->username}}</td>
                                <td>{{$complaint->EmployeeAgainst->username}}</td>
                                <td>{{$complaint->date}}</td>
                                <td>
                                    @can('complaint_edit')
                                    <a @click="Edit_Complaint( {{ $complaint}})" class="ul-link-action text-success"
                                        data-toggle="tooltip" data-placement="top" title="Edit">
                                        <i class="i-Edit"></i>
                                    </a>
                                    @endcan
                                    @can('complaint_delete')
                                    <a @click="Remove_Complaint( {{ $complaint->id}})"
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

        <!-- Modal Add & Edit Complaint -->
        <div class="modal fade" id="Complaint_Modal" tabindex="-1" role="dialog" aria-labelledby="Complaint_Modal"
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

                        <form @submit.prevent="editmode?Update_Complaint():Create_Complaint()">
                            <div class="row">

                                <div class="col-md-6">
                                    <label for="title"
                                        class="ul-form__label">{{ __('translate.Title_of_Complaint') }}<span
                                            class="field_required">*</span></label>
                                    <input type="text" v-model="complaint.title" class="form-control" name="title"
                                        id="title" placeholder="{{ __('translate.Enter_Complaint_title') }}">
                                    <span class="error" v-if="errors && errors.title">
                                        @{{ errors.title[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label class="ul-form__label">{{ __('translate.Company') }} <span
                                            class="field_required">*</span></label>
                                    <v-select @input="Selected_Company"
                                        placeholder="{{ __('translate.Choose_Company') }}"
                                        v-model="complaint.company_id" :reduce="label => label.value"
                                        :options="companies.map(companies => ({label: companies.name, value: companies.id}))">
                                    </v-select>

                                    <span class="error" v-if="errors && errors.company_id">
                                        @{{ errors.company_id[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label class="ul-form__label">{{ __('translate.Complaint_From') }} <span
                                            class="field_required">*</span></label>
                                    <v-select @input="Selected_employee_from"
                                        placeholder="{{ __('translate.Choose_Employee') }}"
                                        v-model="complaint.employee_from" :reduce="label => label.value"
                                        :options="employees.map(employees => ({label: employees.username, value: employees.id}))">
                                    </v-select>
                                    <span class="error" v-if="errors && errors.employee_from">
                                        @{{ errors.employee_from[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label class="ul-form__label">{{ __('translate.Complaint_against') }} <span
                                            class="field_required">*</span></label>
                                    <v-select @input="Selected_employee_against"
                                        placeholder="{{ __('translate.Choose_Employee') }}"
                                        v-model="complaint.employee_against" :reduce="label => label.value"
                                        :options="employees.map(employees => ({label: employees.username, value: employees.id}))">
                                    </v-select>
                                    <span class="error" v-if="errors && errors.employee_against">
                                        @{{ errors.employee_against[0] }}
                                    </span>
                                </div>


                                <div class="col-md-6">
                                    <label for="date" class="ul-form__label">{{ __('translate.Date_of_Complaint') }}
                                        <span class="field_required">*</span></label>

                                    <vuejs-datepicker id="date" name="date"
                                        placeholder="{{ __('translate.Enter_Date_of_Complaint') }}"
                                        v-model="complaint.date" input-class="form-control" format="yyyy-MM-dd"
                                        @closed="complaint.date=formatDate(complaint.date)">
                                    </vuejs-datepicker>

                                    <span class="error" v-if="errors && errors.date">
                                        @{{ errors.date[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label for="time" class="ul-form__label">{{ __('translate.Time_of_Complaint') }}
                                    </label>

                                    <vue-clock-picker v-model="complaint.time"
                                        placeholder="{{ __('translate.Enter_Time_of_Complaint') }}" name="time"
                                        id="time"></vue-clock-picker>
                                    <span class="error" v-if="errors && errors.time">
                                        @{{ errors.time[0] }}
                                    </span>
                                </div>


                                <div class="col-md-12">
                                    <label for="reason"
                                        class="ul-form__label">{{ __('translate.Reason_for_Complaint') }} <span
                                            class="field_required">*</span></label>
                                    <textarea type="text" v-model="complaint.reason" class="form-control" name="reason"
                                        id="reason"
                                        placeholder="{{ __('translate.Enter_Reason_for_Complaint') }}"></textarea>
                                    <span class="error" v-if="errors && errors.reason">
                                        @{{ errors.reason[0] }}
                                    </span>
                                </div>

                                <div class="col-md-12">
                                    <label for="Description"
                                        class="ul-form__label">{{ __('translate.Please_provide_any_details') }} </label>
                                    <textarea type="text" v-model="complaint.description" class="form-control"
                                        name="Description" id="Description"
                                        placeholder="{{ __('translate.Enter_description') }}"></textarea>
                                    <span class="error" v-if="errors && errors.description">
                                        @{{ errors.description[0] }}
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
        el: '#section_Complaint_list',
        components: {
            vuejsDatepicker
        },
        data: {
            editmode: false,
            SubmitProcessing:false,
            errors:[],
            selectedIds:[],
            companies:[],
            employees:[],
            complaints: {}, 
            complaint: {
                company_id:"",
                employee_from:"",
                employee_against:"",
                title: "",
                date:"",
                time:"",
                reason:"",
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

            //------------------------------ Show Modal (Create Complaint) -------------------------------\\
            New_Complaint() {
                this.reset_Form();
                this.Get_Data_Create();
                this.editmode = false;
                $('#Complaint_Modal').modal('show');
            },

            //------------------------------ Show Modal (Update Complaint) -------------------------------\\
            Edit_Complaint(complaint) {
                this.editmode = true;
                this.reset_Form();
                this.Get_Data_Edit(complaint.id);
                this.Get_employees_by_company(complaint.company_id);
                this.complaint = complaint;
                this.complaint.employee_from = complaint.employee_from.id;
                this.complaint.employee_against = complaint.employee_against.id;
                $('#Complaint_Modal').modal('show');
            },

            Selected_Company(value) {
                if (value === null) {
                    this.complaint.company_id = "";
                }
                this.employees = [];
                this.complaint.employee_from = "";
                this.complaint.employee_against = "";
                this.Get_employees_by_company(value);
            },

            Selected_employee_from(value) {
                if (value === null) {
                    this.complaint.employee_from = "";
                }
            },

            Selected_employee_against(value) {
                if (value === null) {
                    this.complaint.employee_against = "";
                }
            },


             //---------------------- Get_employees_by_company ------------------------------\\
            
             Get_employees_by_company(value) {
                axios
                .get("/Get_employees_by_company?id=" + value)
                .then(({ data }) => (this.employees = data));
            },


            
             //---------------------- Get_Data_Create  ------------------------------\\
             Get_Data_Create() {
                axios
                    .get("/hr/complaint/create")
                    .then(response => {
                        this.companies   = response.data.companies;
                    })
                    .catch(error => {
                       
                    });
            },

             //---------------------- Get_Data_Edit  ------------------------------\\
             Get_Data_Edit(id) {
                axios
                    .get("/hr/complaint/"+id+"/edit")
                    .then(response => {
                        this.companies   = response.data.companies;
                    })
                    .catch(error => {
                       
                    });
            },



            //----------------------------- Reset Form ---------------------------\\
            reset_Form() {
                this.complaint = {
                    company_id:"",
                    employee_from:"",
                    employee_against:"",
                    id: "",
                    title: "",
                    date:"",
                    time:"",
                    reason:"",
                    description:"",
                };
                this.errors = {};
            },

            //------------------------ Create Complaint ---------------------------\\
            Create_Complaint() {
                var self = this;
                self.SubmitProcessing = true;
                axios.post("/hr/complaint", {
                    company_id: self.complaint.company_id,
                    employee_from: self.complaint.employee_from,
                    employee_against: self.complaint.employee_against,
                    title: self.complaint.title,
                    date: self.complaint.date,
                    time: self.complaint.time,
                    reason: self.complaint.reason,
                    description: self.complaint.description,
                }).then(response => {
                        self.SubmitProcessing = false;
                        window.location.href = '/hr/complaint'; 
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

           //----------------------- Update Complaint ---------------------------\\
            Update_Complaint() {
                var self = this;
                self.SubmitProcessing = true;
                axios.put("/hr/complaint/" + self.complaint.id, {
                    company_id: self.complaint.company_id,
                    employee_from: self.complaint.employee_from,
                    employee_against: self.complaint.employee_against,
                    title: self.complaint.title,
                    date: self.complaint.date,
                    time: self.complaint.time,
                    reason: self.complaint.reason,
                    description: self.complaint.description,
                }).then(response => {
                        self.SubmitProcessing = false;
                        window.location.href = '/hr/complaint'; 
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

             //--------------------------------- Remove Complaint ---------------------------\\
            Remove_Complaint(id) {

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
                            .delete("/hr/complaint/" + id)
                            .then(() => {
                                window.location.href = '/hr/complaint'; 
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
                        .post("/hr/complaint/delete/by_selection", {
                            selectedIds: self.selectedIds
                        })
                            .then(() => {
                                window.location.href = '/hr/complaint'; 
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

        $('#complaint_list_table').DataTable( {
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