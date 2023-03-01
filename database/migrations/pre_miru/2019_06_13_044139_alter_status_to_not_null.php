<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterStatusToNotNull extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('countries', function (Blueprint $table) {   
           $table->dropColumn('status');     
        });

          
        DB::update("update persons set status='enabled' where status is NULL");  
       

        Schema::table('countries', function (Blueprint $table) {   
           $table->string('status',20)->nullable(false)->default('disabled');    
        });


        $tables = DB::select("select table_name, column_name from information_schema.columns where table_schema = 'flow_api_test' and column_name like '%status%'");

        foreach($tables as $table){
            Schema::table($table->TABLE_NAME, function (Blueprint $table) { 
                $table->string('status',20)->nullable(false)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
