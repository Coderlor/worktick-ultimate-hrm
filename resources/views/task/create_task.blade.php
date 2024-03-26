@extends('layouts.master')
@section('main-content')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/vue-slider-component.css')}}">

@endsection

<div class="breadcrumb">
    <h1>{{ __('translate.Create_Task') }}</h1>
    <ul>
        <li><a href="/tasks">{{ __('translate.Tasks') }}</a></li>
        <li>{{ __('translate.Create_Task') }}</li>
    </ul>
</div>

<div class="separator-breadcrumb border-top"></div>

<!-- begin::main-row -->
<div class="row" id="section_Task_Create">
    <div class="col-lg-12 mb-3">
        <div class="card">

            <!--begin::form-->
            <form @submit.prevent="Create_Task()">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="title" class="ul-form__label">{{ __('translate.Task_Title') }} <span
                                    class="field_required">*</span></label>
                            <input type="text" v-model="task.title" class="form-control" name="title" id="title"
                                placeholder="{{ __('translate.Enter_title') }}">
                            <span class="error" v-if="errors && errors.title">
                                @{{ errors.title[0] }}
                            </span>
                        </div>

                        <div class="col-md-4">
                            <label for="start_date" class="ul-form__label">{{ __('translate.Start_Date') }} <span
                                    class="field_required">*</span></label>

                            <vuejs-datepicker id="start_date" name="start_date"
                                placeholder="{{ __('translate.Enter_Start_date') }}" v-model="task.start_date"
                                input-class="form-control" format="yyyy-MM-dd"
                                @closed="task.start_date=formatDate(task.start_date)">
                            </vuejs-datepicker>

                            <span class="error" v-if="errors && errors.start_date">
                                @{{ errors.start_date[0] }}
                            </span>
                        </div>

                        <div class="col-md-4">
                            <label for="end_date" class="ul-form__label">{{ __('translate.Finish_Date') }} <span
                                    class="field_required">*</span></label>

                            <vuejs-datepicker id="end_date" name="end_date"
                                placeholder="{{ __('translate.Enter_Finish_date') }}" v-model="task.end_date"
                                input-class="form-control" format="yyyy-MM-dd"
                                @closed="task.end_date=formatDate(task.end_date)">
                            </vuejs-datepicker>

                            <span class="error" v-if="errors && errors.end_date">
                                @{{ errors.end_date[0] }}
                            </span>
                        </div>

                        <div class="col-md-4">
                            <label class="ul-form__label">{{ __('translate.Company') }} <span
                                    class="field_required">*</span></label>
                            <v-select @input="Selected_Company" placeholder="{{ __('translate.Choose_Company') }}"
                                v-model="task.company_id" :reduce="label => label.value"
                                :options="companies.map(companies => ({label: companies.name, value: companies.id}))">
                            </v-select>

                            <span class="error" v-if="errors && errors.company_id">
                                @{{ errors.company_id[0] }}
                            </span>
                        </div>

                        <div class="col-md-4">
                            <label class="ul-form__label">{{ __('translate.Assigned_Employees') }} </label>
                            <v-select multiple @input="Selected_Team" placeholder="{{ __('translate.Choose_Team') }}"
                                v-model="task.assigned_to" :reduce="label => label.value"
                                :options="employees.map(employees => ({label: employees.username, value: employees.id}))">
                            </v-select>
                        </div>

                        <div class="col-md-4">
                            <label class="ul-form__label">{{ __('translate.Project') }} <span
                                    class="field_required">*</span></label>
                            <v-select @input="Selected_Project" placeholder="{{ __('translate.Select_Project') }}"
                                v-model="task.project_id" :reduce="label => label.value"
                                :options="projects.map(projects => ({label: projects.title, value: projects.id}))">
                            </v-select>
                            <span class="error" v-if="errors && errors.project_id">
                                @{{ errors.project_id[0] }}
                            </span>
                        </div>

                        <div class="col-md-4">
                            <label for="summary" class="ul-form__label">{{ __('translate.Summary') }} <span
                                    class="field_required">*</span></label>
                            <input type="text" v-model="task.summary" class="form-control" name="summary" id="summary"
                                placeholder="{{ __('translate.Enter_task_Summary') }}">
                            <span class="error" v-if="errors && errors.summary">
                                @{{ errors.summary[0] }}
                            </span>
                        </div>


                        <div class="col-md-4">
                            <label class="ul-form__label">{{ __('translate.Priority') }} <span
                                    class="field_required">*</span></label>
                            <v-select @input="Selected_Priority" placeholder="{{ __('translate.Select_priority') }}"
                                v-model="task.priority" :reduce="(option) => option.value" :options="
                                        [
                                            {label: 'Urgent', value: 'urgent'},
                                            {label: 'High', value: 'high'},
                                            {label: 'Medium', value: 'medium'},
                                            {label: 'Low', value: 'low'},
                                        ]">
                            </v-select>

                            <span class="error" v-if="errors && errors.priority">
                                @{{ errors.priority[0] }}
                            </span>
                        </div>

                        <div class="col-md-4">
                            <label class="ul-form__label">{{ __('translate.Status') }} <span
                                    class="field_required">*</span></label>
                            <v-select @input="Selected_Status" placeholder="{{ __('translate.Select_status') }}"
                                v-model="task.status" :reduce="(option) => option.value" :options="
                                            [
                                                {label: 'Not Started', value: 'not_started'},
                                                {label: 'In Progress', value: 'progress'},
                                                {label: 'Cancelled', value: 'cancelled'},
                                                {label: 'On Hold', value: 'hold'},
                                                {label: 'Completed', value: 'completed'},
                                            ]">
                            </v-select>

                            <span class="error" v-if="errors && errors.status">
                                @{{ errors.status[0] }}
                            </span>
                        </div>


                        <div class="col-md-4">
                            <label class="ul-form__label">{{ __('translate.Progress') }}</label>
                            <vue-slider v-model="task.task_progress" />
                        </div>

                        <div class="col-md-12">
                            <label for="description" class="ul-form__label">{{ __('translate.Description') }}</label>
                            <textarea type="text" v-model="task.description" class="form-control" name="description"
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
<script src="{{asset('assets/js/vue-slider-component.min.js')}}"></script>

<script>
    Vue.component('v-select', VueSelect.VueSelect)

    var app = new Vue({
    el: '#section_Task_Create',
    components: {
        vuejsDatepicker,
        VueSlider: window['vue-slider-component']
    },
    data: {
        SubmitProcessing:false,
        errors:[],
        projects: @json($projects),
        companies:@json($companies),
        employees:[],
        tooltip:'right',
        task: {
            title: "",
            description:"",
            summary:"",
            project_id:"",
            company_id:"",
            start_date:"",
            end_date:"",
            status:"",
            priority:"",
            task_progress:0,
            assigned_to:[],
        }, 
    },
   
   
    methods: {

        Selected_Team(value) {
            if (value === null) {
                this.task.assigned_to = [];
            }
        },

        formatDate(d){
            var m1 = d.getMonth()+1;
            var m2 = m1 < 10 ? '0' + m1 : m1;
            var d1 = d.getDate();
            var d2 = d1 < 10 ? '0' + d1 : d1;
            return [d.getFullYear(), m2, d2].join('-');
        },

        Selected_Project(value) {
            if (value === null) {
                this.task.project_id = "";
            }
        },

        Selected_Status(value) {
            if (value === null) {
                this.task.status = "";
            }
        },

        
        Selected_Priority(value) {
            if (value === null) {
                this.task.priority = "";
            }
        },

        Selected_Company(value) {
            if (value === null) {
                this.task.company_id = "";
            }
            this.employees = [];
            this.task.assigned_to = [];
            this.Get_employees_by_company(value);
        },

            
        //---------------------- Get_employees_by_company ------------------------------\\
        
        Get_employees_by_company(value) {
            axios
            .get("/Get_employees_by_company?id=" + value)
            .then(({ data }) => (this.employees = data));
        },

        
        //------------------------ Create Task ---------------------------\\
        Create_Task() {
            var self = this;
            self.SubmitProcessing = true;
            axios.post("/tasks", {
                title: self.task.title,
                description: self.task.description,
                summary: self.task.summary,
                project_id: self.task.project_id,
                company_id: self.task.company_id,
                assigned_to: self.task.assigned_to,
                priority: self.task.priority,
                start_date: self.task.start_date,
                end_date: self.task.end_date,
                status: self.task.status,
                task_progress: self.task.task_progress,
            }).then(response => {
                    self.SubmitProcessing = false;
                    window.location.href = '/tasks'; 
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