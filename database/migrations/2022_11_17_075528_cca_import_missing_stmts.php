<?php

use App\Scripts\php\UGARecon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class CcaImportMissingStmts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Log::warning("CCA Importing Missing Statements.....");
        
        $process = new Process(["python3", app_path()."/Scripts/python/vendors/stmts/raw_CCA_stmt_import.py"]);
		$process->setTimeout(500);
		$process->run();
        $output = $process->getOutput();
		Log::warning($process->getErrorOutput());
		Log::warning($output);
        $response = json_decode($output,true);

        Log::warning($response);

        UGARecon::run([["acc_prvdr_code" => 'CCA', 'id' => 1783]]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
