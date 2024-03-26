@extends('layouts.master')
@section('main-content')


<div class="breadcrumb">
    <h1>{{ __('translate.Create_Employee') }}</h1>
    <ul>
        <li><a href="/employees">{{ __('translate.Employees') }}</a></li>
        <li>{{ __('translate.Create_Employee') }}</li>
    </ul>
</div>

<div class="separator-breadcrumb border-top"></div>

<!-- begin::main-row -->
<div class="row" id="section_create_employee">
    <div class="col-lg-8 mb-3">
        <div class="card">

            <!--begin::form-->
            <form @submit.prevent="Create_Employee()">
                <div class="card-body">
                    <div class="form-row ">
                        <div class="form-group col-md-6">
                            <label for="FirstName" class="ul-form__label">{{ __('translate.FirstName') }} <span
                                    class="field_required">*</span></label>
                            <input type="text" class="form-control" id="FirstName"
                                placeholder="{{ __('translate.Enter_FirstName') }}" v-model="employee.firstname">
                            <span class="error" v-if="errors && errors.firstname">
                                @{{ errors.firstname[0] }}
                            </span>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="LastName" class="ul-form__label">{{ __('translate.LastName') }} <span
                                    class="field_required">*</span></label>
                            <input type="text" class="form-control" id="LastName"
                                placeholder="{{ __('translate.Enter_LastName') }}" v-model="employee.lastname">
                            <span class="error" v-if="errors && errors.lastname">
                                @{{ errors.lastname[0] }}
                            </span>
                        </div>

                        <div class="col-md-6">
                            <label class="ul-form__label">{{ __('translate.Gender') }} <span
                                    class="field_required">*</span></label>
                            <v-select @input="Selected_Gender" placeholder="{{ __('translate.Choose_Gender') }}"
                                v-model="employee.gender" :reduce="(option) => option.value" :options="
                                    [
                                        {label: 'Male', value: 'male'},
                                        {label: 'Female', value: 'female'},
                                    ]">
                            </v-select>

                            <span class="error" v-if="errors && errors.gender">
                                @{{ errors.gender[0] }}
                            </span>
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="ul-form__label" for="picker3">{{ __('translate.Birth_date') }}</label>

                            <vuejs-datepicker id="birth_date" name="birth_date"
                                placeholder="{{ __('translate.Enter_Birth_date') }}" v-model="employee.birth_date"
                                input-class="form-control" format="yyyy-MM-dd"
                                @closed="employee.birth_date=formatDate(employee.birth_date)">
                            </vuejs-datepicker>

                        </div>

                        <div class="form-group col-md-6">
                            <label for="inputEmail4" class="ul-form__label">{{ __('translate.Email_Address') }} <span
                                    class="field_required">*</span></label>
                            <input type="email" class="form-control" id="inputtext4"
                                placeholder="{{ __('translate.Enter_email_address') }}" v-model="employee.email">
                            <span class="error" v-if="errors && errors.email">
                                @{{ errors.email[0] }}
                            </span>
                        </div>
                        @if (auth()->user()->role_users_id == 1)
                            <div class="form-group col-md-6">
                                <label class="ul-form__label">{{ __('translate.Role') }} <span
                                        class="field_required">*</span></label>
                                <v-select @input="Selected_Role" placeholder="{{ __('translate.Choose_Role') }}"
                                    v-model="employee.role_users_id" :reduce="label => label.value"
                                    :options="roles.map(roles => ({label: roles.name, value: roles.id}))">
                                </v-select>

                                <span class="error" v-if="errors && errors.role_users_id">
                                    @{{ errors.role_users_id[0] }}
                                </span>
                            </div>
                        @endif

                        <div class="col-md-6">
                            <label for="password" class="ul-form__label">{{ __('translate.Password') }} <span
                                    class="field_required">*</span></label>
                            <input type="password" v-model="employee.password" class="form-control" id="password"
                                placeholder="{{ __('translate.min_6_characters') }}">
                            <span class="error" v-if="errors && errors.password">
                                @{{ errors.password[0] }}
                            </span>
                        </div>

                        <div class="col-md-6">
                            <label for="password_confirmation"
                                class="ul-form__label">{{ __('translate.Repeat_Password') }} <span
                                    class="field_required">*</span></label>
                            <input type="password" v-model="employee.password_confirmation" class="form-control"
                                id="password_confirmation" placeholder="{{ __('translate.Repeat_Password') }}">
                            <span class="error" v-if="errors && errors.password_confirmation">
                                @{{ errors.password_confirmation[0] }}
                            </span>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="country" class="ul-form__label">{{ __('translate.Country') }} <span
                                    class="field_required">*</span></label>
                            <input type="text" class="form-control" id="country"
                                placeholder="{{ __('translate.Enter_Country') }}" v-model="employee.country">
                            <span class="error" v-if="errors && errors.country">
                                @{{ errors.country[0] }}
                            </span>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="phone" class="ul-form__label">{{ __('translate.Phone_Number') }} <span
                                    class="field_required">*</span></label>
                            <input type="text" class="form-control" id="phone"
                                placeholder="{{ __('translate.Enter_Phone_Number') }}" v-model="employee.phone">
                            <span class="error" v-if="errors && errors.phone">
                                @{{ errors.phone[0] }}
                            </span>
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="ul-form__label" for="picker3">{{ __('translate.Joining_Date') }}</label>

                            <vuejs-datepicker id="joining_date" name="joining_date"
                                placeholder="{{ __('translate.Enter_Joining_Date') }}" v-model="employee.joining_date"
                                input-class="form-control" format="yyyy-MM-dd"
                                @closed="employee.joining_date=formatDate(employee.joining_date)">
                            </vuejs-datepicker>

                        </div>

                        <div class="form-group col-md-6">
                            <label class="ul-form__label">{{ __('translate.Company') }} <span
                                    class="field_required">*</span></label>
                            <v-select @input="Selected_Company" placeholder="{{ __('translate.Choose_Company') }}"
                                v-model="employee.company_id" :reduce="label => label.value"
                                :options="companies.map(companies => ({label: companies.name, value: companies.id}))">
                            </v-select>

                            <span class="error" v-if="errors && errors.company_id">
                                @{{ errors.company_id[0] }}
                            </span>
                        </div>


                        <div class="form-group col-md-6">
                            <label class="ul-form__label">{{ __('translate.Department') }} <span
                                    class="field_required">*</span></label>
                            <v-select @input="Selected_Department" placeholder="{{ __('translate.Choose_Department') }}"
                                v-model="employee.department_id" :reduce="label => label.value"
                                :options="departments.map(departments => ({label: departments.department, value: departments.id}))">
                            </v-select>

                            <span class="error" v-if="errors && errors.department_id">
                                @{{ errors.department_id[0] }}
                            </span>
                        </div>

                        <div class="form-group col-md-6">
                            <label class="ul-form__label">{{ __('translate.Designation') }} <span
                                    class="field_required">*</span></label>
                            <v-select @input="Selected_Designation"
                                placeholder="{{ __('translate.Choose_Designation') }}" v-model="employee.designation_id"
                                :reduce="label => label.value"
                                :options="designations.map(designations => ({label: designations.designation, value: designations.id}))">
                            </v-select>

                            <span class="error" v-if="errors && errors.designation_id">
                                @{{ errors.designation_id[0] }}
                            </span>
                        </div>


                        <div class="form-group col-md-6">
                            <label class="ul-form__label">{{ __('translate.Office_Shift') }} <span
                                    class="field_required">*</span></label>
                            <v-select @input="Selected_Office_shift"
                                placeholder="{{ __('translate.Choose_Office_Shift') }}"
                                v-model="employee.office_shift_id" :reduce="label => label.value"
                                :options="office_shifts.map(office_shifts => ({label: office_shifts.name, value: office_shifts.id}))">
                            </v-select>

                            <span class="error" v-if="errors && errors.office_shift_id">
                                @{{ errors.office_shift_id[0] }}
                            </span>
                        </div>

                    </div>

                    <div class="row mt-3">
                        <div class="col-lg-6">
                            <button type="submit" class="btn btn-primary" :disabled="SubmitProcessing">
                                {{ __('translate.Submit') }}
                            </button>
                            <div v-once class="typo__p" v-if="SubmitProcessing">
                                <div class="spinner spinner-primary mt-3"></div>
                            </div>
                        </div>
                    </div>
            </form>

            <!-- end::form -->
        </div>
    </div>

</div>
@endsection

@section('page-js')
<script src="{{asset('assets/js/vendor/vuejs-datepicker/vuejs-datepicker.min.js')}}"></script>

<script>
    Vue.component('v-select', VueSelect.VueSelect)
    var app = new Vue({
    el: '#section_create_employee',
    components: {
        vuejsDatepicker
    },
    data: {
        SubmitProcessing:false,
        errors:[],
        companies: @json($companies),
        roles: @json($roles), 
        departments: [],
        designations :[],
        office_shifts :[],
        employee: {
            firstname: "",
            lastname:"",
            country:"",
            email:"",
            gender:"",
            phone:"",
            birth_date:"",
            department_id:"",
            designation_id:"",
            office_shift_id:"",
            joining_date:"",
            company_id:"",
            password: "",
            password_confirmation:"",
            role_users_id: 2,
        }, 
    },
   
   
    methods: {

        formatDate(d){
            var m1 = d.getMonth()+1;
            var m2 = m1 < 10 ? '0' + m1 : m1;
            var d1 = d.getDate();
            var d2 = d1 < 10 ? '0' + d1 : d1;
            return [d.getFullYear(), m2, d2].join('-');
        },

        Selected_Office_shift(value) {
            if (value === null) {
                this.employee.office_shift_id = "";
            }
        },

        Selected_Role(value) {
                if (value === null) {
                    this.employee.role_users_id = "";
                }
            },

        Selected_Company(value) {
            if (value === null) {
                this.employee.company_id = "";
                this.employee.department_id = "";
                this.employee.designation_id = "";
                this.employee.office_shift_id = "";
            }
            this.departments = [];
            this.designations = [];
            this.employee.department_id = "";
            this.employee.designation_id = "";
            this.employee.office_shift_id = "";
            this.Get_departments_by_company(value);
            this.Get_office_shift_by_company(value);

        },

        Selected_Department(value) {
            if (value === null) {
                this.employee.department_id = "";
                this.employee.designation_id = "";
            }
            this.designations = [];
            this.employee.designation_id = "";
            this.Get_designations_by_department(value);
        },

        Selected_Designation(value) {
            if (value === null) {
                this.employee.designation_id = "";
            }
        },

        //---------------------- Get_departments_by_company ------------------------------\\
        Get_departments_by_company(value) {
        axios
            .get("/core/Get_departments_by_company?id=" + value)
            .then(({ data }) => (this.departments = data));
        },

        //---------------------- Get designations by department ------------------------------\\
        Get_designations_by_department(value) {
        axios
            .get("/core/get_designations_by_department?id=" + value)
            .then(({ data }) => (this.designations = data));
        },

        //---------------------- Get_office_shift_by_company ------------------------------\\
        Get_office_shift_by_company(value) {
        axios
            .get("/Get_office_shift_by_company?id=" + value)
            .then(({ data }) => (this.office_shifts = data));
        },

       

        Selected_Gender(value) {
            if (value === null) {
                this.employee.gender = "";
            }
        },

        //------------------------ Create Employee ---------------------------\\
        Create_Employee() {
            var self = this;
            self.SubmitProcessing = true;
            axios.post("/employees", {
                firstname: self.employee.firstname,
                lastname: self.employee.lastname,
                country: self.employee.country,
                email: self.employee.email,
                gender: self.employee.gender,
                phone: self.employee.phone,
                birth_date: self.employee.birth_date,
                company_id: self.employee.company_id,
                department_id: self.employee.department_id,
                designation_id: self.employee.designation_id,
                office_shift_id: self.employee.office_shift_id,
                joining_date: self.employee.joining_date,

                password: self.employee.password,
                password_confirmation: self.employee.password_confirmation,
                role_users_id: self.employee.role_users_id,

            }).then(response => {
                    self.SubmitProcessing = false;
                    window.location.href = '/employees'; 
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

    },
    //-----------------------------Autoload function-------------------
    created () {
        
    },

})

</script>

@endsection