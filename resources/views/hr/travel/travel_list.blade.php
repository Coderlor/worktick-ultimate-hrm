@extends('layouts.master')
@section('main-content')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">


@endsection

<div class="breadcrumb">
    <h1>{{ __('translate.Travel_List') }}</h1>
    <ul>
        <li><a href="/hr/travel">{{ __('translate.Travels') }}</a></li>
        <li>{{ __('translate.Travel_List') }}</li>
    </ul>
</div>

<div class="separator-breadcrumb border-top"></div>

<div class="row" id="section_Travel_list">
    <div class="col-md-12">
        <div class="card text-left">
            <div class="card-header text-right bg-transparent">
                @can('travel_add')
                <a class="btn btn-primary btn-md m-1" @click="New_Travel"><i class="i-Add text-white mr-2"></i>
                    {{ __('translate.Create') }}</a>
                @endcan
                @can('travel_delete')
                <a v-if="selectedIds.length > 0" class="btn btn-danger btn-md m-1" @click="delete_selected()"><i
                        class="i-Close-Window text-white mr-2"></i> {{ __('translate.Delete') }}</a>
                @endcan
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="travel_list_table" class="display table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('translate.Employee') }}</th>
                                <th>{{ __('translate.Company') }}</th>
                                <th>{{ __('translate.Type') }}</th>
                                <th>{{ __('translate.Start_Date') }}</th>
                                <th>{{ __('translate.Finish_Date') }}</th>
                                <th>{{ __('translate.Purpose_of_visit') }}</th>
                                <th>{{ __('translate.Expected_Budget') }}</th>
                                <th>{{ __('translate.Actual_Budget') }}</th>
                                <th>{{ __('translate.Status') }}</th>
                                <th>{{ __('translate.Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($travels as $travel)
                            <tr>
                                <td @click="selected_row( {{ $travel->id}})"></td>
                                <td>{{$travel->employee->username}}</td>
                                <td>{{$travel->company->name}}</td>
                                <td>{{$travel->arrangement_type->title}}</td>
                                <td>{{$travel->start_date}}</td>
                                <td>{{$travel->end_date}}</td>
                                <td>{{$travel->visit_purpose}}</td>
                                <td>{{$travel->expected_budget}}</td>
                                <td>{{$travel->actual_budget}}</td>
                                <td>{{$travel->status}}</td>
                                <td>
                                    @can('travel_edit')
                                    <a @click="Edit_Travel( {{ $travel}})" class="ul-link-action text-success"
                                        data-toggle="tooltip" data-placement="top" title="Edit">
                                        <i class="i-Edit"></i>
                                    </a>
                                    @endcan
                                    @can('travel_delete')
                                    <a @click="Remove_Travel( {{ $travel->id}})" class="ul-link-action text-danger mr-1"
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

        <!-- Modal Add & Edit Travel -->
        <div class="modal fade" id="Travel_Modal" tabindex="-1" role="dialog" aria-labelledby="Travel_Modal"
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

                        <form @submit.prevent="editmode?Update_Travel():Create_Travel()">
                            <div class="row">


                                <div class="col-md-6">
                                    <label class="ul-form__label">{{ __('translate.Company') }} <span
                                            class="field_required">*</span></label>
                                    <v-select @input="Selected_Company"
                                        placeholder="{{ __('translate.Choose_Company') }}" v-model="travel.company_id"
                                        :reduce="label => label.value"
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
                                        placeholder="{{ __('translate.Choose_Employee') }}" v-model="travel.employee_id"
                                        :reduce="label => label.value"
                                        :options="employees.map(employees => ({label: employees.username, value: employees.id}))">

                                    </v-select>

                                    <span class="error" v-if="errors && errors.employee_id">
                                        @{{ errors.employee_id[0] }}
                                    </span>
                                </div>


                                <div class="col-md-6">
                                    <label for="start_date" class="ul-form__label">{{ __('translate.Start_Date') }}
                                        <span class="field_required">*</span></label>

                                    <vuejs-datepicker id="start_date" name="start_date"
                                        placeholder="{{ __('translate.Enter_Start_date') }}" v-model="travel.start_date"
                                        input-class="form-control" format="yyyy-MM-dd"
                                        @closed="travel.start_date=formatDate(travel.start_date)">
                                    </vuejs-datepicker>

                                    <span class="error" v-if="errors && errors.start_date">
                                        @{{ errors.start_date[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label for="end_date" class="ul-form__label">{{ __('translate.Finish_Date') }} <span
                                            class="field_required">*</span></label>

                                    <vuejs-datepicker id="end_date" name="end_date"
                                        placeholder="{{ __('translate.Enter_Finish_date') }}" v-model="travel.end_date"
                                        input-class="form-control" format="yyyy-MM-dd"
                                        @closed="travel.end_date=formatDate(travel.end_date)">
                                    </vuejs-datepicker>

                                    <span class="error" v-if="errors && errors.end_date">
                                        @{{ errors.end_date[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label for="visit_purpose"
                                        class="ul-form__label">{{ __('translate.Purpose_of_visit') }} <span
                                            class="field_required">*</span></label>
                                    <input type="text" v-model="travel.visit_purpose" class="form-control"
                                        name="visit_purpose" placeholder="{{ __('translate.Enter_Purpose_of_visit') }}"
                                        id="visit_purpose">
                                    <span class="error" v-if="errors && errors.visit_purpose">
                                        @{{ errors.visit_purpose[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label for="visit_place" class="ul-form__label">{{ __('translate.Place_of_visit') }}
                                        <span class="field_required">*</span></label>
                                    <input type="text" v-model="travel.visit_place" class="form-control"
                                        name="visit_place" placeholder="{{ __('translate.Enter_Place_of_visit') }}"
                                        id="visit_place">
                                    <span class="error" v-if="errors && errors.visit_place">
                                        @{{ errors.visit_place[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label class="ul-form__label">{{ __('translate.Modes_of_Travel') }} <span
                                            class="field_required">*</span></label>
                                    <v-select @input="Selected_travel_mode"
                                        placeholder="{{ __('translate.Choose_Modes_of_Travel') }}"
                                        v-model="travel.travel_mode" :reduce="(option) => option.value" :options="
                                            [
                                                {label: 'Bus', value: 'bus'},
                                                {label: 'Train', value: 'train'},
                                                {label: 'Car', value: 'car'},
                                                {label: 'Taxi', value: 'taxi'},
                                                {label: 'Air', value: 'air'},
                                                {label: 'Other', value: 'other'},
                                            ]">
                                    </v-select>

                                    <span class="error" v-if="errors && errors.travel_mode">
                                        @{{ errors.travel_mode[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label class="ul-form__label">{{ __('translate.Arrangement_Type') }} <span
                                            class="field_required">*</span></label>
                                    <v-select @input="Selected_Arrangement_Type"
                                        placeholder="{{ __('translate.Choose_Arrangement_Type') }}"
                                        v-model="travel.arrangement_type_id" :reduce="label => label.value"
                                        :options="arrangement_types.map(arrangement_types => ({label: arrangement_types.title, value: arrangement_types.id}))">

                                    </v-select>

                                    <span class="error" v-if="errors && errors.arrangement_type_id">
                                        @{{ errors.arrangement_type_id[0] }}
                                    </span>
                                </div>




                                <div class="col-md-6">
                                    <label for="expected_budget"
                                        class="ul-form__label">{{ __('translate.Expected_Budget') }} <span
                                            class="field_required">*</span></label>
                                    <input type="text" v-model="travel.expected_budget" class="form-control"
                                        name="expected_budget" placeholder="{{ __('translate.Enter_Expected_Budget') }}"
                                        id="expected_budget">
                                    <span class="error" v-if="errors && errors.expected_budget">
                                        @{{ errors.expected_budget[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label for="actual_budget"
                                        class="ul-form__label">{{ __('translate.Actual_Budget') }} <span
                                            class="field_required">*</span></label>
                                    <input type="text" v-model="travel.actual_budget" class="form-control"
                                        name="actual_budget" placeholder="{{ __('translate.Enter_Actual_Budget') }}"
                                        id="actual_budget">
                                    <span class="error" v-if="errors && errors.actual_budget">
                                        @{{ errors.actual_budget[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label class="ul-form__label">{{ __('translate.Status') }} <span
                                            class="field_required">*</span></label>
                                    <v-select @input="Selected_Status" placeholder="{{ __('translate.Choose_status') }}"
                                        v-model="travel.status" :reduce="(option) => option.value" :options="
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

                                <div class="col-md-12">
                                    <label for="description"
                                        class="ul-form__label">{{ __('translate.Please_provide_any_details') }}</label>
                                    <textarea type="text" v-model="travel.description" class="form-control"
                                        name="description" id="description"
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
        el: '#section_Travel_list',
        components: {
            vuejsDatepicker
        },
        data: {
            selectedIds:[],
            editmode: false,
            SubmitProcessing:false,
            companies:[],
            employees:[],
            arrangement_types:[],
            errors:[],
            travels: [], 
            travel: {
                company_id: "",
                employee_id: "",
                arrangement_type_id:"",
                expected_budget:"",
                actual_budget:"",
                start_date:"",
                end_date:"",
                visit_purpose:"",
                visit_place:"",
                travel_mode:"",
                description:"",
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

            Selected_Status(value) {
                if (value === null) {
                    this.travel.status = "";
                }
            },


            //------------------------------ Show Modal (Create Travel) -------------------------------\\
            New_Travel() {
                this.reset_Form();
                this.editmode = false;
                this.Get_Data_Create();
                $('#Travel_Modal').modal('show');
            },

            //------------------------------ Show Modal (Update Travel) -------------------------------\\
            Edit_Travel(travel) {
                this.editmode = true;
                this.reset_Form();
                this.Get_Data_Edit(travel.id);
                this.Get_employees_by_company(travel.company_id);
                this.travel = travel;
                $('#Travel_Modal').modal('show');
            },
            

            Selected_travel_mode(value) {
                if (value === null) {
                    this.travel.employee_id = "";
                }
            }, 

            Selected_Company(value) {
                if (value === null) {
                    this.travel.company_id = "";
                }
                this.employees = [];
                this.travel.employee_id = "";
                this.Get_employees_by_company(value);
            },

            Selected_Employee(value) {
                if (value === null) {
                    this.travel.travel_mode = "";
                }
            },

            Selected_Arrangement_Type(value) {
                if (value === null) {
                    this.travel.arrangement_type_id = "";
                }
            },
            //----------------------------- Reset Form ---------------------------\\
            reset_Form() {
                this.travel = {
                    id: "",
                    employee_id: "",
                    arrangement_type_id:"",
                    expected_budget:"",
                    actual_budget:"",
                    start_date:"",
                    end_date:"",
                    visit_purpose:"",
                    visit_place:"",
                    travel_mode:"",
                    description:"",
                    status:"",
                };
                this.errors = {};
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
                    .get("/hr/travel/create")
                    .then(response => {
                        this.companies   = response.data.companies;
                        this.arrangement_types   = response.data.arrangement_types;
                    })
                    .catch(error => {
                       
                    });
            },

              //---------------------- Get_Data_Edit  ------------------------------\\
              Get_Data_Edit(id) {
                axios
                    .get("/hr/travel/"+id+"/edit")
                    .then(response => {
                        this.companies   = response.data.companies;
                        this.arrangement_types   = response.data.arrangement_types;
                    })
                    .catch(error => {
                       
                    });
            },


              //------------------------ Create Travel ---------------------------\\
              Create_Travel() {
                var self = this;
                self.SubmitProcessing = true;
                axios.post("/hr/travel", {
                    company_id: self.travel.company_id,
                    employee_id: self.travel.employee_id,
                    arrangement_type_id: self.travel.arrangement_type_id,
                    description: self.travel.description,
                    expected_budget: self.travel.expected_budget,
                    actual_budget: self.travel.actual_budget,
                    start_date: self.travel.start_date,
                    end_date: self.travel.end_date,
                    visit_purpose: self.travel.visit_purpose,
                    visit_place: self.travel.visit_place,
                    travel_mode: self.travel.travel_mode,
                    status: self.travel.status,
                }).then(response => {
                        self.SubmitProcessing = false;
                        window.location.href = '/hr/travel'; 
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

           //----------------------- Update Travel ---------------------------\\
            Update_Travel() {
                var self = this;
                self.SubmitProcessing = true;
                axios.put("/hr/travel/" + self.travel.id, {
                    company_id: self.travel.company_id,
                    employee_id: self.travel.employee_id,
                    arrangement_type_id: self.travel.arrangement_type_id,
                    description: self.travel.description,
                    expected_budget: self.travel.expected_budget,
                    actual_budget: self.travel.actual_budget,
                    start_date: self.travel.start_date,
                    end_date: self.travel.end_date,
                    visit_purpose: self.travel.visit_purpose,
                    visit_place: self.travel.visit_place,
                    travel_mode: self.travel.travel_mode,
                    status: self.travel.status,
                }).then(response => {
                        self.SubmitProcessing = false;
                        window.location.href = '/hr/travel'; 
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

          

             //--------------------------------- Remove Travel ---------------------------\\
            Remove_Travel(id) {

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
                            .delete("/hr/travel/" + id)
                            .then(() => {
                                window.location.href = '/hr/travel'; 
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
                        .post("/hr/travel/delete/by_selection", {
                            selectedIds: self.selectedIds
                        })
                            .then(() => {
                                window.location.href = '/hr/travel'; 
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

        $('#travel_list_table').DataTable( {
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