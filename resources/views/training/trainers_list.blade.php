@extends('layouts.master')
@section('main-content')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">


@endsection

<div class="breadcrumb">
    <h1>{{ __('translate.Trainer_List') }}</h1>
    <ul>
        <li><a href="/trainers">{{ __('translate.Trainers') }}</a></li>
        <li>{{ __('translate.Trainer_List') }}</li>
    </ul>
</div>

<div class="separator-breadcrumb border-top"></div>

<div class="row" id="section_Trainer_list">
    <div class="col-md-12">
        <div class="card text-left">
            <div class="card-header text-right bg-transparent">
                @can('trainer')
                <a class="btn btn-primary btn-md m-1" @click="New_Trainer"><i class="i-Add text-white mr-2"></i>
                    {{ __('translate.Create') }}</a>
                <a v-if="selectedIds.length > 0" class="btn btn-danger btn-md m-1" @click="delete_selected()"><i
                        class="i-Close-Window text-white mr-2"></i> {{ __('translate.Delete') }}</a>
                @endcan
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="trainer_list_table" class="display table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('translate.Company') }}</th>
                                <th>{{ __('translate.Name') }}</th>
                                <th>{{ __('translate.Email') }}</th>
                                <th>{{ __('translate.Phone') }}</th>
                                <th>{{ __('translate.Country') }}</th>
                                <th>{{ __('translate.Address') }}</th>
                                <th>{{ __('translate.Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trainers as $trainer)
                            <tr>
                                <td @click="selected_row( {{ $trainer->id}})"></td>
                                <td>{{$trainer->company->name}}</td>
                                <td>{{$trainer->name}}</td>
                                <td>{{$trainer->email}}</td>
                                <td>{{$trainer->phone}}</td>
                                <td>{{$trainer->country}}</td>
                                <td>{{$trainer->address}}</td>
                                <td>
                                    @can('trainer')
                                    <a @click="Edit_Trainer( {{ $trainer}})" class="ul-link-action text-success"
                                        data-toggle="tooltip" data-placement="top" title="Edit">
                                        <i class="i-Edit"></i>
                                    </a>

                                    <a @click="Remove_Trainer( {{ $trainer->id}})"
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

        <!-- Modal Add & Edit trainer -->
        <div class="modal fade" id="Trainer_Modal" tabindex="-1" role="dialog" aria-labelledby="Trainer_Modal"
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

                        <form @submit.prevent="editmode?Update_Trainer():Create_Trainer()">
                            <div class="row">

                                <div class="col-md-6">
                                    <label class="ul-form__label">{{ __('translate.Company') }} <span
                                            class="field_required">*</span></label>
                                    <v-select @input="Selected_Company"
                                        placeholder="{{ __('translate.Choose_Company') }}" v-model="trainer.company_id"
                                        :reduce="label => label.value"
                                        :options="companies.map(companies => ({label: companies.name, value: companies.id}))">
                                    </v-select>

                                    <span class="error" v-if="errors && errors.company_id">
                                        @{{ errors.company_id[0] }}
                                    </span>
                                </div>
                                <div class="col-md-6">
                                    <label for="name" class="ul-form__label">{{ __('translate.Name') }} <span
                                            class="field_required">*</span></label>
                                    <input type="text" v-model="trainer.name" class="form-control" name="name" id="name"
                                        placeholder="{{ __('translate.Enter_Trainer_Name') }}">
                                    <span class="error" v-if="errors && errors.name">
                                        @{{ errors.name[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="ul-form__label">{{ __('translate.Email') }} <span
                                            class="field_required">*</span></label>
                                    <input type="text" v-model="trainer.email" class="form-control" id="email"
                                        id="email" placeholder="{{ __('translate.Enter_email_address') }}">
                                    <span class="error" v-if="errors && errors.email">
                                        @{{ errors.email[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label for="Phone" class="ul-form__label">{{ __('translate.Phone') }} <span
                                            class="field_required">*</span></label>
                                    <input type="text" v-model="trainer.phone" class="form-control" id="Phone"
                                        placeholder="{{ __('translate.Enter_Phone') }}">
                                    <span class="error" v-if="errors && errors.phone">
                                        @{{ errors.phone[0] }}
                                    </span>

                                </div>

                                <div class="col-md-6">
                                    <label for="country" class="ul-form__label">{{ __('translate.Country') }}</label>
                                    <input type="text" v-model="trainer.country" class="form-control" id="country"
                                        placeholder="{{ __('translate.Enter_Country') }}">
                                    <span class="error" v-if="errors && errors.country">
                                        @{{ errors.country[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label for="address" class="ul-form__label">{{ __('translate.Address') }}</label>
                                    <input type="text" v-model="trainer.address" class="form-control" id="address"
                                        placeholder="{{ __('translate.Enter_Address') }}">
                                    <span class="error" v-if="errors && errors.address">
                                        @{{ errors.address[0] }}
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



<script>
    Vue.component('v-select', VueSelect.VueSelect)

        var app = new Vue({
        el: '#section_Trainer_list',
        data: {
            selectedIds:[],
            editmode: false,
            SubmitProcessing:false,
            errors:[],
            companies:[],
            trainers: {}, 
            trainer: {
                company_id: "",
                country: "",
                name: "",
                email: "",
                phone: "",
                address: ""
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


            //------------------------------ Show Modal (Create Trainer) -------------------------------\\
            New_Trainer() {
                this.reset_Form();
                this.Get_all_companies();
                this.editmode = false;
                $('#Trainer_Modal').modal('show');
            },

            //------------------------------ Show Modal (Update Trainer) -------------------------------\\
            Edit_Trainer(trainer) {
                this.editmode = true;
                this.reset_Form();
                this.Get_all_companies();
                this.trainer = trainer;
                $('#Trainer_Modal').modal('show');
            },

              //---------------------- Get all companies  ------------------------------\\
              Get_all_companies() {
                axios
                    .get("/trainers/create")
                    .then(response => {
                        this.companies   = response.data.companies;
                    })
                    .catch(error => {
                       
                    });
                },

                Selected_Company(value) {
                    if (value === null) {
                        this.trainer.company_id = "";
                    }
                },


            //----------------------------- Reset Form ---------------------------\\
            reset_Form() {
                this.trainer = {
                    id: "",
                    name: "",
                    email: "",
                    phone: "",
                    company_id: "",
                    country: "",
                    address: "",
                };
                this.errors = {};
            },
            
           
            //------------------------ Create Trainer ---------------------------\\
            Create_Trainer() {
                var self = this;
                self.SubmitProcessing = true;
                axios.post("/trainers", {
                    name: self.trainer.name,
                    email: self.trainer.email,
                    phone: self.trainer.phone,
                    company_id: self.trainer.company_id,
                    country: self.trainer.country,
                    address: self.trainer.address,
                }).then(response => {
                        self.SubmitProcessing = false;
                        window.location.href = '/trainers'; 
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

           //----------------------- Update Trainer ---------------------------\\
            Update_Trainer() {
                var self = this;
                self.SubmitProcessing = true;
                axios.put("/trainers/" + self.trainer.id, {
                    name: self.trainer.name,
                    email: self.trainer.email,
                    phone: self.trainer.phone,
                    address: self.trainer.address,
                    company_id: self.trainer.company_id,
                    country: self.trainer.country,
                }).then(response => {
                        self.SubmitProcessing = false;
                        window.location.href = '/trainers'; 
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

          

             //--------------------------------- Remove trainer ---------------------------\\
            Remove_Trainer(id) {

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
                            .delete("/trainers/" + id)
                            .then(() => {
                                window.location.href = '/trainers'; 
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
                        .post("/trainers/delete/by_selection", {
                            selectedIds: self.selectedIds
                        })
                            .then(() => {
                                window.location.href = '/trainers'; 
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

    $('#trainer_list_table').DataTable( {
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