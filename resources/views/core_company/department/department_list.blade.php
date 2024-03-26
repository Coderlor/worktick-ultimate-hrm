@extends('layouts.master')
@section('main-content')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">


@endsection

<div class="breadcrumb">
    <h1>{{ __('translate.Department_List') }}</h1>
    <ul>
        <li><a href="/core/departments">{{ __('translate.Departments') }}</a></li>
        <li>{{ __('translate.Department_List') }}</li>
    </ul>
</div>

<div class="separator-breadcrumb border-top"></div>

<div class="row" id="section_Department_list">
    <div class="col-md-12">
        <div class="card text-left">
            <div class="card-header text-right bg-transparent">
                @can('department_add')
                <a class="btn btn-primary btn-md m-1" @click="New_Department"><i class="i-Add text-white mr-2"></i>
                    {{ __('translate.Create') }}</a>
                @endcan
                @can('department_delete')
                <a v-if="selectedIds.length > 0" class="btn btn-danger btn-md m-1" @click="delete_selected()"><i
                        class="i-Close-Window text-white mr-2"></i> {{ __('translate.Delete') }}</a>
                @endcan
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="department_list_table" class="display table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('translate.Department_Name') }}</th>
                                <th>{{ __('translate.Department_Head') }}</th>
                                <th>{{ __('translate.Company') }}</th>
                                <th>{{ __('translate.Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($departments as $department)
                            <tr>
                                <td @click="selected_row( {{ $department->id}})"></td>
                                <td>{{$department->department}}</td>
                                <td>{{$department->employee_head?$department->employee_head:'__'}}</td>
                                <td>{{$department->company_name}}</td>
                                <td>
                                    @can('department_edit')
                                    <a @click="Edit_Department( {{ $department}})" class="ul-link-action text-success"
                                        data-toggle="tooltip" data-placement="top" title="Edit">
                                        <i class="i-Edit"></i>
                                    </a>
                                    @endcan
                                    @can('department_delete')
                                    <a @click="Remove_Department( {{ $department->id}})"
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

        <!-- Modal Add & Edit department -->
        <div class="modal fade" id="Department_Modal" tabindex="-1" role="dialog" aria-labelledby="Department_Modal"
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

                        <form @submit.prevent="editmode?Update_Department():Create_Department()">
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="department" class="ul-form__label">{{ __('translate.Department_Name') }}
                                        <span class="field_required">*</span></label>
                                    <input type="text" v-model="department.department" class="form-control"
                                        name="department" id="department"
                                        placeholder="{{ __('translate.Enter_Department_Name') }}">
                                    <span class="error" v-if="errors && errors.department">
                                        @{{ errors.department[0] }}
                                    </span>
                                </div>

                                <div class="col-md-12">
                                    <label class="ul-form__label">{{ __('translate.Company') }} <span
                                            class="field_required">*</span></label>
                                    <v-select @input="Selected_Company"
                                        placeholder="{{ __('translate.Choose_Company') }}"
                                        v-model="department.company_id" :reduce="label => label.value"
                                        :options="companies.map(companies => ({label: companies.name, value: companies.id}))">
                                    </v-select>

                                    <span class="error" v-if="errors && errors.company_id">
                                        @{{ errors.company_id[0] }}
                                    </span>
                                </div>

                                <div class="col-md-12">
                                    <label class="ul-form__label">{{ __('translate.Department_Head') }} </label>
                                    <v-select @input="Selected_Employee"
                                        placeholder="{{ __('translate.Choose_Department_Head') }}"
                                        v-model="department.department_head" :reduce="label => label.value"
                                        :options="employees.map(employees => ({label: employees.username, value: employees.id}))">

                                    </v-select>

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



<script>
    Vue.component('v-select', VueSelect.VueSelect)

        var app = new Vue({
        el: '#section_Department_list',
        data: {
            selectedIds:[],
            editmode: false,
            SubmitProcessing:false,
            errors:[],
            departments: {}, 
            employees:[],
            companies:[],
            department: {
                department: "",
                company_id: "",
                department_head:"",
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

            //------------------------------ Show Modal (Create Department) -------------------------------\\
            New_Department() {
                this.reset_Form();
                this.editmode = false;
                this.Get_Data_Create();
                $('#Department_Modal').modal('show');
            },

            //------------------------------ Show Modal (Update Department) -------------------------------\\
            Edit_Department(department) {
                this.editmode = true;
                this.reset_Form();
                this.Get_Data_Edit(department.id);
                this.Get_employees_by_company(department.company_id);
                this.department = department;
                $('#Department_Modal').modal('show');
            },

            Selected_Company(value) {
                if (value === null) {
                    this.department.company_id = "";
                }
                this.employees = [];
                this.department.department_head = "";
                this.Get_employees_by_company(value);
            },

            Selected_Employee(value) {
                if (value === null) {
                    this.department.department_head = "";
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
                    .get("/core/departments/create")
                    .then(response => {
                        this.companies   = response.data.companies;
                    })
                    .catch(error => {
                       
                    });
            },

              //---------------------- Get_Data_Edit  ------------------------------\\
              Get_Data_Edit(id) {
                axios
                    .get("/core/departments/"+id+"/edit")
                    .then(response => {
                        this.companies   = response.data.companies;
                    })
                    .catch(error => {
                       
                    });
            },


            //----------------------------- Reset Form ---------------------------\\
            reset_Form() {
                this.department = {
                    id: "",
                    department: "",
                    company_id: "",
                    department_head:"",
                };
                this.errors = {};
            },
            
            //------------------------ Create department ---------------------------\\
            Create_Department() {
                var self = this;
                self.SubmitProcessing = true;
                axios.post("/core/departments", {
                    department: self.department.department,
                    company_id: self.department.company_id,
                    department_head: self.department.department_head,
                }).then(response => {
                        self.SubmitProcessing = false;
                        window.location.href = '/core/departments'; 
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

           //----------------------- Update Department ---------------------------\\
            Update_Department() {
                var self = this;
                self.SubmitProcessing = true;
                axios.put("/core/departments/" + self.department.id, {
                    department: self.department.department,
                    company_id: self.department.company_id,
                    department_head: self.department.department_head,
                }).then(response => {
                        self.SubmitProcessing = false;
                        window.location.href = '/core/departments'; 
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

             //--------------------------------- Remove department ---------------------------\\
            Remove_Department(id) {

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
                            .delete("/core/departments/" + id)
                            .then(() => {
                                window.location.href = '/core/departments'; 
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
                        .post("/core/departments/delete/by_selection", {
                            selectedIds: self.selectedIds
                        })
                            .then(() => {
                                window.location.href = '/core/departments'; 
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

        $('#department_list_table').DataTable( {
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