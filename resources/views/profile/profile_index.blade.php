@extends('layouts.master')
@section('page-css')
@endsection

@section('main-content')
<div class="breadcrumb">
    <h1>{{ __('translate.User_Profile') }}</h1>
    <ul>
        <li><a href="/profile">{{ __('translate.Profile') }}</a></li>
        <li>{{ __('translate.User_Profile') }}</li>
    </ul>
</div>

<div class="separator-breadcrumb border-top"></div>

<div class="card user-profile o-hidden mb-4" id="section_Profile_list">
    <div class="header-cover"></div>
    <div class="user-info">
        <img class="profile-picture avatar-lg mb-2" src="{{asset('assets/images/avatar/'.Auth::user()->avatar)}}"
            alt="">
        <p class="m-0 text-24">@{{ user.username }}</p>
    </div>
    <div class="card-body">
        <form @submit.prevent="Update_Profile()" enctype="multipart/form-data">
            <div class="row">

                <div class="col-md-6">
                    <label for="username" class="ul-form__label">{{ __('translate.FullName') }} <span
                            class="field_required">*</span></label>
                    <input type="text" v-model="user.username" class="form-control" id="username"
                        placeholder="{{ __('translate.Enter_FullName') }}">
                    <span class="error" v-if="errors && errors.username">
                        @{{ errors.username[0] }}
                    </span>
                </div>

                <div class="col-md-6">
                    <label for="email" class="ul-form__label">{{ __('translate.Email') }} <span
                            class="field_required">*</span></label>
                    <input type="text" v-model="user.email" class="form-control" id="email"
                        placeholder="{{ __('translate.Enter_email_address') }}">
                    <span class="error" v-if="errors && errors.email">
                        @{{ errors.email[0] }}
                    </span>
                </div>

                <div class="col-md-6">
                    <label for="Avatar" class="ul-form__label">{{ __('translate.Avatar') }}</label>
                    <input name="Avatar" @change="changeAvatar" type="file" class="form-control" id="Avatar">
                    <span class="error" v-if="errors && errors.avatar">
                        @{{ errors.avatar[0] }}
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
                    <label for="password_confirmation" class="ul-form__label">{{ __('translate.Repeat_Password') }}
                        <span class="field_required">*</span></label>
                    <input type="password" v-model="user.password_confirmation" class="form-control"
                        id="password_confirmation" placeholder="{{ __('translate.Repeat_Password') }}">
                    <span class="error" v-if="errors && errors.password_confirmation">
                        @{{ errors.password_confirmation[0] }}
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



@endsection

@section('page-js')

<script>
    var app = new Vue({
        el: '#section_Profile_list',
        data: {
            data: new FormData(),
            SubmitProcessing:false,
            errors:[],
            user: @json($user),
        },
       
        methods: {


            changeAvatar(e){
                let file = e.target.files[0];
                this.user.avatar = file;
            },


           //----------------------- Update Profile ---------------------------\\
           Update_Profile() {
                var self = this;
                self.SubmitProcessing = true;
                self.data.append("username", self.user.username);
                self.data.append("email", self.user.email);
                self.data.append("password", self.user.password);
                self.data.append("password_confirmation", self.user.password_confirmation);
                self.data.append("avatar", self.user.avatar);
                self.data.append("_method", "put");

                axios
                    .post("/updateProfile/" + self.user.id, self.data)
                    .then(response => {
                        self.SubmitProcessing = false;
                        window.location.href = '/profile'; 
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
           
        },
        //-----------------------------Autoload function-------------------
        created() {
        }

    })

</script>

@endsection