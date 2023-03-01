<?php

use App\Scripts\php\UGARecon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class UmtnImportMissingStmts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Log::warning("UMTN Importing Missing Statements.....");

        $process = new Process(["python", app_path()."/Scripts/python/vendors/stmts/UMTN_stmt_import_from_xl.py"]);
		$process->setTimeout(3000);
		$process->run();
        $output = $process->getOutput();
		Log::warning($process->getErrorOutput());
		Log::warning($output);
        $response = json_decode($output,true);

        Log::warning($response);

        // $data = [["acc_prvdr_code" => 'UMTN', "id" => 3728], ["acc_prvdr_code" => "UMTN", "id" => 3605], ["acc_prvdr_code" => 'UMTN', "id" => 4094]];
        // UGARecon::run($data);
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
