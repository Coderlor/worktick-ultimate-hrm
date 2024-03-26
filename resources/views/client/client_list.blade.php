@extends('layouts.master')
@section('main-content')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">


@endsection

<div class="breadcrumb">
    <h1>{{ __('translate.Client_List') }}</h1>
    <ul>
        <li><a href="/clients">{{ __('translate.Clients') }}</a></li>
        <li>{{ __('translate.Client_List') }}</li>
    </ul>
</div>

<div class="separator-breadcrumb border-top"></div>

<div class="row" id="section_Client_list">
    <div class="col-md-12">
        <div class="card text-left">
            <div class="card-header text-right bg-transparent">
                @can('client_add')
                <a class="btn btn-primary btn-md m-1" @click="New_Client"><i class="i-Add-User text-white mr-2"></i>
                    {{ __('translate.Create') }}</a>
                @endcan
                @can('client_delete')
                <a v-if="selectedIds.length > 0" class="btn btn-danger btn-md m-1" @click="delete_selected()"><i
                        class="i-Close-Window text-white mr-2"></i> {{ __('translate.Delete') }}</a>
                @endcan
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="client_list_table" class="display table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('translate.Code') }}</th>
                                <th>{{ __('translate.Firstname') }}</th>
                                <th>{{ __('translate.Lastname') }}</th>
                                <th>{{ __('translate.Email') }}</th>
                                <th>{{ __('translate.City') }}</th>
                                <th>{{ __('translate.Country') }}</th>
                                <th>{{ __('translate.Phone') }}</th>
                                <th>{{ __('translate.Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clients as $client)
                            <tr>
                                <td @click="selected_row( {{ $client->id}})"></td>
                                <td>{{$client->code}}</td>
                                <td>{{$client->firstname}}</td>
                                <td>{{$client->lastname}}</td>
                                <td>{{$client->email}}</td>
                                <td>{{$client->city}}</td>
                                <td>{{$client->country}}</td>
                                <td>{{$client->phone}}</td>
                                <td>
                                    @can('client_edit')
                                    <a @click="Edit_Client( {{ $client}})" class="ul-link-action text-success"
                                        data-toggle="tooltip" data-placement="top" title="Edit">
                                        <i class="i-Edit"></i>
                                    </a>
                                    @endcan
                                    @can('client_delete')
                                    <a @click="Remove_Client( {{ $client->id}})" class="ul-link-action text-danger mr-1"
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

        <!-- Modal Add & Edit Client -->
        <div class="modal fade" id="Client_Modal" tabindex="-1" role="dialog" aria-labelledby="Client_Modal"
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

                        <form @submit.prevent="editmode?Update_Client():Create_Client()">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="firstname" class="ul-form__label">{{ __('translate.Firstname') }} <span
                                            class="field_required">*</span></label>
                                    <input type="text" v-model="client.firstname" class="form-control" name="firstname"
                                        id="firstname" placeholder="{{ __('translate.Enter_Client_Firstname') }}">
                                    <span class="error" v-if="errors && errors.firstname">
                                        @{{ errors.firstname[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label for="lastname" class="ul-form__label">{{ __('translate.Lastname') }} <span
                                            class="field_required">*</span></label>
                                    <input type="text" v-model="client.lastname" class="form-control" name="lastname"
                                        id="lastname" placeholder="{{ __('translate.Enter_Client_Lastname') }}">
                                    <span class="error" v-if="errors && errors.lastname">
                                        @{{ errors.lastname[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="ul-form__label">{{ __('translate.Email') }} <span
                                            class="field_required">*</span></label>
                                    <input type="text" v-model="client.email" class="form-control" id="email" id="email"
                                        placeholder="{{ __('translate.Enter_email_address') }}">
                                    <span class="error" v-if="errors && errors.email">
                                        @{{ errors.email[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label for="password" class="ul-form__label">{{ __('translate.Password') }} <span
                                            class="field_required">*</span></label>
                                    <input type="password" v-model="client.password" class="form-control" id="password"
                                        placeholder="{{ __('translate.min_6_characters') }}">
                                    <span class="error" v-if="errors && errors.password">
                                        @{{ errors.password[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label for="country" class="ul-form__label">{{ __('translate.Country') }}</label>
                                    <input type="text" v-model="client.country" class="form-control" id="country"
                                        placeholder="{{ __('translate.Enter_Country') }}">
                                    <span class="error" v-if="errors && errors.country">
                                        @{{ errors.country[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label for="city" class="ul-form__label">{{ __('translate.City') }}</label>
                                    <input type="text" v-model="client.city" class="form-control" id="city"
                                        placeholder="{{ __('translate.Enter_City') }}">
                                    <span class="error" v-if="errors && errors.city">
                                        @{{ errors.city[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label for="Phone" class="ul-form__label">{{ __('translate.Phone') }}</label>
                                    <input type="text" v-model="client.phone" class="form-control" id="Phone"
                                        placeholder="{{ __('translate.Enter_Phone') }}">
                                    <span class="error" v-if="errors && errors.phone">
                                        @{{ errors.phone[0] }}
                                    </span>

                                </div>

                                <div class="col-md-6">
                                    <label for="address" class="ul-form__label">{{ __('translate.Address') }}</label>
                                    <input type="text" v-model="client.address" class="form-control" id="address"
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
    var app = new Vue({
        el: '#section_Client_list',
        data: {
            editmode: false,
            SubmitProcessing:false,
            errors:[],
            selectedIds:[],
            clients: {}, 
            client: {
                firstname: "",
                lastname: "",
                password: "",
                email: "",
                country: "",
                city: "",
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



            //------------------------------ Show Modal (Create Client) -------------------------------\\
            New_Client() {
                this.reset_Form();
                this.editmode = false;
                $('#Client_Modal').modal('show');
            },

            //------------------------------ Show Modal (Update Client) -------------------------------\\
            Edit_Client(client) {
                this.editmode = true;
                this.reset_Form();
                this.client = client;
                $('#Client_Modal').modal('show');
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
            
           
            //------------------------ Create Client ---------------------------\\
            Create_Client() {
                var self = this;
                self.SubmitProcessing = true;
                axios.post("/clients", {
                    firstname: self.client.firstname,
                    lastname: self.client.lastname,
                    password: self.client.password,
                    email: self.client.email,
                    country: self.client.country,
                    city: self.client.city,
                    phone: self.client.phone,
                    address: self.client.address,
                }).then(response => {
                        self.SubmitProcessing = false;
                        window.location.href = '/clients'; 
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

           //----------------------- Update Client ---------------------------\\
            Update_Client() {
                var self = this;
                self.SubmitProcessing = true;
                axios.put("/clients/" + self.client.id, {
                    firstname: self.client.firstname,
                    lastname: self.client.lastname,
                    email: self.client.email,
                    country: self.client.country,
                    city: self.client.city,
                    phone: self.client.phone,
                    address: self.client.address,
                }).then(response => {
                        self.SubmitProcessing = false;
                        window.location.href = '/clients'; 
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

          

             //--------------------------------- Remove Client ---------------------------\\
            Remove_Client(id) {

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
                            .delete("/clients/" + id)
                            .then(() => {
                                window.location.href = '/clients'; 
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
                        .post("/clients/delete/by_selection", {
                            selectedIds: self.selectedIds
                        })
                            .then(() => {
                                window.location.href = '/clients'; 
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

        $('#client_list_table').DataTable( {
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