<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AtlerRekycBorrowerBackupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('rekyc_borrower_backups','kyc_records');
        Schema::table('kyc_records', function (Blueprint $table) {
            $table->json('cust_json_now')->nullable()->after('rekyc_lead_id');
            $table->renameColumn('backup_json','cust_json_before');
            $table->renameColumn('rekyc_lead_id','lead_id');
        });
        Schema::table('kyc_records', function (Blueprint $table) {
            $table->json('cust_json_before')->nullable()->change();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kyc_records', function (Blueprint $table) {
            $table->dropColumn('cust_json_now');
            $table->renameColumn('cust_json_before','backup_json');
            $table->renameColumn('lead_id','rekyc_lead_id');
        });
        Schema::rename('kyc_records','rekyc_borrower_backups');
    }
}
