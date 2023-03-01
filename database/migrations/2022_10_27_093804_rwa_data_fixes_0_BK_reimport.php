<?php

use App\Scripts\php\RWARecon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class RwaDataFixes0BKReimport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::delete("delete from account_stmts where account_id = 4182 and date(stmt_txn_date) <= '2022-10-31' and stmt_txn_id != 'FT22305Q4RKC'");

        $process = new Process(["python3", app_path()."/Scripts/python/vendors/stmts/raw_BK_stmt_import.py"]);
		$process->setTimeout(500);
		$process->run();
        $output = $process->getOutput();
		Log::warning($process->getErrorOutput());
		Log::warning($output);
        $response = json_decode($output,true);

        Log::warning($response);

        RWARecon::run();
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
