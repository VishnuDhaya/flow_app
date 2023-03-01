<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMasterDataSkipTxnIdCheck extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data_keys')->insert(['country_code' => '*', 'data_key' => 'skip_txn_id_check', 'parent_data_key' => "", 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);
        
        DB::table('master_data')->insert(
           ['country_code'=> '*', 'data_key' => 'skip_txn_id_check', 'data_code' => 'statement_import_failed' , 'data_value' => 'Statement Import Failed' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
           );

           Schema::table('loan_txns', function (Blueprint $table) {
            $table->string('reason_for_skip', 64)->nullable()->after('reversed_date');
        });
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
