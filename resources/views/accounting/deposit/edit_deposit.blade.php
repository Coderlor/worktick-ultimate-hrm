@extends('layouts.master')
@section('main-content')


<div class="breadcrumb">
    <h1>{{ __('translate.Edit_Deposit') }}</h1>
    <ul>
        <li><a href="/accounting/deposit">{{ __('translate.Deposit_List') }}</a></li>
        <li>{{ __('translate.Edit_Deposit') }}</li>
    </ul>
</div>

<div class="separator-breadcrumb border-top"></div>

<!-- begin::main-row -->
<div class="row" id="section_Edit_Deposit">
    <div class="col-lg-12 mb-3">
        <div class="card">

            <!--begin::form-->
            <form @submit.prevent="Edit_Deposit()">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="ul-form__label">{{ __('translate.Account') }} <span
                                    class="field_required">*</span></label>
                            <v-select @input="Selected_Account" placeholder="{{ __('translate.Choose_Account') }}"
                                v-model="deposit.account_id" :reduce="label => label.value"
                                :options="accounts.map(accounts => ({label: accounts.account_name, value: accounts.id}))">
                            </v-select>

                            <span class="error" v-if="errors && errors.account_id">
                                @{{ errors.account_id[0] }}
                            </span>
                        </div>

                        <div class="col-md-4">
                            <label class="ul-form__label">{{ __('translate.Category') }} <span
                                    class="field_required">*</span></label>
                            <v-select @input="Selected_Category" placeholder="{{ __('translate.Choose_Category') }}"
                                v-model="deposit.deposit_category_id" :reduce="label => label.value"
                                :options="categories.map(categories => ({label: categories.title, value: categories.id}))">
                            </v-select>

                            <span class="error" v-if="errors && errors.deposit_category_id">
                                @{{ errors.deposit_category_id[0] }}
                            </span>
                        </div>

                        <div class="col-md-4">
                            <label for="deposit_ref" class="ul-form__label">{{ __('translate.Deposit_Ref') }} <span
                                    class="field_required">*</span></label>
                            <input type="text" class="form-control" id="deposit_ref"
                                placeholder="{{ __('translate.Enter_deposit_Ref') }}" v-model="deposit.deposit_ref">
                            <span class="error" v-if="errors && errors.deposit_ref">
                                @{{ errors.deposit_ref[0] }}
                            </span>
                        </div>


                        <div class="col-md-4">
                            <label for="date" class="ul-form__label">{{ __('translate.Date') }} <span
                                    class="field_required">*</span></label>
                            <vuejs-datepicker id="deposit_date" placeholder="{{ __('translate.Enter_deposit_date') }}"
                                v-model="deposit.date" input-class="form-control" format="yyyy-MM-dd"
                                name="deposit_date" @closed="deposit.date=formatDate(deposit.date)">

                            </vuejs-datepicker>
                            <span class="error" v-if="errors && errors.date">
                                @{{ errors.date[0] }}
                            </span>
                        </div>


                        <div class="col-md-4">
                            <label for="amount" class="ul-form__label">{{ __('translate.Amount') }} <span
                                    class="field_required">*</span></label>
                            <input type="text" v-model="deposit.amount" class="form-control" name="amount"
                                placeholder="{{ __('translate.Enter_Amount') }}" id="amount">
                            <span class="error" v-if="errors && errors.amount">
                                @{{ errors.amount[0] }}
                            </span>
                        </div>

                        <div class="col-md-4">
                            <label class="ul-form__label">{{ __('translate.Payment_method') }} <span
                                    class="field_required">*</span></label>
                            <v-select @input="Selected_Payment_Method"
                                placeholder="{{ __('translate.Choose_Payment_method') }}"
                                v-model="deposit.payment_method_id" :reduce="label => label.value"
                                :options="payment_methods.map(payment_methods => ({label: payment_methods.title, value: payment_methods.id}))">
                            </v-select>

                            <span class="error" v-if="errors && errors.payment_method_id">
                                @{{ errors.payment_method_id[0] }}
                            </span>
                        </div>

                        <div class="col-md-4">
                            <label for="attachment" class="ul-form__label">{{ __('translate.Attachment') }}</label>
                            <input name="attachment" @change="changeAttachement" type="file" class="form-control"
                                id="attachment">
                            <span class="error" v-if="errors && errors.attachment">
                                @{{ errors.attachment[0] }}
                            </span>
                        </div>

                        <div class="col-md-12">
                            <label for="description"
                                class="ul-form__label">{{ __('translate.Please_provide_any_details') }}</label>
                            <textarea type="text" v-model="deposit.description" class="form-control" name="description"
                                id="description"
                                placeholder="{{ __('translate.Please_provide_any_details') }}"></textarea>
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
    el: '#section_Edit_Deposit',
    components: {
        vuejsDatepicker
    },
    data: {
        SubmitProcessing:false,
        errors:[],
        data: new FormData(),
        accounts: @json($accounts),
        categories: @json($categories),
        payment_methods: @json($payment_methods),
        deposit: @json($deposit),
    },
   
   
    methods: {

    
        formatDate(d){
            var m1 = d.getMonth()+1;
            var m2 = m1 < 10 ? '0' + m1 : m1;
            var d1 = d.getDate();
            var d2 = d1 < 10 ? '0' + d1 : d1;
            return [d.getFullYear(), m2, d2].join('-');
        },

        Selected_Account(value) {
            if (value === null) {
                this.deposit.account_id = "";
            }
        },

        Selected_Category(value) {
            if (value === null) {
                this.deposit.deposit_category_id = "";
            }
        },


        Selected_Payment_Method(value) {
            if (value === null) {
                this.deposit.payment_method_id = "";
            }
        },



        changeAttachement (e){
                let file = e.target.files[0];
                this.deposit.attachment = file;
            },

        //------------------------ Edit deposit ---------------------------\\
        Edit_Deposit() {
            var self = this;
            self.SubmitProcessing = true;

            self.data.append("account_id", self.deposit.account_id);
            self.data.append("deposit_category_id", self.deposit.deposit_category_id);
            self.data.append("amount", self.deposit.amount);
            self.data.append("payment_method_id", self.deposit.payment_method_id);
            self.data.append("date", self.deposit.date);
            self.data.append("deposit_ref", self.deposit.deposit_ref);
            self.data.append("description", self.deposit.description);
            self.data.append("attachment", self.deposit.attachment);
            self.data.append("_method", "put");

            axios.post("/accounting/deposit/" + self.deposit.id, self.data)
                .then(response => {
                    self.SubmitProcessing = false;
                    window.location.href = '/accounting/deposit'; 
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
    created () {
        
    },

})

</script>

@endsection