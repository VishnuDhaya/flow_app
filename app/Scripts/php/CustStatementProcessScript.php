<?php

namespace App\Scripts\php;

use App\Consts;
use App\Services\Mobile\RMService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CustStatementProcessScript{

    public function delete_stmt_folder($country_code, $acc_prvdr_code, $acc_number) {
        
        $rel_path = "files/$country_code/statements/$acc_prvdr_code/$acc_number/";
        $abs_path = flow_storage_path($rel_path);
        File::deleteDirectory($abs_path);
    }

    public function rename_stmt_folder($folder_path, $flow_req_id) {
        
        $from_folder = flow_storage_path($folder_path);
        $folder_path = rtrim($folder_path, basename($folder_path));
        $to_folder = flow_storage_path($folder_path.$flow_req_id);
        File::moveDirectory($from_folder, $to_folder);
    }

    public function handle_file_upload_n_lambda_invoke($req_data, $folder_path, $file_json, $zip_file_path) {

        try {
            $rm_serv = new RMService;
            $acc_number = $req_data['account_num'];
            $acc_prvdr_code = $req_data['acc_prvdr_code'];
            
            $result = $rm_serv->check_uploaded_files($file_json, $acc_prvdr_code);
            if (!empty($result['files_not_uploaded'])) {
                thrw("Please upload the required files: $acc_number");
            }
            create_zip_file($zip_file_path, $result['uploaded_file_paths']);       
            $flow_req_id = $rm_serv->upload_to_s3_and_invoke_lambda($req_data, $zip_file_path, $file_json);
        }
        finally {
            if(File::exists($zip_file_path)) {
                File::delete($zip_file_path);
            }
            // if (isset($flow_req_id)) {
            //     $this->rename_stmt_folder($folder_path, $flow_req_id);
            // }
        }
    }

    public function store_file_info_in_file_json($file, $rel_file_path, $file_json) {
        
        $file_name=$file->getFileName();
        $file_type=$file->getExtension();
        foreach($file_json['files'] as $index => $file) {
            if(str_contains($file_name, $file['file_of'])) {
                $file_json['files'][$index]['file_name'] = $file_name;
                $file_json['files'][$index]['file_type'] = "application/$file_type";
                $file_json['files'][$index]['file_path'] = $rel_file_path;
            }
        }
        return $file_json;
    }

    public function handle_stmt_upload_process($rel_ap_path) {
        
        $acc_prvdr_code = basename($rel_ap_path);
        $abs_ap_path = flow_storage_path($rel_ap_path);
        $stmt_folders = File::directories($abs_ap_path);
        foreach ($stmt_folders as $stmt_folder) {
            $acc_number = trim(basename($stmt_folder));
            $stmts = File::files($stmt_folder);
            
            $rm_serv = new RMService();
            $file_json = $rm_serv->get_file_upload_tmplt($acc_prvdr_code);
            $file_json = json_decode($file_json, true);

            $stmt_folder_rel_path = "$rel_ap_path/$acc_number";
            foreach($stmts as $stmt)
            {
                $file_json = $this->store_file_info_in_file_json($stmt, "$stmt_folder_rel_path/", $file_json);
            }
            $zip_file_path = flow_storage_path("$stmt_folder_rel_path/stmts.zip");
            $req_data = [
                'id' => null,
                'account_num' => $acc_number,
                'acc_prvdr_code' => $acc_prvdr_code
            ];
            $this->handle_file_upload_n_lambda_invoke($req_data, $stmt_folder_rel_path, $file_json, $zip_file_path);
        }   
    }

    public function main() {
        $country_codes = ['UGA', 'RWA'];

        foreach($country_codes as $country_code) {
            set_app_session($country_code);
            $rel_statement_path = "files/$country_code/statements";
            $abs_statement_path = flow_storage_path($rel_statement_path);
            $ap_paths = File::directories($abs_statement_path);
            foreach($ap_paths as $ap_path) {
                $acc_prvdr_code = basename($ap_path);
                $this->handle_stmt_upload_process("$rel_statement_path/$acc_prvdr_code");
            }
        }
    }
}