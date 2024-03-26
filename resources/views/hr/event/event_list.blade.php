@extends('layouts.master')
@section('main-content')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/styles/vendor/vue2-clock-picker/vue2-clock-picker.min.css')}}">


@endsection

<div class="breadcrumb">
    <h1>{{ __('translate.Event_List') }}</h1>
    <ul>
        <li><a href="/hr/event">{{ __('translate.Events') }}</a></li>
        <li>{{ __('translate.Event_List') }}</li>
    </ul>
</div>

<div class="separator-breadcrumb border-top"></div>

<div class="row" id="section_Event_list">
    <div class="col-md-12">
        <div class="card text-left">
            <div class="card-header text-right bg-transparent">
                @can('event_add')
                <a class="btn btn-primary btn-md m-1" @click="New_Event"><i class="i-Add text-white mr-2"></i>
                    {{ __('translate.Create') }}</a>
                @endcan
                @can('event_delete')
                <a v-if="selectedIds.length > 0" class="btn btn-danger btn-md m-1" @click="delete_selected()"><i
                        class="i-Close-Window text-white mr-2"></i> {{ __('translate.Delete') }}</a>
                @endcan
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="event_list_table" class="display table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('translate.Event') }}</th>
                                <th>{{ __('translate.Company') }}</th>
                                <th>{{ __('translate.Department') }}</th>
                                <th>{{ __('translate.Date') }}</th>
                                <th>{{ __('translate.Time') }}</th>
                                <th>{{ __('translate.Status') }}</th>
                                <th>{{ __('translate.Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($events as $event)
                            <tr>
                                <td @click="selected_row( {{ $event->id}})"></td>
                                <td>{{$event->title}}</td>
                                <td>{{$event->company->name}}</td>
                                <td>{{$event->department->department}}</td>
                                <td>{{$event->date}}</td>
                                <td>{{$event->time}}</td>
                                <td>{{$event->status}}</td>
                                <td>
                                    @can('event_edit')
                                    <a @click="Edit_Event( {{ $event}})" class="ul-link-action text-success"
                                        data-toggle="tooltip" data-placement="top" title="Edit">
                                        <i class="i-Edit"></i>
                                    </a>
                                    @endcan
                                    @can('event_delete')
                                    <a @click="Remove_Event( {{ $event->id}})" class="ul-link-action text-danger mr-1"
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

        <!-- Modal Add & Edit event -->
        <div class="modal fade" id="Event_Modal" tabindex="-1" role="dialog" aria-labelledby="Event_Modal"
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

                        <form @submit.prevent="editmode?Update_Event():Create_Event()">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="title" class="ul-form__label">{{ __('translate.Title') }} <span
                                            class="field_required">*</span></label>
                                    <input type="text" v-model="event.title" class="form-control" name="title"
                                        id="title" placeholder="{{ __('translate.Enter_Event_Title') }}">
                                    <span class="error" v-if="errors && errors.title">
                                        @{{ errors.title[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label for="date" class="ul-form__label">{{ __('translate.Date') }} <span
                                            class="field_required">*</span></label>
                                    <vuejs-datepicker v-model="event.date"
                                        placeholder="{{ __('translate.Enter_Event_date') }}" input-class="form-control"
                                        name="date" id="date" format="yyyy-MM-dd"
                                        @closed="event.date=formatDate(event.date)">
                                    </vuejs-datepicker>
                                    <span class="error" v-if="errors && errors.date">
                                        @{{ errors.date[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label for="time" class="ul-form__label">{{ __('translate.Time') }} <span
                                            class="field_required">*</span></label>

                                    <vue-clock-picker v-model="event.time"
                                        placeholder="{{ __('translate.Enter_Event_Time') }}" name="time" id="time">
                                    </vue-clock-picker>
                                    <span class="error" v-if="errors && errors.time">
                                        @{{ errors.time[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label class="ul-form__label">{{ __('translate.Status') }} <span
                                            class="field_required">*</span></label>
                                    <v-select @input="Selected_Status" placeholder="{{ __('translate.Choose_status') }}"
                                        v-model="event.status" :reduce="(option) => option.value" :options="
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
                                    <label class="ul-form__label">{{ __('translate.Company') }} <span
                                            class="field_required">*</span></label>
                                    <v-select @input="Selected_Company"
                                        placeholder="{{ __('translate.Choose_Company') }}" v-model="event.company_id"
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
                                        v-model="event.department_id" :reduce="label => label.value"
                                        :options="departments.map(departments => ({label: departments.department, value: departments.id}))">
                                    </v-select>
                                    <span class="error" v-if="errors && errors.department_id">
                                        @{{ errors.department_id[0] }}
                                    </span>
                                </div>

                                <div class="col-md-12">
                                    <label for="note"
                                        class="ul-form__label">{{ __('translate.Please_provide_any_details') }}</label>
                                    <textarea type="text" v-model="event.note" class="form-control" name="note"
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
<script src="{{asset('assets/js/vendor/vue2-clock-picker/vue2-clock-picker.plugin.js')}}"></script>
<script src="{{asset('assets/js/vendor/vuejs-datepicker/vuejs-datepicker.min.js')}}"></script>


<script>
    Vue.use(VueClockPickerPlugin)

        Vue.component('v-select', VueSelect.VueSelect)
        var app = new Vue({
        el: '#section_Event_list',
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
            events: {}, 
            event: {
                title: "",
                note:"",
                company_id:'',
                department_id:'',
                date:"",
                time:"",
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

            //------------------------------ Show Modal (Create event) -------------------------------\\
            New_Event() {
                this.reset_Form();
                this.editmode = false;
                this.Get_Data_Create();
                $('#Event_Modal').modal('show');
            },

            //------------------------------ Show Modal (Update event) -------------------------------\\
            Edit_Event(event) {
                this.editmode = true;
                this.reset_Form();
                this.Get_Data_Edit(event.id);
                this.Get_departments_by_company(event.company_id);
                this.event = event;
                $('#Event_Modal').modal('show');
            },

            //----------------------------- Reset Form ---------------------------\\
            reset_Form() {
                this.event = {
                    id: "",
                    title: "",
                    note:"",
                    company_id:"",
                    department_id:"",
                    date:"",
                    time:"",
                    status:"",
                };
                this.errors = {};
            },


            Selected_Status(value) {
                if (value === null) {
                    this.event.status = "";
                }
            },

            Selected_Company(value) {
                if (value === null) {
                    this.event.company_id = "";
                }
                this.departments = [];
                this.event.department_id = "" ;
                this.Get_departments_by_company(value);
            },

            Selected_Department(value) {
                if (value === null) {
                    this.event.department_id = "";
                }
            },

             //---------------------- Get_departments_by_company ------------------------------\\
             Get_departments_by_company(value) {
                axios
                    .get("/core/Get_departments_by_company?id=" + value)
                    .then(({ data }) => (this.departments = data));
            },


              //---------------------- Get_Data_Create  ------------------------------\\
              Get_Data_Create() {
                axios
                    .get("/hr/event/create")
                    .then(response => {
                        this.companies   = response.data.companies;
                    })
                    .catch(error => {
                       
                    });
            },

              //---------------------- Get_Data_Edit  ------------------------------\\
              Get_Data_Edit(id) {
                axios
                    .get("/hr/event/"+id+"/edit")
                    .then(response => {
                        this.companies   = response.data.companies;
                    })
                    .catch(error => {
                       
                    });
            },
            
            //------------------------ Create event ---------------------------\\
            Create_Event() {
                var self = this;
                self.SubmitProcessing = true;
                axios.post("/hr/event", {
                    title: self.event.title,
                    note: self.event.note,
                    department_id: self.event.department_id,
                    company_id: self.event.company_id,
                    status: self.event.status,
                    date: self.event.date,
                    time: self.event.time,
                }).then(response => {
                        self.SubmitProcessing = false;
                        window.location.href = '/hr/event'; 
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

           //----------------------- Update event ---------------------------\\
            Update_Event() {
                var self = this;
                self.SubmitProcessing = true;
                axios.put("/hr/event/" + self.event.id, {
                    title: self.event.title,
                    note: self.event.note,
                    department_id: self.event.department_id,
                    company_id: self.event.company_id,
                    status: self.event.status,
                    date: self.event.date,
                    time: self.event.time,
                }).then(response => {
                        self.SubmitProcessing = false;
                        window.location.href = '/hr/event'; 
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

             //--------------------------------- Remove event ---------------------------\\
            Remove_Event(id) {

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
                            .delete("/hr/event/" + id)
                            .then(() => {
                                window.location.href = '/hr/event'; 
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
                        .post("/hr/event/delete/by_selection", {
                            selectedIds: self.selectedIds
                        })
                            .then(() => {
                                window.location.href = '/hr/event'; 
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

        $('#event_list_table').DataTable( {
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