@extends('layouts.master')
@section('main-content')


<div class="breadcrumb">
    <h1>{{ __('translate.Settings') }}</h1>
    <ul>
        <li><a href="/settings/system_settings">{{ __('translate.Update_Log') }}</a></li>
        <li>{{ __('translate.Settings') }}</li>
    </ul>
</div>

<div class="separator-breadcrumb border-top"></div>

<section id="section_update_Settings">
    {{-- Update_settings --}}
    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">
                    <span>{{ __('translate.Update_Log') }}</span>
                </div>
                <!--begin::form-->
                <div class="card-body">
                        <div class="alert alert-danger">Note : Make sure you backup your Current version & database before you run the Upgrade , To restore it if there is an error</div>
                        <div class="alert alert-info" v-if="version !=''">
                            <strong>Update Available
                                <span class="badge badge-pill badge-info">
                                    @{{version}}
                                </span>
                            </strong>
                           
                        </div>
                        <div class="alert alert-info" v-else>
                            <strong>You already have the latest version <span class="badge badge-pill badge-info"></span></strong>
                        </div>
                          <div class="col-md-12 text-center mt-3">
                            <a href="https://github.com/uilibrary/WorkTick-Issue-and-Features-Request" target="_blank"
                                class="btn btn-outline-info">View Change Log</a>
                        </div>
    
                         <div class="col-md-12 mt-3">
                           <h5>Please follow these steps, To Update your application</h5>
                           <div class="allert alert-danger">Note 1: If you have made any changes in the code manually then your changes will be lost.</div>
                            <div class="allert alert-danger">Note 2: only admin he can upgrade the system</div>
                           <ul>
                            <li>
                               <strong>Step 1 : </strong>Take back up of your database,  Go to <a href="/settings/backup">Backup</a> Click on Generate Backup ,
                               You will find it in <strong>/storage/app/public/backup</strong>  and save it to your pc To restore it if there is an error , 
                               or Go to your PhpMyAdmin and export your database then and save it to your pc To restore it if there is an error
                            </li>
    
                             <li>
                               <strong>Step 2 : </strong> Take back up of your files before updating.
                             </li>
    
                            <li>
                               <strong>Step 3 : </strong>  Download the latest version from your codecanyon and Extract it .
                             </li>
    
                             <li>
                               <strong>Step 4 : </strong> Replace all the files and folders <strong>except</strong> the following :
                                <ul>
                                    <li>file   : <strong>.env</strong></li>
                                    <li>file   : <strong>.htaccess</strong></li>
                                    <li>Folder : <strong>storage</strong></li>
                                    <li>Folder : <strong>Modules</strong> (If you have any Module installed)</li>
                                    <li>file   : <strong>modules_statuses.json</strong> (If you have any Module installed)</li>
                                    <li>Folder : <strong>images folder in public : /public/assets/images</strong></li>
                                  </ul>
                              </li>
    
                             <li>
                               <strong>Step 5 : </strong>Visit  http://your_app/update_database to update your database
                             </li>
    
                              <li>
                               <strong>Step 6 : </strong> Hard Clear your cache browser
                             </li>
    
                               <li>
                               <strong>Step 7 : </strong> You are done! Enjoy the updated application
                             </li>
    
                           </ul>
                           <div class="allert alert-danger">Note: If any pages are not loading or blank, make sure you cleared your browser cache.</div>
                         </div>
    

            </div>
        </div>
    </div>



</section>

@endsection

@section('page-js')

<script>
    var app = new Vue({
            el: '#section_update_Settings',
            data: {
                SubmitProcessing:false,
                errors:[],
                version: @json($version),
            },
           
            methods: {
    
                
            },
            //-----------------------------Autoload function-------------------
            created() {
            }
    
        })
    
</script>

@endsection