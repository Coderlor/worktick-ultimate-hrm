@extends('layouts.master')
@section('main-content')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">

@endsection

<div class="breadcrumb">
    <h1>{{ __('translate.User_Controller') }}</h1>
</div>

<div class="separator-breadcrumb border-top"></div>

<div class="row" id="section_User_list">
    <div class="col-md-12">
        <div class="card text-left">
            @can('user_add')
            <div class="card-header text-right bg-transparent">
                <a class="btn btn-primary btn-md m-1" @click="New_User"><i class="i-Add-User text-white mr-2"></i>
                    {{ __('translate.New_Admin') }}</a>
            </div>
            @endcan
            <div class="card-body">
                <div class="table-responsive">
                    <table id="ul-contact-list" class="display table">
                        <thead>
                            <tr>
                                <th>{{ __('translate.Avatar') }}</th>
                                <th>{{ __('translate.Username') }}</th>
                                <th>{{ __('translate.Email') }}</th>
                                <th>{{ __('translate.Status') }}</th>
                                <th>{{ __('translate.Role') }}</th>
                                <th>{{ __('translate.Assign_Role') }}</th>
                                <th>{{ __('translate.Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>
                                    <div class="ul-widget-app__profile-pic">
                                        <img class="profile-picture avatar-sm mb-2 rounded-circle img-fluid"
                                            src="{{ asset('assets/images/avatar/'.$user->avatar) }}" alt="">
                                    </div>
                                </td>
                                <td>{{$user->username}}</td>
                                <td>{{$user->email}}</td>
                                <td>
                                    @if($user->status)
                                    <span class="badge badge-success m-2">{{ __('translate.Active') }}</span>
                                    @else
                                    <span class="badge badge-danger m-2">{{ __('translate.Inactive') }}</span>
                                    @endif
                                </td>
                                <td>{{$user->RoleUser['name']}}</td>
                                @can('group_permission')
                                @if($user->role_users_id === 1)
                                <td>{{ __('translate.Cannot_change_Permissions_for_admin') }}</td>
                                @elseif($user->role_users_id === 3)
                                <td>{{ __('translate.Cannot_change_Permissions_for_client') }}</td>
                                @else
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-primary btn-sm m-1 dropdown-toggle" type="button"
                                            id="assignRole" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            {{ __('translate.Assign_Role') }}
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="assignRole">
                                            @foreach ($roles as $role)
                                            <a class="dropdown-item cursor-pointer" 
                                                @click="assignRole( {{ $user->id}} , {{ $role->id}})">{{$role->name}}</a>
                                            @endforeach
                                        </div>
                                    </div>
                                </td>
                                @endif
                               
                                @endcan

                                <td>
                                    @can('user_edit')
                                    <a @click="Edit_User( {{ $user}})" class="ul-link-action text-success"
                                        data-toggle="tooltip" data-placement="top" title="Edit">
                                        <i class="i-Edit"></i>
                                    </a>
                                    @endcan

                                    @can('user_delete')
                                    <a @click="Remove_User( {{ $user->id}})" class="ul-link-action text-danger mr-1"
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

        <!-- Modal Add & Edit User -->
        <div class="modal fade" id="user_Modal" tabindex="-1" role="dialog" aria-labelledby="user_Modal"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 v-if="editmode" class="modal-title" id="user_Modal">{{ __('translate.Edit') }}</h5>
                        <h5 v-else class="modal-title" id="user_Modal">{{ __('translate.Create') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <form @submit.prevent="editmode?Update_User():Create_User()" enctype="multipart/form-data">
                            <div class="row">

                                <div class="col-md-6">
                                    <label for="username" class="ul-form__label">{{ __('translate.FullName') }}<span
                                            class="field_required">*</span></label>
                                    <input type="text" v-model="user.username" class="form-control" name="username"
                                        id="username" placeholder="{{ __('translate.Enter_FullName') }}">
                                    <span class="error" v-if="errors && errors.username">
                                        @{{ errors.username[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="ul-form__label">{{ __('translate.Email_Address') }}<span
                                            class="field_required">*</span></label>
                                    <input type="text" v-model="user.email" class="form-control" name="email" id="email"
                                        placeholder="{{ __('translate.Enter_email_address') }}">
                                    <span class="error" v-if="errors && errors.email">
                                        @{{ errors.email[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label for="password" class="ul-form__label">{{ __('translate.Password') }} <span
                                            class="field_required">*</span></label>
                                    <input type="password" v-model="user.password" class="form-control" id="password"
                                        placeholder="{{ __('translate.min_6_characters') }}">
                                    <span class="error" v-if="errors && errors.password">
                                        @{{ errors.password[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label for="password_confirmation"
                                        class="ul-form__label">{{ __('translate.Repeat_Password') }} <span
                                            class="field_required">*</span></label>
                                    <input type="password" v-model="user.password_confirmation" class="form-control"
                                        id="password_confirmation" placeholder="{{ __('translate.Repeat_Password') }}">
                                    <span class="error" v-if="errors && errors.password_confirmation">
                                        @{{ errors.password_confirmation[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label class="ul-form__label">{{ __('translate.Status') }} <span
                                            class="field_required">*</span></label>
                                    <v-select @input="Selected_Status" placeholder="{{ __('translate.Choose_status') }}"
                                        v-model="user.status" :reduce="(option) => option.value" :options="
                                                [
                                                    {label: 'Active', value: 1},
                                                    {label: 'Inactive', value: 0},
                                                ]">
                                    </v-select>

                                    <span class="error" v-if="errors && errors.status">
                                        @{{ errors.status[0] }}
                                    </span>
                                </div>

                                <div class="col-md-6">
                                    <label for="Avatar" class="ul-form__label">{{ __('translate.Avatar') }}</label>
                                    <input name="Avatar" @change="changeAvatar" type="file" class="form-control"
                                        id="Avatar">
                                    <span class="error" v-if="errors && errors.avatar">
                                        @{{ errors.avatar[0] }}
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
        el: '#section_User_list',
        data: {
            data: new FormData(),
            editmode: false,
            SubmitProcessing:false,
            errors:[],
            roles: @json($roles), 
            users: {}, 
            user: {
                username: "",
                password: "",
                password_confirmation:"",
                email: "",
                status: "",
                avatar: "",
            }, 
        },
       
        methods: {



            Selected_Status(value) {
                if (value === null) {
                    this.user.status = "";
                }
            },


            //------------------------------ Show Modal (Create User) -------------------------------\\
            New_User() {
                this.reset_Form();
                this.editmode = false;
                this.Get_Data_Create();
                $('#user_Modal').modal('show');
            },

            //------------------------------ Show Modal (Update User) -------------------------------\\
            Edit_User(user) {
                this.editmode = true;
                this.reset_Form();
                this.Get_Data_Edit(user.id);
                this.user = user;
                this.user.avatar = "";
                this.user.password =  "";
                this.user.password_confirmation = "";
                $('#user_Modal').modal('show');
            },

            changeAvatar(e){
                let file = e.target.files[0];
                this.user.avatar = file;
            },

            //----------------------------- Reset Form ---------------------------\\
            reset_Form() {
                this.user = {
                    id: "",
                    username: "",
                    password: "",
                    password_confirmation:"",
                    email: "",
                    status: "",
                    avatar: "",
                };
                this.errors = {};
            },

             //---------------------- Get_Data_Create  ------------------------------\\
             Get_Data_Create() {
                axios
                    .get("/users/create")
                    .then(response => {
                        this.roles   = response.data;
                    })
                    .catch(error => {
                       
                    });
            },

               //---------------------- Get_Data_Edit  ------------------------------\\
               Get_Data_Edit(id) {
                axios
                    .get("/users/"+id+"/edit")
                    .then(response => {
                        this.roles   = response.data;
                    })
                    .catch(error => {
                       
                    });
            },

            //----------------------- assignRole ---------------------------\\

            assignRole(user_id , role_id) {
                var self = this;
                axios.post("/assignRole", {
                    user_id: user_id,
                    role_id: role_id,
                }).then(response => {
                        window.location.href = '/users'; 
                        toastr.success('{{ __('translate.Updated_in_successfully') }}');
                        self.errors = {};
                    })
                    .catch(error => {
                        if (error.response.status == 422) {
                            self.errors = error.response.data.errors;
                        }
                        toastr.error('{{ __('translate.There_was_something_wronge') }}');
                    });
            },

            
            //------------------------ Create User ---------------------------\\
            Create_User() {
                var self = this;
                self.SubmitProcessing = true;
                self.data.append("username", self.user.username);
                self.data.append("email", self.user.email);
                self.data.append("password", self.user.password);
                self.data.append("password_confirmation", self.user.password_confirmation);
                self.data.append("status", self.user.status);
                self.data.append("avatar", self.user.avatar);
                
                axios
                    .post("/users", self.data)
                    .then(response => {
                        self.SubmitProcessing = false;
                        window.location.href = '/users'; 
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

           //----------------------- Update User ---------------------------\\
            Update_User() {
                var self = this;
                self.SubmitProcessing = true;
                self.data.append("username", self.user.username);
                self.data.append("email", self.user.email);
                self.data.append("password", self.user.password);
                self.data.append("password_confirmation", self.user.password_confirmation);
                self.data.append("status", self.user.status);
                self.data.append("avatar", self.user.avatar);
                self.data.append("_method", "put");

                axios
                    .post("/users/" + this.user.id, self.data)
                    .then(response => {
                        self.SubmitProcessing = false;
                        window.location.href = '/users'; 
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

             //--------------------------------- Remove User ---------------------------\\
            Remove_User(id) {

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
                            .delete("/users/" + id)
                            .then(() => {
                                window.location.href = '/users'; 
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

        $('#ul-contact-list').DataTable( {
            "processing": true, // for show progress bar
            dom: "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'f><'col-sm-12 col-md-4'B>>rtip",
            buttons: [
                'csv', 'excel', 'pdf', 'print'
            ]
        });
    });
</script>
@endsection