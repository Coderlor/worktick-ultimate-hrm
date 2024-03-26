@extends('layouts.master')
@section('main-content')
@section('page-css')

@endsection

<div class="breadcrumb">
    <h1>{{ __('translate.Create_Training') }}</h1>
    <ul>
        <li><a href="/trainings">{{ __('translate.Trainings') }}</a></li>
        <li>{{ __('translate.Create_Training') }}</li>
    </ul>
</div>

<div class="separator-breadcrumb border-top"></div>

<!-- begin::main-row -->
<div class="row" id="section_Training_Create">
    <div class="col-lg-12 mb-3">
        <div class="card">

            <!--begin::form-->
            <form @submit.prevent="Create_Training()">
                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6">
                            <label class="ul-form__label">{{ __('translate.Trainer') }} <span
                                    class="field_required">*</span></label>
                            <v-select @input="Selected_Trainer" placeholder="{{ __('translate.Choose_Trainer') }}"
                                v-model="training.trainer_id" :reduce="label => label.value"
                                :options="trainers.map(trainers => ({label: trainers.name, value: trainers.id}))">
                            </v-select>
                            <span class="error" v-if="errors && errors.trainer_id">
                                @{{ errors.trainer_id[0] }}
                            </span>
                        </div>

                        <div class="col-md-6">
                            <label class="ul-form__label">{{ __('translate.Training_Skill') }} <span
                                    class="field_required">*</span></label>
                            <v-select @input="Selected_Trainer_Skill"
                                placeholder="{{ __('translate.Choose_Training_Skill') }}"
                                v-model="training.training_skill_id" :reduce="label => label.value"
                                :options="training_skills.map(training_skills => ({label: training_skills.training_skill, value: training_skills.id}))">
                            </v-select>
                            <span class="error" v-if="errors && errors.training_skill_id">
                                @{{ errors.training_skill_id[0] }}
                            </span>
                        </div>

                        <div class="col-md-6">
                            <label class="ul-form__label">{{ __('translate.Company') }} <span
                                    class="field_required">*</span></label>
                            <v-select @input="Selected_Company" placeholder="{{ __('translate.Choose_Company') }}"
                                v-model="training.company_id" :reduce="label => label.value"
                                :options="companies.map(companies => ({label: companies.name, value: companies.id}))">
                            </v-select>

                            <span class="error" v-if="errors && errors.company_id">
                                @{{ errors.company_id[0] }}
                            </span>
                        </div>

                        <div class="col-md-6">
                            <label class="ul-form__label">{{ __('translate.Employees') }} <span
                                    class="field_required">*</span></label>
                            <v-select multiple @input="Selected_Employee"
                                placeholder="{{ __('translate.Choose_Employees') }}" v-model="training.assigned_to"
                                :reduce="label => label.value"
                                :options="employees.map(employees => ({label: employees.username, value: employees.id}))">
                            </v-select>
                            <span class="error" v-if="errors && errors.assigned_to">
                                @{{ errors.assigned_to[0] }}
                            </span>
                        </div>


                        <div class="col-md-6">
                            <label for="start_date" class="ul-form__label">{{ __('translate.Start_Date') }} <span
                                    class="field_required">*</span></label>

                            <vuejs-datepicker id="start_date" name="start_date"
                                placeholder="{{ __('translate.Enter_Start_date') }}" v-model="training.start_date"
                                input-class="form-control" format="yyyy-MM-dd"
                                @closed="training.start_date=formatDate(training.start_date)">
                            </vuejs-datepicker>

                            <span class="error" v-if="errors && errors.start_date">
                                @{{ errors.start_date[0] }}
                            </span>
                        </div>

                        <div class="col-md-6">
                            <label for="end_date" class="ul-form__label">{{ __('translate.Finish_Date') }} <span
                                    class="field_required">*</span></label>

                            <vuejs-datepicker id="end_date" name="end_date"
                                placeholder="{{ __('translate.Enter_Finish_date') }}" v-model="training.end_date"
                                input-class="form-control" format="yyyy-MM-dd"
                                @closed="training.end_date=formatDate(training.end_date)">
                            </vuejs-datepicker>

                            <span class="error" v-if="errors && errors.end_date">
                                @{{ errors.end_date[0] }}
                            </span>
                        </div>


                        <div class="col-md-6">
                            <label for="training_cost" class="ul-form__label">{{ __('translate.Training_Cost') }}
                            </label>
                            <input type="text" v-model="training.training_cost" class="form-control"
                                name="training_cost" id="title" placeholder="{{ __('translate.Enter_Training_Cost') }}">
                            <span class="error" v-if="errors && errors.training_cost">
                                @{{ errors.training_cost[0] }}
                            </span>
                        </div>

                        <div class="col-md-6">
                            <label class="ul-form__label">{{ __('translate.Status') }} <span
                                    class="field_required">*</span></label>
                            <v-select @input="Selected_Status" placeholder="{{ __('translate.Choose_status') }}"
                                v-model="training.status" :reduce="(option) => option.value" :options="
                                            [
                                                {label: 'Active', value: 1},
                                                {label: 'Inactive', value: 0},
                                            ]">
                            </v-select>

                            <span class="error" v-if="errors && errors.status">
                                @{{ errors.status[0] }}
                            </span>
                        </div>

                        <div class="col-md-12">
                            <label for="description" class="ul-form__label">{{ __('translate.Description') }}</label>
                            <textarea type="text" v-model="training.description" class="form-control" name="description"
                                id="description" placeholder="{{ __('translate.Enter_description') }}"></textarea>
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
    el: '#section_Training_Create',
    components: {
        vuejsDatepicker
    },
    data: {
        SubmitProcessing:false,
        errors:[],
        trainers: @json($trainers), 
        companies: @json($companies),
        training_skills: @json($training_skills),
        employees: [], 
        training: {
            training_cost: 0,
            description:"",
            trainer_id:"",
            company_id:"",
            assigned_to:[],
            training_skill_id:"",
            start_date:"",
            end_date:"",
            status:1,
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

            Selected_Trainer(value) {
                if (value === null) {
                    this.training.trainer_id = "";
                }
            },

            Selected_Trainer_Skill(value) {
                if (value === null) {
                    this.training.training_skill_id = "";
                }
            },

            Selected_Employee(value) {
                if (value === null) {
                    this.training.assigned_to = [];
                }
            },

            Selected_Status(value) {
                if (value === null) {
                    this.training.status = "";
                }
            },

            Selected_Company(value) {
                if (value === null) {
                    this.training.company_id = "";
                }
                this.employees = [];
                this.training.assigned_to = [];
                this.Get_employees_by_company(value);
            },

             //---------------------- Get_employees_by_company ------------------------------\\
            
             Get_employees_by_company(value) {
                axios
                .get("/Get_employees_by_company?id=" + value)
                .then(({ data }) => (this.employees = data));
            },


            
            //------------------------ Create Training ---------------------------\\
            Create_Training() {
                var self = this;
                self.SubmitProcessing = true;
                axios.post("/trainings", {
                    training_cost: self.training.training_cost,
                    description: self.training.description,
                    trainer_id: self.training.trainer_id,
                    company_id: self.training.company_id,
                    assigned_to: self.training.assigned_to,
                    training_skill_id: self.training.training_skill_id,
                    status: self.training.status,
                    start_date: self.training.start_date,
                    end_date: self.training.end_date,
                }).then(response => {
                        self.SubmitProcessing = false;
                        window.location.href = '/trainings'; 
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
        created() {
        }

    })

</script>

@endsection