<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use DataTables;
use DB;

class BackupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
      //-------------------- Backup Databse -------------\\

      public function index(Request $request)
      {
  
          $user_auth = auth()->user();
          if ($user_auth->can('backup')){
            if ($request->ajax()) {
              $backups = [];
              $id = 0;
              foreach (glob(storage_path() . '/app/public/backup/*') as $filename) {


                  $item['id'] = $id += 1;
                  $item['date'] = basename($filename);
                  $size = $this->formatSizeUnits(filesize($filename));
                  $item['size'] = $size;
  
                  $backups[] = $item;
              }

                return Datatables::of($backups)
                ->addIndexColumn()
                ->make(true);
            }
  
              return view('settings.backup.backup_list');
          }
          return abort('403', __('You are not authorized'));
  
      }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
   

     //-------------------- Fomrmat units -------------\\

    public function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

  

    //-------------------- Generate Databse -------------\\

    public function GenerateBackup(Request $request)
    {

        $user_auth = auth()->user();
            if ($user_auth->can('backup')){

            Artisan::call('database:backup');

            return response()->json(['success' => true]);
        }
        return abort('403', __('You are not authorized'));
    }

    //-------------------- Delete Databse -------------\\

    public function destroy(Request $request, $name)
    {

        $user_auth = auth()->user();
        if ($user_auth->can('backup')){

        foreach (glob(storage_path() . '/app/public/backup/*') as $filename) {
                $path = storage_path() . '/app/public/backup/' . basename($name);
                if (file_exists($path)) {
                    @unlink($path);
                }
            }
        }
        return abort('403', __('You are not authorized'));
    }

}
