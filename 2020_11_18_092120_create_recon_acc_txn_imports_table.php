<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReconAccTxnImportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        #"LENDERSTMT-UGA-UEZM-UFLW-UEZM-1234567890.xls"
        
        Schema::create('account_txn_imports', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code', 4);
	        $table->string('data_prvdr_code', 4); #dp_code
	        $table->string('lender_code', 4);
	        $table->string('acc_prvdr_code', 4);
	        $table->string('cust_id', 20);
	        $table->string('data_prvdr_cust_id', 20);
	        $table->unsignedInteger('account_id')->nullable();
	        $table->string('account_num', 32); #ac_num
	        $table->string('account_name', 32)->nullable();
	        $table->string('s3_bucket_name', 16); 
	        $table->string('s3_key', 64); 
	        $table->string('import_status', 12); 
	        
        });
    }

     
     
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_txn_imports');
    }
}
