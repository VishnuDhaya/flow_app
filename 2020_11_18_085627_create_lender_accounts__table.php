<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLenderAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lender_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code',4);
	        $table->string('data_prvdr_code',4); #dp_code
	        #$table->string('lender_code',4);
	        #$table->string('acc_prvdr_code',4); #ac_provider_code
	        $table->unsignedInteger('account_id')->nullable();
	        $table->string('account_num',32); #ac_num
	        $table->string('account_name',32)->nullable();
	        $table->unsignedInteger('ref_account_id')->nullable();
	        $table->string('ref_account_num',128)->nullable();
	        $table->dateTime('txn_date');
	        $table->string('txn_type',32);
	        $table->string('descr',128)->nullable();
	        $table->double('dr_amt',15, 2)->default(0);
	        $table->double('cr_amt',15, 2)->default(0);
	        $table->double('balance',15, 2)->default(0);
	        $table->string('txn_id',64);
	        $table->string('terminal',50)->nullable();
	        $table->boolean('is_reversal')->default(false);
	        $table->string('descr_contains',256)->nullable();
	        $table->string('recon_id', 16)->nullable(true);
	        $table->boolean('import_id'); # run_id

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lender_accounts');
    }
}
