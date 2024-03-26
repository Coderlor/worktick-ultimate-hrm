<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


$installed = Storage::disk('public')->exists('installed');

if ($installed === true) {

    Auth::routes(['register' => false]);


    Route::group(['middleware'=>'XSS'],function(){

        Route::get('/', "HomeController@RedirectToLogin");
        Route::get('switch/language/{lang}', 'LocalController@languageSwitch')->name('language.switch');

        //------------------------------- dashboard Admin--------------------------\\

        Route::group(['middleware'=>'auth','Is_Admin'],function(){

            Route::get('dashboard/admin', "DashboardController@dashboard_admin")->name('dashboard');
           
            //------------------------------------------------------------------\\

            Route::get('/update_database', 'UpdateController@viewStep1');

            Route::get('/update_database/finish', function () {

                return view('update.finishedUpdate');
            });

            Route::post('/update_database/lastStep', [
                'as' => 'update_lastStep', 'uses' => 'UpdateController@lastStep',
            ]);
            
        });

        Route::group(['middleware'=>'auth'],function(){
            
            //------------------------------- dashboard --------------------------\\
            //--------------------------------------------------------------------\\
            
            Route::get('dashboard/employee', "DashboardController@dashboard_employee")->name('dashboard_employee');
            Route::get('dashboard/client', "DashboardController@dashboard_client")->name('dashboard_client');

            //------------------------------- Employee --------------------------\\
            //--------------------------------------------------------------------\\
            Route::resource('employees', 'EmployeesController');
            Route::get("Get_all_employees", "EmployeesController@Get_all_employees");
            Route::get("Get_employees_by_company", "EmployeesController@Get_employees_by_company");
            Route::get("Get_employees_by_department", "EmployeesController@Get_employees_by_department");
            Route::get("Get_office_shift_by_company", "EmployeesController@Get_office_shift_by_company");
            Route::put("update_social_profile/{id}", "EmployeesController@update_social_profile");
            Route::put("update_employee_document/{id}", "EmployeesController@update_employee_document");
            Route::post("employees/delete/by_selection", "EmployeesController@delete_by_selection");

            
            //------------------------------- Employee Experience ----------------\\
            //--------------------------------------------------------------------\\
            
            Route::resource('work_experience', 'EmployeeExperienceController');


                //------------------------------- Employee Document ----------------\\
            //--------------------------------------------------------------------\\
            
            Route::resource('employee_document', 'EmployeeDocumentController'); 
                
            //------------------------------- Employee Accounts bank ----------------\\
            //--------------------------------------------------------------------\\
            
            Route::resource('employee_account', 'EmployeeAccountController');

            //------------------------------- Hr Management --------------------------\\
            //----------------------------------------------------------------------\\

            Route::prefix('core')->group(function () {


                //------------------------------- company --------------------------\\
                //--------------------------------------------------------------------\\
                Route::resource('company', 'CompanyController');
                Route::get("Get_all_Company", "CompanyController@Get_all_Company");
                Route::post("company/delete/by_selection", "CompanyController@delete_by_selection");

                //------------------------------- departments --------------------------\\
                //--------------------------------------------------------------------\\
                Route::resource('departments', 'DepartmentsController');
                Route::get("Get_all_departments", "DepartmentsController@Get_all_Departments");
                Route::get("Get_departments_by_company", "DepartmentsController@Get_departments_by_company")->name('Get_departments_by_company');
                Route::post("departments/delete/by_selection", "DepartmentsController@delete_by_selection");

                //------------------------------- designations --------------------------\\
                //--------------------------------------------------------------------\\
                Route::resource('designations', 'DesignationsController');
                Route::get("get_designations_by_department", "DesignationsController@Get_designations_by_department");
                Route::post("designations/delete/by_selection", "DesignationsController@delete_by_selection");

                //------------------------------- policies --------------------------\\
                //--------------------------------------------------------------------\\
                Route::resource('policies', 'PoliciesController');
                Route::post("policies/delete/by_selection", "PoliciesController@delete_by_selection");


                //------------------------------- announcements ---------------------\\
                //--------------------------------------------------------------------\\
                Route::resource('announcements', 'AnnouncementsController');
                Route::post("announcements/delete/by_selection", "AnnouncementsController@delete_by_selection");

            });


            //------------------------------- Attendances ------------------------\\
            //--------------------------------------------------------------------\\
            Route::resource('attendances', 'AttendancesController');
            Route::get("daily_attendance", "AttendancesController@daily_attendance")->name('daily_attendance');
            Route::post('attendance_by_employee/{id}', 'EmployeeSessionController@attendance_by_employee')->name('attendance_by_employee.post');
            Route::post("attendances/delete/by_selection", "AttendancesController@delete_by_selection");



            //------------------------------- Accounting -----------------------\\
            //----------------------------------------------------------------\\
            Route::prefix('accounting')->group(function () {

                Route::resource('account', 'AccountController');
                Route::resource('deposit', 'DepositController');
                Route::resource('expense', 'ExpenseController');
                Route::resource('expense_category', 'ExpenseCategoryController');
                Route::resource('deposit_category', 'DepositCategoryController');
                Route::resource('payment_methods', 'PaymentMethodController');

                Route::post("account/delete/by_selection", "AccountController@delete_by_selection");
                Route::post("deposit/delete/by_selection", "DepositController@delete_by_selection");
                Route::post("expense/delete/by_selection", "ExpenseController@delete_by_selection");
                Route::post("expense_category/delete/by_selection", "ExpenseCategoryController@delete_by_selection");
                Route::post("deposit_category/delete/by_selection", "DepositCategoryController@delete_by_selection");
                Route::post("payment_methods/delete/by_selection", "PaymentMethodController@delete_by_selection");

            });


            //------------------------------- Project -----------------------\\
            //----------------------------------------------------------------\\

            Route::resource('projects', 'ProjectController');

            Route::post("projects/delete/by_selection", "ProjectController@delete_by_selection");
            Route::post("project_discussions", "ProjectController@Create_project_discussions");
            Route::delete("project_discussions/{id}", "ProjectController@destroy_project_discussion");

            Route::post("project_issues", "ProjectController@Create_project_issues");
            Route::put("project_issues/{id}", "ProjectController@Update_project_issues");
            Route::delete("project_issues/{id}", "ProjectController@destroy_project_issues");

            Route::post("project_documents", "ProjectController@Create_project_documents");
            Route::delete("project_documents/{id}", "ProjectController@destroy_project_documents");

            //------------------------------- Task -----------------------\\
            //----------------------------------------------------------------\\

            Route::resource('tasks', 'TaskController');
            Route::put("update_task_status/{id}", "TaskController@update_task_status");

            Route::post("tasks/delete/by_selection", "TaskController@delete_by_selection");
            Route::get("tasks_kanban", "TaskController@tasks_kanban")->name('tasks_kanban');
            Route::post("task_change_status", "TaskController@task_change_status")->name('task_change_status');

            Route::post("task_discussions", "TaskController@Create_task_discussions");
            Route::delete("task_discussions/{id}", "TaskController@destroy_task_discussion");

            Route::post("task_documents", "TaskController@Create_task_documents");
            Route::delete("task_documents/{id}", "TaskController@destroy_task_documents");

            //------------------------------- Request leave  -----------------------\\
            //----------------------------------------------------------------\\

            Route::resource('leave', 'LeaveController');
            Route::resource('leave_type', 'LeaveTypeController');
            Route::post("leave/delete/by_selection", "LeaveController@delete_by_selection");
            Route::post("leave_type/delete/by_selection", "LeaveTypeController@delete_by_selection");



            //------------------------------- training ----------------------\\
            //----------------------------------------------------------------\\
            Route::resource('trainings', 'TrainingController');
            Route::post("trainings/delete/by_selection", "TrainingController@delete_by_selection");

            Route::resource('trainers', 'TrainersController');
            Route::post("trainers/delete/by_selection", "TrainersController@delete_by_selection");

            Route::resource('training_skills', 'TrainingSkillsController');
            Route::post("training_skills/delete/by_selection", "TrainingSkillsController@delete_by_selection");


            //------------------------------- Apps Management ----------------\\
            //--------------------------------------------------------------------\\

            Route::prefix('hr')->group(function () {


                //------------------------------- office_shift ------------------\\
                //----------------------------------------------------------------\\

                Route::resource('office_shift', 'OfficeShiftController');
                Route::post("office_shift/delete/by_selection", "OfficeShiftController@delete_by_selection");

                //------------------------------- event ---------------------------\\
                //----------------------------------------------------------------\\

                Route::resource('event', 'EventController');
                Route::post("event/delete/by_selection", "EventController@delete_by_selection");

                //------------------------------- holiday ----------------------\\
                //----------------------------------------------------------------\\

                Route::resource('holiday', 'HolidayController');
                Route::post("holiday/delete/by_selection", "HolidayController@delete_by_selection");

                //------------------------------- award ----------------------\\
                //----------------------------------------------------------------\\

                Route::resource('award', 'AwardController');
                Route::post("award/delete/by_selection", "AwardController@delete_by_selection");

                Route::resource('award_type', 'AwardTypeController');
                Route::post("award_type/delete/by_selection", "AwardTypeController@delete_by_selection");


                //------------------------------- complaint ----------------------\\
                //----------------------------------------------------------------\\

                Route::resource('complaint', 'ComplaintController');
                Route::post("complaint/delete/by_selection", "ComplaintController@delete_by_selection");

                //------------------------------- travel ----------------------\\
                //----------------------------------------------------------------\\

                Route::resource('travel', 'TravelController');
                Route::post("travel/delete/by_selection", "TravelController@delete_by_selection");

                Route::resource('arrangement_type', 'ArrangementTypeController');
                Route::post("arrangement_type/delete/by_selection", "ArrangementTypeController@delete_by_selection");

            });
                

                //------------------------------- Clients ----------------------\\
                //----------------------------------------------------------------\\

                Route::resource('clients', 'ClientController');
                Route::post("clients/delete/by_selection", "ClientController@delete_by_selection");

                //------------------------------- Sessions Client ----------------------\\
                //----------------------------------------------------------------\\

                Route::get("client_projects", "ClientController@client_projects_index");
                Route::get("client_projects/create", "ClientController@client_projects_create");
                Route::post("client_projects", "ClientController@client_projects_store");

                Route::get("client_tasks", "ClientController@client_tasks_index");
                Route::get("client_tasks/create", "ClientController@client_tasks_create");
                Route::post("client_tasks", "ClientController@client_tasks_store");

                Route::put('client_profile/{id}', 'ProfileController@Update_client_profile');
                Route::get('client_profile', 'ProfileController@get_client_profile')->name('client_profile');

                //------------------------------- Sessions Employee ----------------------\\
                //----------------------------------------------------------------\\

                Route::put('employee_profile/{id}', 'EmployeeSessionController@Update_employee_profile');
                Route::get('employee_profile', 'EmployeeSessionController@get_employee_profile')->name('employee_profile');

                Route::get('employee/overview', 'EmployeeSessionController@employee_details')->name('employee_details');
                Route::put('session_employee/basic/info/{id}', 'EmployeeSessionController@update_basic_info');
                Route::put('session_employee/social/{id}', 'EmployeeSessionController@update_social_profile');
                Route::put("session_employee/document/{id}", "EmployeeSessionController@update_employee_document");

                Route::post("session_employee/storeExperiance", "EmployeeSessionController@storeExperiance");
                Route::put("session_employee/updateExperiance/{id}", "EmployeeSessionController@updateExperiance");
                Route::delete("session_employee/destroyExperiance/{id}", "EmployeeSessionController@destroyExperiance");


                Route::put('session_employee/update_task_status/{id}', 'EmployeeSessionController@update_task_status');

                Route::post("session_employee/storeAccount", "EmployeeSessionController@storeAccount");
                Route::put("session_employee/updateAccount/{id}", "EmployeeSessionController@updateAccount");
                Route::delete("session_employee/destroyAccount/{id}", "EmployeeSessionController@destroyAccount");

                Route::get("session_employee/Get_leave_types", "EmployeeSessionController@Get_leave_types");
                Route::post("session_employee/requestleave", "EmployeeSessionController@Request_leave");


                //------------------------------- users --------------------------\\
                //----------------------------------------------------------------\\
                Route::resource('users', 'UserController');
                Route::post('assignRole', 'UserController@assignRole');

                Route::get('getAllPermissions', 'UserController@getAllPermissions');

            

            //------------------------------- Settings --------------------------\\
            //----------------------------------------------------------------\\

            Route::prefix('settings')->group(function () {
                Route::resource('system_settings', 'SettingController');
                Route::resource('update_settings', 'UpdateController');
                Route::resource('email_settings', 'EmailSettingController');
                Route::resource('permissions', 'PermissionsController');
                Route::resource('currency', 'CurrencyController');
                Route::resource('backup', 'BackupController');

                Route::post("currency/delete/by_selection", "CurrencyController@delete_by_selection");

            });

                //------------------------------- Module Settings ------------------------\\

                Route::resource('module_settings', 'ModuleSettingController');
                Route::post('update_status_module', 'ModuleSettingController@update_status_module')->name('update_status_module');
                Route::post('upload_module', 'ModuleSettingController@upload_module')->name('upload_module');
                Route::get('update_database_module/{module_name}', 'ModuleSettingController@update_database_module')->name('update_database_module');

                Route::get('GenerateBackup', 'BackupController@GenerateBackup');


            //------------------------------- Reports --------------------------\\
            //----------------------------------------------------------------\\

            Route::prefix('report')->group(function () {
                Route::get('attendance', 'ReportController@attendance_report_index')->name('attendance_report_index');
                Route::get('employee', 'ReportController@employee_report_index')->name('employee_report_index');
                Route::get('project', 'ReportController@project_report_index')->name('project_report_index');
                Route::get('task', 'ReportController@task_report_index')->name('task_report_index');
                Route::get('expense', 'ReportController@expense_report_index')->name('expense_report_index');
                Route::get('deposit', 'ReportController@deposit_report_index')->name('deposit_report_index');

                Route::POST('fetchDepartment', 'ReportController@fetchDepartment')->name('fetchDepartment');
                Route::POST('fetchDesignation', 'ReportController@fetchDesignation')->name('fetchDesignation');

            });

            //------------------------------- Profile --------------------------\\
            //----------------------------------------------------------------\\
            Route::put('updateProfile/{id}', 'ProfileController@updateProfile');
            Route::resource('profile', 'ProfileController');

            //------------------------------- clear_cache --------------------------\\

            Route::get("clear_cache", "SettingController@Clear_Cache");

        });
   

    });

} else {
    
        Route::get('/{vue?}',
        function () {
                return redirect('/setup');
        })->where('vue', '^(?!setup).*$');

    
    Route::get('/setup', [
        'uses' => 'SetupController@viewCheck',
    ])->name('setup');

    Route::get('/setup/step-1', [
        'uses' => 'SetupController@viewStep1',
    ]);

    Route::post('/setup/step-2', [
        'as' => 'setupStep1', 'uses' => 'SetupController@setupStep1',
    ]);

    Route::post('/setup/testDB', [
        'as' => 'testDB', 'uses' => 'TestDbController@testDB',
    ]);

    Route::get('/setup/step-2', [
        'uses' => 'SetupController@viewStep2',
    ]);

    Route::get('/setup/step-3', [
        'uses' => 'SetupController@viewStep3',
    ]);

    Route::get('/setup/finish', function () {

        return view('setup.finishedSetup');
    });

    Route::get('/setup/getNewAppKey', [
        'as' => 'getNewAppKey', 'uses' => 'SetupController@getNewAppKey',
    ]);

    Route::post('/setup/step-3', [
        'as' => 'setupStep2', 'uses' => 'SetupController@setupStep2',
    ]);

    Route::post('/setup/step-4', [
        'as' => 'setupStep3', 'uses' => 'SetupController@setupStep3',
    ]);

    Route::post('/setup/step-5', [
        'as' => 'setupStep4', 'uses' => 'SetupController@setupStep4',
    ]);

    Route::post('/setup/lastStep', [
        'as' => 'lastStep', 'uses' => 'SetupController@lastStep',
    ]);

    Route::get('setup/lastStep', function () {
        return redirect('/setup', 301);
    });

} 
    



