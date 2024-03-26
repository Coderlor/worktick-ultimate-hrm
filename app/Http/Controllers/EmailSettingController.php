<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Artisan;

class EmailSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       //
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
        $user_auth = auth()->user();
		if ($user_auth->can('settings')){

            request()->validate([
                'mailer'   => 'required|string|max:255',
                'from_name'   => 'required|string|max:255',
                'from_email'   => 'required|string|max:255',
                'host'    => 'required|string|max:255',
                'port'    => 'required|string|max:255',
                'username'    => 'required|string|max:255',
                'password'    => 'required|string|max:255',
                'encryption'    => 'required|string|max:255',
            ]);

            $this->setEnvironmentValue([
                'MAIL_MAILER' => $request['mailer'] !== null?'"' . $request['mailer'] . '"':'"' . env('MAIL_MAILER') . '"',
                'MAIL_HOST' => $request['host'] !== null?'"' . $request['host'] . '"':'"' . env('MAIL_HOST') . '"',
                'MAIL_PORT' => $request['port'] !== null? $request['port']:env('MAIL_PORT'),
                'MAIL_USERNAME' => $request['username'] !== null?'"' . $request['username'] . '"':'"' . env('MAIL_USERNAME') . '"',
                'MAIL_PASSWORD' => $request['password'] !== null?'"' . $request['password'] . '"':'"' . env('MAIL_PASSWORD') . '"',
                'MAIL_ENCRYPTION' =>  $request['encryption'] !== null?'"' . $request['encryption'] . '"':'"' . env('MAIL_ENCRYPTION') . '"',
                'MAIL_FROM_ADDRESS' => $request['from_email'] !== null?'"' . $request['from_email'] . '"':'"' . env('MAIL_FROM_ADDRESS') . '"',
                'MAIL_FROM_NAME' => $request['from_name'] !== null?'"' . $request['from_name'] . '"':'"' . env('MAIL_FROM_NAME') . '"',

            ]);

            Artisan::call('config:cache');
            Artisan::call('config:clear');

            return response()->json(['success' => true]);

            

        }
        return abort('403', __('You are not authorized'));
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
        //
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
