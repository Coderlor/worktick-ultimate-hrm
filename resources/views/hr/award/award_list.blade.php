@extends('layouts.master')
@section('main-content')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">


@endsection

<div class="breadcrumb">
    <h1>{{ __('translate.Award_List') }}</h1>
    <ul>
        <li><a href="/hr/award">{{ __('translate.Awards') }}</a></li>
        <li>{{ __('translate.Award_List') }}</li>
    </ul>
</div>

<div class="separator-breadcrumb border-top"></div>

<div class="row" id="section_Award_list">
    <div class="col-md-12">
        <div class="card text-left">
            <div class="card-header text-right bg-transparent">
                @can('award_add')
                <a class="btn btn-primary btn-md m-1" @click="New_Award">
                    <i class="i-Add text-white mr-2"></i> {{ __('translate.Create') }}</a>
                @endcan
                @can('award_delete')
                <a v-if="selectedIds.length > 0" class="btn btn-danger btn-md m-1" @click="delete_selected()"><i
                        class="i-Close-Window text-white mr-2"></i> {{ __('translate.Delete') }}</a>
                @endcan
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="award_list_table" class="display table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('translate.Award_Photo') }}</th>
                                <th>{{ __('translate.Company') }}</th>
                                <th>{{ __('translate.Department') }}</th>
                                <th>{{ __('translate.Employee') }}</th>
                                <th>{{ __('translate.Award_Type') }}</th>
                                <th>{{ __('translate.Award_Date') }}</th>
                                <th>{{ __('translate.Award_Gift') }}</th>
                                <th>{{ __('translate.Award_Cash') }}</th>
                                <th>{{ __('translate.Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($awards as $award)
                            <tr>
                                <td @click="selected_row( {{ $award->id}})"></td>
                                <td>
                                    <div class="ul-widget-app__profile-pic">
                                        <img class="profile-picture avatar-sm mb-2 rounded-circle img-fluid"
                                            src="{{ asset('assets/images/awards/'.$award->photo) }}" alt="">
                                    </div>
                                </td>
                                <td>{{$award->company_name}}</td>
                                <td>{{$award->department_name}}</td>
                                <td>{{$award->employee_name}}</td>
                                <td>{{$award->award_type_title}}</td>
                                <td>{{$award->date}}</td>
                                <td>{{$award->gift}}</td>
                                <td>{{$award->cash}}</td>
                                <td>
                                    @can('award_edit')
                                    <a @click="Edit_Award( {{ $award}})" class="ul-link-action text-success"
                                        data-toggle="tooltip" data-placement="top" title="Edit">
                                        <i class="i-Edit"></i>
                                    </a>
                                    @endcan
                                    @can('award_delete')
                                    <a @click="Remove_Award( {{ $award->id}})" class="ul-link-action text-danger mr-1"
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

        <!-- Modal Add & Edit Award -->
        <div class="modal fade" id="Award_Modal" tabindex="-1" role="dialog" aria-labelledby="Award_Modal"
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

                        <form @submit.prevent="editmode?Update_Award():Create_Award()" enctype="multipart/form-data">
                            <div class="row">

                                <div class="col-md-6">
                                    <label class="ul-form__label">{{ __('translate.Company') }} <span
                                            class="field_required">*</span></label>
                                    <v-select @input="Selected_Company"
                                        placeholder="{{ __('translate.Choose_Company') }}" v-model="award.company_id"
                                        :reduce="label => label.value"
                                        :options="companies.map(companies => ({label: companies.name, value: companies.id}))">
                                    </v-select>

                                    <span class="error" v-if="errors && errors.company_id">
                                        @{{ errors.company_id[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label class="ul-form__label">{{ __('translate.Department') }} <span
                                            class="field_required">*</span></label>
                                    <v-select @input="Selected_Department"
                                        placeholder="{{ __('translate.Choose_Department') }}"
                                        v-model="award.department_id" :reduce="label => label.value"
                                        :options="departments.map(departments => ({label: departments.department, value: departments.id}))">
                                    </v-select>
                                    <span class="error" v-if="errors && errors.department_id">
                                        @{{ errors.department_id[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label class="ul-form__label">{{ __('translate.Employee') }} <span
                                            class="field_required">*</span></label>
                                    <v-select @input="Selected_Employee"
                                        placeholder="{{ __('translate.Choose_Employee') }}" v-model="award.employee_id"
                                        :reduce="label => label.value"
                                        :options="employees.map(employees => ({label: employees.username, value: employees.id}))">

                                    </v-select>

                                    <span class="error" v-if="errors && errors.employee_id">
                                        @{{ errors.employee_id[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label class="ul-form__label">{{ __('translate.Award_type') }} <span
                                            class="field_required">*</span></label>
                                    <v-select @input="Selected_Award_Type"
                                        placeholder="{{ __('translate.Choose_Award_type') }}"
                                        v-model="award.award_type_id" :reduce="label => label.value"
                                        :options="award_types.map(award_types => ({label: award_types.title, value: award_types.id}))">

                                    </v-select>

                                    <span class="error" v-if="errors && errors.award_type_id">
                                        @{{ errors.award_type_id[0] }}
                                    </span>
                                </div>


                                <div class="col-md-6">
                                    <label for="date" class="ul-form__label">{{ __('translate.Award_Date') }} <span
                                            class="field_required">*</span></label>

                                    <vuejs-datepicker id="date" name="date"
                                        placeholder="{{ __('translate.Enter_award_date') }}" v-model="award.date"
                                        input-class="form-control" format="yyyy-MM-dd"
                                        @closed="award.date=formatDate(award.date)">
                                    </vuejs-datepicker>

                                    <span class="error" v-if="errors && errors.date">
                                        @{{ errors.date[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label for="gift" class="ul-form__label">{{ __('translate.Award_Gift') }} <span
                                            class="field_required">*</span></label>
                                    <input type="text" v-model="award.gift" class="form-control" name="gift"
                                        placeholder="{{ __('translate.Enter_Award_Gift') }}" id="gift">
                                    <span class="error" v-if="errors && errors.gift">
                                        @{{ errors.gift[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label for="cash" class="ul-form__label">{{ __('translate.Award_Cash') }} <span
                                            class="field_required">*</span></label>
                                    <input type="text" v-model="award.cash" class="form-control" name="cash"
                                        placeholder="{{ __('translate.Enter_Award_Cash') }}" id="cash">
                                    <span class="error" v-if="errors && errors.cash">
                                        @{{ errors.cash[0] }}
                                    </span>
                                </div>


                                <div class="col-md-6">
                                    <label for="photo" class="ul-form__label">{{ __('translate.Award_Photo') }}</label>
                                    <input name="photo" @change="changePhoto" type="file" class="form-control"
                                        id="photo">
                                    <span class="error" v-if="errors && errors.photo">
                                        @{{ errors.photo[0] }}
                                    </span>
                                </div>

                                <div class="col-md-12">
                                    <label for="note"
                                        class="ul-form__label">{{ __('translate.Please_provide_any_details') }}</label>
                                    <textarea type="text" v-model="award.note" class="form-control" name="note"
                                        id="note"
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
        el: '#section_Award_list',
        components: {
            vuejsDatepicker
        },
        data: {
            data: new FormData(),
            editmode: false,
            SubmitProcessing:false,
            companies: [],
            departments: [],
            employees:[],
            award_types:[],
            errors:[],
            selectedIds:[],
            awards: [], 
            award: {
                department_id: "",
                company_id: "",
                employee_id: "",
                date:"",
                award_type_id:"",
                note:"",
                photo:"",
                gift:"",
                cash:"",
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

            //------------------------------ Show Modal (Create Award) -------------------------------\\
            New_Award() {
                this.reset_Form();
                this.editmode = false;
                this.Get_Data_Create();
                $('#Award_Modal').modal('show');
            },

            //------------------------------ Show Modal (Update Award) -------------------------------\\
            Edit_Award(award) {
                this.editmode = true;
                this.reset_Form();
                this.Get_Data_Edit(award.id);
                this.Get_departments_by_company(award.company_id);
                this.Get_employees_by_department(award.department_id);
                this.award = award;
                this.award.photo = "";
                $('#Award_Modal').modal('show');
            },

            Selected_Company(value) {
                if (value === null) {
                    this.award.company_id = "";
                }
                this.employees = [];
                this.departments = [];
                this.award.employee_id = "";
                this.award.department_id = "";
                this.Get_departments_by_company(value);
            },

            Selected_Department(value) {
                if (value === null) {
                    this.award.department_id = "";
                    this.award.employee_id = "";
                }
                this.employee_id = [];
                this.award.employee_id = "";
                this.Get_employees_by_department(value);
            },

            Selected_Employee(value) {
                if (value === null) {
                    this.award.employee_id = "";
                }
            },

            Selected_Award_Type(value) {
                if (value === null) {
                    this.award.award_type_id = "";
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



            //----------------------------- Reset Form ---------------------------\\
            reset_Form() {
                this.award = {
                    id: "",
                    company_id: "",
                    department_id: "",
                    employee_id: "",
                    date:"",
                    award_type_id:"",
                    note:"",
                    photo:"",
                    gift:"",
                    cash:"",
                };
                this.errors = {};
            },

            changePhoto(e){
                let file = e.target.files[0];
                this.award.photo = file;
            },

           
             //---------------------- Get_Data_Create  ------------------------------\\
             Get_Data_Create() {
                axios
                    .get("/hr/award/create")
                    .then(response => {
                        this.award_types = response.data.award_types;
                        this.companies   = response.data.companies;
                    })
                    .catch(error => {
                       
                    });
            },

             //---------------------- Get_Data_Edit  ------------------------------\\
             Get_Data_Edit(id) {
                axios
                    .get("/hr/award/"+id+"/edit")
                    .then(response => {
                        this.award_types = response.data.award_types;
                        this.companies   = response.data.companies;
                    })
                    .catch(error => {
                       
                    });
            },

             //------------------------ Create Award ---------------------------\\
             Create_Award() {
                var self = this;
                self.SubmitProcessing = true;
                self.data.append("company_id", self.award.company_id);
                self.data.append("department_id", self.award.department_id);
                self.data.append("employee_id", self.award.employee_id);
                self.data.append("date", self.award.date);
                self.data.append("award_type_id", self.award.award_type_id);
                self.data.append("note", self.award.note);
                self.data.append("gift", self.award.gift);
                self.data.append("cash", self.award.cash);
                self.data.append("photo", self.award.photo);
                
                axios
                    .post("/hr/award", self.data)
                    .then(response => {
                        self.SubmitProcessing = false;
                        window.location.href = '/hr/award'; 
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

           //----------------------- Update Award ---------------------------\\
            Update_Award() {
                var self = this;
                self.SubmitProcessing = true;
                self.data.append("company_id", self.award.company_id);
                self.data.append("department_id", self.award.department_id);
                self.data.append("employee_id", self.award.employee_id);
                self.data.append("date", self.award.date);
                self.data.append("award_type_id", self.award.award_type_id);
                self.data.append("note", self.award.note);
                self.data.append("gift", self.award.gift);
                self.data.append("cash", self.award.cash);
                self.data.append("photo", self.award.photo);
                self.data.append("_method", "put");

                axios
                    .post("/hr/award/" + this.award.id, self.data)
                    .then(response => {
                        self.SubmitProcessing = false;
                        window.location.href = '/hr/award'; 
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

             //--------------------------------- Remove Award ---------------------------\\
            Remove_Award(id) {

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
                            .delete("/hr/award/" + id)
                            .then(() => {
                                window.location.href = '/hr/award'; 
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
                        .post("/hr/award/delete/by_selection", {
                            selectedIds: self.selectedIds
                        })
                            .then(() => {
                                window.location.href = '/hr/award'; 
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

        $('#award_list_table').DataTable( {
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