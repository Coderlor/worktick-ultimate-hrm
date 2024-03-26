<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Currency;
use File;
use Illuminate\Support\Facades\Artisan;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_auth = auth()->user();
		if ($user_auth->can('settings')){
            
            $setting_data = Setting::where('deleted_at', '=', null)->first();

            $email_settings['mailer'] = env('MAIL_MAILER');
            $email_settings['host'] = env('MAIL_HOST');
            $email_settings['port'] = env('MAIL_PORT');
            $email_settings['username'] = env('MAIL_USERNAME');
            $email_settings['password'] = env('MAIL_PASSWORD');
            $email_settings['encryption'] = env('MAIL_ENCRYPTION');
            $email_settings['from_email'] = env('MAIL_FROM_ADDRESS');
            $email_settings['from_name'] = env('MAIL_FROM_NAME');

            $currencies = Currency::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','name']);
            $setting['id'] = $setting_data->id;
            $setting['email'] = $setting_data->email;
            $setting['CompanyName'] = $setting_data->CompanyName;
            $setting['CompanyPhone'] = $setting_data->CompanyPhone;
            $setting['CompanyAdress'] = $setting_data->CompanyAdress;
            $setting['logo'] = "";
            $setting['footer'] = $setting_data->footer;
            $setting['developed_by'] = $setting_data->developed_by;
            $setting['currency_id'] = $setting_data->currency_id;
            $setting['default_language'] = $setting_data->default_language;
            $setting['timezone'] = env('APP_TIMEZONE') == null?'UTC':env('APP_TIMEZONE');

            $zones_array = array();
            $timestamp = time();
            foreach(timezone_identifiers_list() as $key => $zone){
                date_default_timezone_set($zone);
                $zones_array[$key]['zone'] = $zone;
                $zones_array[$key]['diff_from_GMT'] = 'UTC/GMT ' . date('P', $timestamp);
                $zones_array[$key]['label'] = $zones_array[$key]['diff_from_GMT'] . ' - ' . $zones_array[$key]['zone'];
            }

            return view('settings.system_settings.system_settings_list', compact('setting','email_settings','currencies','zones_array'));

        }
        return abort('403', __('You are not authorized'));
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
        $user_auth = auth()->user();
		if ($user_auth->can('settings')){

            $request->validate([
                'CompanyName'      => 'required|string|max:255',
                'CompanyPhone'     => 'nullable|numeric',
                'email'            => 'required|string|email|max:255',
                'CompanyAdress'    => 'required|string',
                'developed_by'     => 'required|string|max:255',
                'footer'           => 'required|string|max:255',
                'logo'             => 'nullable|image|mimes:jpeg,png,jpg,bmp,gif,svg|max:2048',
                'currency_id'      => 'required',
                'timezone'         => 'required',
            ]);
            
            $setting = Setting::findOrFail($id);
            $currentAvatar = $setting->logo;

            if ($request->logo != null) {
                if ($request->logo != $currentAvatar) {

                    $image = $request->file('logo');
                    $filename = time().'.'.$image->extension();  
                    $image->move(public_path('/assets/images'), $filename);
                    $path = public_path() . '/assets/images';

                    $userPhoto = $path . '/' . $currentAvatar;
                    if (file_exists($userPhoto)) {
                        if ($setting->logo != 'logo-default.png') {
                            @unlink($userPhoto);
                        }
                    }
                } else {
                    $filename = $currentAvatar;
                }

            }else{
                $filename = $currentAvatar;
            }




            if ($request['currency_id'] != 'null') {
                $currency = $request['currency_id'];
            } else {
                $currency = null;
            }

            if ($request['default_language'] != 'null') {
                $default_language = $request['default_language'];
            } else {
                $default_language = 'en';
            }

            Setting::whereId($id)->update([
                'currency_id' => $currency,
                'email' => $request['email'],
                'CompanyName' => $request['CompanyName'],
                'CompanyPhone' => $request['CompanyPhone'],
                'CompanyAdress' => $request['CompanyAdress'],
                'footer' => $request['footer'],
                'developed_by' => $request['developed_by'],
                'logo' => $filename,
            ]);

            $this->setEnvironmentValue([
                'APP_TIMEZONE' => $request['timezone'] !== null?'"' . $request['timezone'] . '"':'"UTC"',
            ]);

            Artisan::call('config:cache');
            Artisan::call('config:clear');

            return response()->json(['success' => true]);

        }
        return abort('403', __('You are not authorized'));
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


    
    //-------------- Clear_Cache ---------------\\

    public function Clear_Cache(Request $request)
    {
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
    }

       //-------------- Set Environment Value ---------------\\

       public function setEnvironmentValue(array $values)
       {
           $envFile = app()->environmentFilePath();
           $str = file_get_contents($envFile);
           $str .= "\r\n";
           if (count($values) > 0) {
               foreach ($values as $envKey => $envValue) {
       
                   $keyPosition = strpos($str, "$envKey=");
                   $endOfLinePosition = strpos($str, "\n", $keyPosition);
                   $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);
       
                   if (is_bool($keyPosition) && $keyPosition === false) {
                       // variable doesnot exist
                       $str .= "$envKey=$envValue";
                       $str .= "\r\n";
                   } else {
                       // variable exist                    
                       $str = str_replace($oldLine, "$envKey=$envValue", $str);
                   }            
               }
           }
       
           $str = substr($str, 0, -1);
           if (!file_put_contents($envFile, $str)) {
               return false;
           }
       
           app()->loadEnvironmentFrom($envFile);    
       
           return true;
       }
}
