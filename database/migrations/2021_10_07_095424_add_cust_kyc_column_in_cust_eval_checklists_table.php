<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustKycColumnInCustEvalChecklistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cust_eval_checklists', function (Blueprint $table) {
            $table->json('cust_kyc_data')->after('checklist_json');
            $table->dropColumn('data_prvdr_code');
            $table->dropColumn('data_prvdr_cust_id');
            $table->dropColumn('biz_name');
            $table->dropColumn('wallet_balance');
            $table->dropColumn('wallet_bal_time');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cust_eval_checklists', function (Blueprint $table) {
            //
        });
    }
}
