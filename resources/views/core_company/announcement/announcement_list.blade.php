@extends('layouts.master')
@section('main-content')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">


@endsection

<div class="breadcrumb">
    <h1>{{ __('translate.Announcement_List') }}</h1>
    <ul>
        <li><a href="/core/announcements">{{ __('translate.Announcements') }}</a></li>
        <li>{{ __('translate.Announcement_List') }}</li>
    </ul>
</div>

<div class="separator-breadcrumb border-top"></div>

<div class="row" id="section_Announcement_list">
    <div class="col-md-12">
        <div class="card text-left">
            <div class="card-header text-right bg-transparent">
                @can('announcement_add')
                <a class="btn btn-primary btn-md m-1" @click="New_Announcement"><i class="i-Add text-white mr-2"></i>
                    {{ __('translate.Create') }}</a>
                @endcan
                @can('announcement_delete')
                <a v-if="selectedIds.length > 0" class="btn btn-danger btn-md m-1" @click="delete_selected()"><i
                        class="i-Close-Window text-white mr-2"></i> {{ __('translate.Delete') }}</a>
                @endcan
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="announcement_list_table" class="display table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('translate.Title') }}</th>
                                <th>{{ __('translate.Company') }}</th>
                                <th>{{ __('translate.Department') }}</th>
                                <th>{{ __('translate.Start_Date') }}</th>
                                <th>{{ __('translate.Finish_Date') }}</th>
                                <th>{{ __('translate.Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($announcements as $announcement)
                            <tr>
                                <td @click="selected_row( {{ $announcement->id}})"></td>
                                <td>{{$announcement->title}}</td>
                                <td>{{$announcement->company_name}}</td>
                                <td>{{$announcement->department_name?$announcement->department_name:'all departments'}}
                                </td>
                                <td>{{$announcement->start_date}}</td>
                                <td>{{$announcement->end_date}}</td>
                                <td>
                                    @can('announcement_edit')
                                    <a  @click="Edit_Announcement( {{ $announcement}})"
                                        class="ul-link-action text-success" data-toggle="tooltip" data-placement="top"
                                        title="Edit">
                                        <i class="i-Edit"></i>
                                    </a>
                                    @endcan
                                    @can('announcement_delete')
                                    <a @click="Remove_Announcement( {{ $announcement->id}})"
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

        <!-- Modal Add & Edit Announcement -->
        <div class="modal fade" id="Announcement_Modal" tabindex="-1" role="dialog" aria-labelledby="Announcement_Modal"
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

                        <form @submit.prevent="editmode?Update_Announcement():Create_Announcement()">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="title" class="ul-form__label">{{ __('translate.Title') }} <span
                                            class="field_required">*</span></label>
                                    <input type="text" v-model="announcement.title" class="form-control" name="title"
                                        id="title" placeholder="{{ __('translate.Enter_title') }}">
                                    <span class="error" v-if="errors && errors.title">
                                        @{{ errors.title[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label for="summary" class="ul-form__label">{{ __('translate.Brief_Summary') }}
                                        <span class="field_required">*</span></label>
                                    <input type="text" v-model="announcement.summary" class="form-control"
                                        name="summary" id="summary" placeholder="{{ __('translate.Enter_summary') }}">
                                    <span class="error" v-if="errors && errors.summary">
                                        @{{ errors.summary[0] }}
                                    </span>
                                </div>


                                <div class="col-md-6">
                                    <label for="start_date" class="ul-form__label">{{ __('translate.Start_Date') }}
                                        <span class="field_required">*</span></label>

                                    <vuejs-datepicker id="start_date" name="start_date"
                                        placeholder="{{ __('translate.Enter_Start_date') }}"
                                        v-model="announcement.start_date" input-class="form-control" format="yyyy-MM-dd"
                                        @closed="announcement.start_date=formatDate(announcement.start_date)">
                                    </vuejs-datepicker>

                                    <span class="error" v-if="errors && errors.start_date">
                                        @{{ errors.start_date[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label for="end_date" class="ul-form__label">{{ __('translate.Finish_Date') }} <span
                                            class="field_required">*</span></label>

                                    <vuejs-datepicker id="end_date" name="end_date"
                                        placeholder="{{ __('translate.Enter_Finish_date') }}"
                                        v-model="announcement.end_date" input-class="form-control" format="yyyy-MM-dd"
                                        @closed="announcement.end_date=formatDate(announcement.end_date)">
                                    </vuejs-datepicker>

                                    <span class="error" v-if="errors && errors.end_date">
                                        @{{ errors.end_date[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label class="ul-form__label">{{ __('translate.Company') }} <span
                                            class="field_required">*</span></label>
                                    <v-select @input="Selected_Company"
                                        placeholder="{{ __('translate.Choose_Company') }}"
                                        v-model="announcement.company_id" :reduce="label => label.value"
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
                                        v-model="announcement.department_id" :reduce="label => label.value"
                                        :options="departments.map(departments => ({label: departments.department, value: departments.id}))">

                                    </v-select>

                                    <span class="error" v-if="errors && errors.department">
                                        @{{ errors.department[0] }}
                                    </span>
                                </div>


                                <div class="col-md-12">
                                    <label for="description"
                                        class="ul-form__label">{{ __('translate.Detailed_Description') }}</label>
                                    <textarea type="text" v-model="announcement.description" class="form-control"
                                        name="description" id="description"
                                        placeholder="{{ __('translate.Enter_description') }}"></textarea>
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
        el: '#section_Announcement_list',
        components: {
            vuejsDatepicker
        },
        data: {
            selectedIds:[],
            editmode: false,
            SubmitProcessing:false,
            errors:[],
            companies: [],
            departments: [],
            announcements: {}, 
            all_department : {
                id :"null",
                department :'all departments',
            },
            announcement: {
                title: "",
                description:"",
                summary:"",
                company_id:"",
                department_id:"",
                start_date:"",
                end_date:"",
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

            Selected_Department(value) {
                if (value === null) {
                    this.announcement.department_id = "";
                }
            },

            //------------------------------ Show Modal (Create announcement) -------------------------------\\
            New_Announcement() {
                this.reset_Form();
                this.departments = [];
                this.editmode = false;
                this.Get_Data_Create();
                $('#Announcement_Modal').modal('show');
            },

            //------------------------------ Show Modal (Update Announcement) -------------------------------\\
            Edit_Announcement(announcement) {
                this.editmode = true;
                this.reset_Form();
                this.Get_Data_Edit(announcement.id);
                this.Get_departments_by_company(announcement.company_id);
                this.announcement = announcement;
                if(announcement.department_id === null){
                    this.announcement.department_id = "null";
                }
                $('#Announcement_Modal').modal('show');
            },

            Selected_Company(value) {
                if (value === null) {
                    this.announcement.company_id = "";
                    this.announcement.department_id = "";
                    this.departments = [];
                }else{
                    this.departments = [];
                    this.announcement.department_id = "";
                
                    this.Get_departments_by_company(value);
                }
            },


            
             //---------------------- Get Data Create  ------------------------------\\
             Get_Data_Create() {
                axios
                    .get("/core/announcements/create")
                    .then(response => {
                        this.companies   = response.data.companies;
                    })
                    .catch(error => {
                       
                    });
            },

             
             //---------------------- Get Data Edit  ------------------------------\\
             Get_Data_Edit(id) {
                axios
                    .get("/core/announcements/"+id+"/edit")
                    .then(response => {
                        this.companies   = response.data.companies;
                    })
                    .catch(error => {
                       
                    });
            },

            //---------------------- Get_departments_by_company ------------------------------\\
            Get_departments_by_company(value) {

                axios
                .get("/core/Get_departments_by_company?id=" + value)
                    .then(response => {
                        this.departments = response.data;
                        
                    })
                    .catch(error => {
                       
                    });
            },

            //----------------------------- Reset Form ---------------------------\\
            reset_Form() {
                this.announcement = {
                    id: "",
                    title: "",
                    description:"",
                    summary:"",
                    company_id:"",
                    department_id:"",
                    start_date:"",
                    end_date:"",
                };
                this.errors = {};
            },
            
            //------------------------ Create Announcement ---------------------------\\
            Create_Announcement() {
                var self = this;
                self.SubmitProcessing = true;
                axios.post("/core/announcements", {
                    title: self.announcement.title,
                    description: self.announcement.description,
                    summary: self.announcement.summary,
                    company_id: self.announcement.company_id,
                    department: self.announcement.department_id,
                    start_date: self.announcement.start_date,
                    end_date: self.announcement.end_date,
                }).then(response => {
                        self.SubmitProcessing = false;
                        window.location.href = '/core/announcements'; 
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

           //----------------------- Update Announcement ---------------------------\\
            Update_Announcement() {
                var self = this;
                self.SubmitProcessing = true;
                axios.put("/core/announcements/" + self.announcement.id, {
                    title: self.announcement.title,
                    description: self.announcement.description,
                    summary: self.announcement.summary,
                    company_id: self.announcement.company_id,
                    department: self.announcement.department_id,
                    start_date: self.announcement.start_date,
                    end_date: self.announcement.end_date,
                }).then(response => {
                        self.SubmitProcessing = false;
                        window.location.href = '/core/announcements'; 
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

             //--------------------------------- Remove Announcement ---------------------------\\
            Remove_Announcement(id) {

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
                            .delete("/core/announcements/" + id)
                            .then(() => {
                                window.location.href = '/core/announcements'; 
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
                        .post("/core/announcements/delete/by_selection", {
                            selectedIds: self.selectedIds
                        })
                            .then(() => {
                                window.location.href = '/core/announcements'; 
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

        $('#announcement_list_table').DataTable( {
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