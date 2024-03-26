<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use \Nwidart\Modules\Facades\Module;
use Illuminate\Support\Facades\Config;
use Macellan\Zip\Zip;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ModuleSettingController extends Controller
{

        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_auth = auth()->user();
		if ($user_auth->can('module_settings')){
            $allModules = Module::all();
            $ModulesInstalled = [];
            foreach($allModules as $key => $module_name){
                $item['module_name'] = $key;
                $item['current_version'] = $this->getCurrentVersion($key);
                $item['status'] = \Module::collections()->has($key);
                $ModulesInstalled[] = $item;

            }

            return view('settings.module_settings.module', compact('ModulesInstalled'));
        }
        return abort('403', __('You are not authorized'));
    }

     /*
    * Return current version (as plain text).
    */
    public function getCurrentVersion($module_name){
        $user_auth = auth()->user();
		if ($user_auth->can('module_settings')){
            $version = File::get(base_path()."/Modules/$module_name/version.txt");
            return $version;
        }
    }



    public function update_status_module(Request $request)
    {
        $user_auth = auth()->user();
		if ($user_auth->can('module_settings')){
            $module = Module::find($request->name);
            ($request->status == '1') ? $module->enable() : $module->disable();

            return response()->json(['success' => true]);
        }
    }

    public function update_database_module(Request $request , $module_name)
    {
        $user_auth = auth()->user();
		if ($user_auth->can('module_settings')){
            try {
                $module = Module::find($module_name);
                if($module){
                    Artisan::call('migrate', ['--force' => true, '--path' => 'Modules/'. $module_name .'/database/Migrations']);
                    Artisan::call('optimize:clear');

                    return response()->json(['success' => true , 'message' => 'Database Updated !!'] , 200);

                }else{
                    return response()->json(['success' => false , 'message' => 'Module Name Not exist!!'], 400);

                }

            } catch (Exception $e) {
                return response()->json(['message' => $e->getMessage()], 500);
            }
        }
        return abort('403', __('You are not authorized'));
    }


    public function upload_module(Request $request)
    {
        $user_auth = auth()->user();
		if ($user_auth->can('module_settings')){

            ini_set('max_execution_time', 600); //600 seconds = 10 minutes 
            
            try {

                $zip_path = $request->module_zip;
                $OriginalName = $zip_path->getClientOriginalName();
                $zip = Zip::open($zip_path);
                    
                if(str_contains($OriginalName , 'codecanyon-')){
                    $zip_name = $this->Unzip($zip);
                }else{
                    $zip_name = $zip_path->getClientOriginalName();
                    $zip->extract(storage_path() . '/app/public/Modules');
                }
                    
                $module_name = str_replace('.zip', '', $zip_name);
                
                File::moveDirectory(storage_path() . '/app/public/Modules/' . $module_name, base_path() . '/Modules/' . $module_name, true);
                File::deleteDirectory(storage_path() . '/app/public/Modules');
            
                $module = Module::find($module_name);
                $module->enable();

                Artisan::call('migrate', ['--force' => true, '--path' => 'Modules/'. $module_name .'/database/Migrations']);
                
                $role = Role::findOrFail(1);

                $permissions = array(
                    0 => 'products_view',
                    1 => 'products_add',
                    2 => 'products_edit',
                    3 => 'products_delete',
                    4 => 'barcode_view',
                    5 => 'category',
                    6 => 'brand',
                    7 => 'unit',
                    8 => 'warehouse',
                    9 => 'adjustment_view',
                    10 => 'adjustment_add',
                    11 => 'adjustment_edit',
                    12 => 'adjustment_delete',
                    13 => 'transfer_view',
                    14 => 'transfer_add',
                    15 => 'transfer_edit',
                    16 => 'transfer_delete',
                    17 => 'sales_view',
                    18 => 'sales_add',
                    19 => 'sales_edit',
                    20 => 'sales_delete',
                    21 => 'shipment',
                    22 => 'purchases_view',
                    23 => 'purchases_add',
                    24 => 'purchases_edit',
                    25=> 'purchases_delete',
                    26 => 'quotations_view',
                    27 => 'quotations_add',
                    28 => 'quotations_edit',
                    29 => 'quotations_delete',
                    30 => 'sale_returns_view',
                    31 => 'sale_returns_add',
                    32 => 'sale_returns_edit',
                    33 => 'sale_returns_delete',
                    34 => 'purchase_returns_view',
                    35 => 'purchase_returns_add',
                    36 => 'purchase_returns_edit',
                    37 => 'purchase_returns_delete',
                    38 => 'payment_sales_view',
                    39 => 'payment_sales_add',
                    40 => 'payment_sales_edit',
                    41 => 'payment_sales_delete',
                    42 => 'payment_purchases_view',
                    43 => 'payment_purchases_add',
                    44 => 'payment_purchases_edit',
                    45 => 'payment_purchases_delete',
                    46 => 'payment_returns_view',
                    47 => 'payment_returns_add',
                    48 => 'payment_returns_edit',
                    49 => 'payment_returns_delete',
                    50 => 'suppliers_view',
                    51 => 'suppliers_add',
                    52 => 'suppliers_edit',
                    53 => 'suppliers_delete',
                    54 => 'sale_reports',
                    55 => 'purchase_reports',
                    56 => 'payment_sale_reports',
                    57 => 'payment_purchase_reports',
                    58 => 'payment_return_reports',
                    59 => 'top_products_report',
                );

                foreach ($permissions as $permission_name) {

                    $perm = Permission::firstOrCreate(['name' => $permission_name]);
                    $role->givePermissionTo($perm);
                }

                Artisan::call('optimize:clear');

            } catch (\Exception $e) {

                return $e->getMessage();
                
                return 'Something went wrong';
            }

        }else{
            return abort('403', __('You are not authorized'));
        }

    }

    private function getZipName($zip_path){
       $array = explode('\\', $zip_path);
        return end($array);
    }

    private function Unzip($zip_file){
        $user_auth = auth()->user();
		if ($user_auth->can('module_settings')){
            $Path_Code_Canyon = storage_path() . '/app/public/Modules';
            $zip_file->extract($Path_Code_Canyon);
            $files = File::allfiles($Path_Code_Canyon);

            foreach($files as $file){
                if(strpos($file->getRelativePathname(), '.zip') !== false){
                    $filePath = $file->getRelativePathname();
                    $zip = Zip::open($Path_Code_Canyon . '/' . $filePath);
                    $zip->extract(storage_path() . '/app/public/Modules');
                    $zipName = $this->getZipName($filePath);
                    return $zipName;
                }
            }

            return false;
        }
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
