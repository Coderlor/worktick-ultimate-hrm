<?php
/*
* @author: Pietro Cinaglia
* 	.website: http://linkedin.com/in/pietrocinaglia
*/
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Artisan;
use Auth;

class UpdateController extends Controller
{   
    
    public function index(){
        $version = $this->check();

        return view('settings.update.update_settings',compact('version'));
    }


    /*
    * Return current version (as plain text).
    */
    public function getCurrentVersion(){
        // todo: env file version
        $version = File::get(base_path().'/version.txt');
        return $version;
    }

    /*
    * Check if a new Update exist.
    */
    public function check()
    {
        $lastVersionInfo = $this->getLastVersion();
        if( version_compare($lastVersionInfo['version'], $this->getCurrentVersion(), ">") )
            return $lastVersionInfo['version'];

        return '';
    }

    private function getLastVersion(){
        $content = file_get_contents('https://update-stocky.ui-lib.com/worktick_version.json');
        $content = json_decode($content, true);
        return $content;
    }


    public function viewStep1(Request $request)
    {
        $user_auth = auth()->user();
        if ($user_auth->role_users_id == 1){
            return view('update.viewStep1');
        }
    }
    
    public function lastStep(Request $request)
    {
        $user_auth = auth()->user();
        if ($user_auth->role_users_id == 1){
            ini_set('max_execution_time', 600); //600 seconds = 10 minutes 

            try {
            
                Artisan::call('config:cache');
                Artisan::call('config:clear');

                Artisan::call('migrate --force');
                
            } catch (\Exception $e) {
                
                return $e->getMessage();
                
                return 'Something went wrong';
            }
            
            return view('update.finishedUpdate');
        }
    }



}