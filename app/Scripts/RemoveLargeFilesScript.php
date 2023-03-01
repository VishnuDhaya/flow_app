<?php
namespace App\Scripts\php;
use File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class RemoveLargeFilesScript{

    public static function remove(){
       
        // $paths = env('FLOW_STORAGE_PATH').'/files';
        ini_set('memory_limit', '2G');
        $paths = env('FLOW_STORAGE_PATH').'/files/RWA';
        
        $paths = File::allFiles($paths);
        foreach($paths as $key => $path){
            $files = pathinfo($path);
            $file_name = $files['filename'];
            $file_type = $files['extension'];
            // if($file_type == 'jpeg' && !Str::contains($file_name,'_' )){
            //     #File::delete($files['dirname'].DIRECTORY_SEPARATOR.$files['basename']);
            //     Log::warning($files['basename']);
            // }
            if($file_type == 'jpeg' && !Str::contains($file_name,'_' ) && Str::contains($path, 'photo')){
                File::delete($files['dirname'].DIRECTORY_SEPARATOR.$files['basename']);
                Log::warning($files['basename'].' - '.$path);
            }
          }

    }



}

