@extends('layouts.master')
@section('main-content')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

<div class="breadcrumb">
    <h1>{{ __('translate.Project_List') }}</h1>
    <ul>
        <li><a href="/client_projects">{{ __('translate.Project') }}</a></li>
        <li>{{ __('translate.Project_List') }}</li>
    </ul>
</div>

<div class="separator-breadcrumb border-top"></div>

<div class="row" id="section_client_Project_list">
    <div class="col-md-12">
        <div class="card text-left">
            <div class="card-header text-right bg-transparent">
                <a class="btn btn-primary btn-md m-1" @click="New_Project"><i class="i-Add text-white mr-2"></i>
                    {{ __('translate.Create') }}</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="ul-contact-list" class="display table">
                        <thead>
                            <tr>
                                <th>{{ __('translate.Project') }}</th>
                                <th>{{ __('translate.Company') }}</th>
                                <th>{{ __('translate.Start_Date') }}</th>
                                <th>{{ __('translate.Finish_Date') }}</th>
                                <th>{{ __('translate.Priority') }}</th>
                                <th>{{ __('translate.Status') }}</th>
                                <th>{{ __('translate.Progress') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($projects as $project)
                            <tr>
                                <td>{{$project->title}}</td>
                                <td>{{$project->company->name}}</td>
                                <td>{{$project->start_date}}</td>
                                <td>{{$project->end_date}}</td>
                                <td>{{$project->priority}}</td>
                                <td>
                                    @if($project->status == 'completed')
                                    <span class="badge badge-success m-2">{{ __('translate.Completed') }}</span>
                                    @elseif($project->status == 'not_started')
                                    <span class="badge badge-warning m-2">{{ __('translate.Not_Started') }}</span>
                                    @elseif($project->status == 'progress')
                                    <span class="badge badge-primary m-2">{{ __('translate.In_Progress') }}</span>
                                    @elseif($project->status == 'cancelled')
                                    <span class="badge badge-danger m-2">{{ __('translate.Cancelled') }}</span>
                                    @elseif($project->status == 'hold')
                                    <span class="badge badge-secondary m-2">{{ __('translate.On_Hold') }}</span>
                                    @endif
                                </td>
                                <td>{{$project->project_progress}}%</td>
                            </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
        <!-- Modal Add & Project Client -->
        <div class="modal fade" id="Client_Project_Modal" tabindex="-1" role="dialog"
            aria-labelledby="Client_Project_Modal" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('translate.Create') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <!--begin::form-->
                        <form @submit.prevent="Create_Project()">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="title" class="ul-form__label">{{ __('translate.Title') }} <span
                                            class="field_required">*</span></label>
                                    <input type="text" v-model="project.title" class="form-control" name="title"
                                        id="title" placeholder="{{ __('translate.Enter_Project_Title') }}">
                                    <span class="error" v-if="errors && errors.title">
                                        @{{ errors.title[0] }}
                                    </span>
                                </div>

                                <div class="col-md-4">
                                    <label for="start_date" class="ul-form__label">{{ __('translate.Start_Date') }}
                                        <span class="field_required">*</span></label>

                                    <vuejs-datepicker id="start_date" name="start_date"
                                        placeholder="{{ __('translate.Enter_Start_date') }}"
                                        v-model="project.start_date" input-class="form-control" format="yyyy-MM-dd"
                                        @closed="project.start_date=formatDate(project.start_date)">
                                    </vuejs-datepicker>

                                    <span class="error" v-if="errors && errors.start_date">
                                        @{{ errors.start_date[0] }}
                                    </span>
                                </div>

                                <div class="col-md-4">
                                    <label for="end_date" class="ul-form__label">{{ __('translate.Finish_Date') }} <span
                                            class="field_required">*</span></label>

                                    <vuejs-datepicker id="end_date" name="end_date"
                                        placeholder="{{ __('translate.Enter_Finish_date') }}" v-model="project.end_date"
                                        input-class="form-control" format="yyyy-MM-dd"
                                        @closed="project.end_date=formatDate(project.end_date)">
                                    </vuejs-datepicker>

                                    <span class="error" v-if="errors && errors.end_date">
                                        @{{ errors.end_date[0] }}
                                    </span>
                                </div>


                                <div class="col-md-4">
                                    <label class="ul-form__label">{{ __('translate.Company') }} <span
                                            class="field_required">*</span></label>
                                    <v-select @input="Selected_Company"
                                        placeholder="{{ __('translate.Choose_Company') }}" v-model="project.company_id"
                                        :reduce="label => label.value"
                                        :options="companies.map(companies => ({label: companies.name, value: companies.id}))">
                                    </v-select>

                                    <span class="error" v-if="errors && errors.company_id">
                                        @{{ errors.company_id[0] }}
                                    </span>
                                </div>



                                <div class="col-md-4">
                                    <label for="summary" class="ul-form__label">{{ __('translate.Summary') }} <span
                                            class="field_required">*</span></label>
                                    <input type="text" v-model="project.summary" class="form-control" name="summary"
                                        id="summary" placeholder="{{ __('translate.Enter_Project_Summary') }}">
                                    <span class="error" v-if="errors && errors.summary">
                                        @{{ errors.summary[0] }}
                                    </span>
                                </div>


                                <div class="col-md-4">
                                    <label class="ul-form__label">{{ __('translate.Priority') }} <span
                                            class="field_required">*</span></label>
                                    <v-select @input="Selected_Priority"
                                        placeholder="{{ __('translate.Select_Priority') }}" v-model="project.priority"
                                        :reduce="(option) => option.value" :options="
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



                                <div class="col-md-12">
                                    <label for="description"
                                        class="ul-form__label">{{ __('translate.Description') }}</label>
                                    <textarea type="text" v-model="project.description" class="form-control"
                                        name="description" id="description"
                                        placeholder="{{ __('translate.Enter_description') }}"></textarea>
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
        el: '#section_client_Project_list',
        components: {
            vuejsDatepicker
        },
        data: {
            SubmitProcessing:false,
            errors:[],
            companies:[],
            project: {
                title: "",
                description:"",
                summary:"",
                company_id:"",
                start_date:"",
                end_date:"",
                priority:"",
            }, 
        },
       
        methods: {

            
            //------------------------------ Shox Modal New_Project -------------------------------\\
            New_Project() {
                this.reset_Form();
                this.Get_Data_Create();
                $('#Client_Project_Modal').modal('show');
            },

            
             //---------------------- Get_Data_Create  ------------------------------\\
             Get_Data_Create() {
                axios
                    .get("/client_projects/create")
                    .then(response => {
                        this.companies   = response.data;
                    })
                    .catch(error => {
                       
                    });
            },


               //----------------------------- Reset Form ---------------------------\\
               reset_Form() {
                this.client = {
                    id: "",
                    firstname: "",
                    lastname: "",
                    password: "",
                    email: "",
                    country: "",
                    city: "",
                    phone: "",
                    address: "",
                };
                this.errors = {};
            },

            
            formatDate(d){
                    var m1 = d.getMonth()+1;
                    var m2 = m1 < 10 ? '0' + m1 : m1;
                    var d1 = d.getDate();
                    var d2 = d1 < 10 ? '0' + d1 : d1;
                    return [d.getFullYear(), m2, d2].join('-');
                },
 
            Selected_Priority(value) {
                if (value === null) {
                    this.project.priority = "";
                }
             },

             Selected_Company(value) {
                if (value === null) {
                    this.project.company_id = "";
                }
            },
            
           
             //------------------------ Create Project ---------------------------\\
        Create_Project() {
            var self = this;
            self.SubmitProcessing = true;
            axios.post("/client_projects", {
                title: self.project.title,
                description: self.project.description,
                summary: self.project.summary,
                company_id: self.project.company_id,
                start_date: self.project.start_date,
                end_date: self.project.end_date,
                priority: self.project.priority,
            }).then(response => {
                    self.SubmitProcessing = false;
                    window.location.href = '/client_projects'; 
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

<script type="text/javascript">
    $(function () {
      "use strict";
      
        $('#ul-contact-list').DataTable( {
            "processing": true, // for show progress bar
            "responsive": true,
            dom: "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'f><'col-sm-12 col-md-4'B>>rtip",
            buttons: [
                'csv', 'excel', 'pdf', 'print','colvis'
            ]
        });

    });
</script>
@endsection