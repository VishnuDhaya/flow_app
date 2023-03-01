<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;



class AlterColumnCustIdTimeStampAsUniqueInCallLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $results = DB::select("select count(id), GROUP_CONCAT(id) as id, cust_id, timestamp  from call_logs where timestamp is not null group by cust_id, timestamp having count(*) > 1");
        foreach($results as $result){
            
            $duplicate_id = explode(",", $result->id);
            array_shift($duplicate_id);
            $duplicate_id = implode(',', $duplicate_id);
            DB::delete("Delete from call_logs where id in ($duplicate_id)");
        }

        Schema::table('call_logs', function (Blueprint $table) {
            $table->unique(['cust_id', 'timestamp']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('call_logs', function (Blueprint $table) {
            $table->dropUnique(['cust_id', 'timestamp']);
        });
    }
}
