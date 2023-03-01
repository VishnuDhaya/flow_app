<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NewAccountsYoUganda extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       /*Schema::table('accounts', function (Blueprint $table) {
            $table->string('lender_data_prvdr_code_csv',40)->nullable()->after('lender_data_prvdr_code_csv');
        });*/
          DB::table('accounts')->insert([
	        ['country_code' => 'UGA', 'cust_id' => NULL, 'lender_code' => 'UFLW', 'lender_data_prvdr_code' => 'UEZM',  'data_prvdr_code' => null,'acc_prvdr_name' =>'Yo Uganda' ,'acc_prvdr_code'=>'UYOU','acc_purpose'=>'repayment','type'=>'payment_gateway','balance'=>0,'holder_name'=>'Flow','acc_number' =>'100272380571','branch'=>NULL,'is_primary_acc'=>false,'status' =>  'enabled', 'created_by'=>2],

	   ]);
   
	   DB::update("update accounts set acc_prvdr_code = 'UMOM', acc_prvdr_name = 'MOMO - MTN UGANDA' where id = 1688");
       DB::update("update accounts set api_cred = '{\"username\" : \"100272380571\", \"password\": \"xkm5-ibmw-yaOq-WnZS-l7FK-K2cG-X2Kv-z4Iq\"}' where acc_prvdr_code = 'UYOU' and acc_prvdr_name = 'Yo Uganda'");
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
