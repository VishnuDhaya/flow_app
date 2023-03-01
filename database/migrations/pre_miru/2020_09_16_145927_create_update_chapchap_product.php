<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUpdateChapchapProduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update("UPDATE loan_products SET product_code='FC1',product_name ='FC1' WHERE id=44");
        DB::update("UPDATE loan_products SET product_code='FC2',product_name = 'FC2' WHERE id=45");
        DB::update("UPDATE loan_products SET product_code='FC3',product_name = 'FC3' WHERE id=46");
        DB::update("UPDATE loan_products SET product_code='FC4',product_name ='FC4' WHERE id=47");
        DB::update("UPDATE loan_products SET product_code='FC5',product_name ='FC5' WHERE id=48");
        DB::update("UPDATE loan_products SET product_code='FC6',product_name ='FC6' WHERE id=49");

        DB::table('loan_products')->insert([['country_code'=> 'UGA','product_name' => 'FC7','product_code' => 'FC7','data_prvdr_code' => 'CCA','lender_code' => 'UFLW','cs_model_code' => 'default_model','product_type' => 'regular','flow_fee_type' => 'Flat','flow_fee' => '32000','flow_fee_duration' => '6','duration'=> '30','max_loan_amount'=> '1500000','status'=> 'enabled','created_by'=> 2]]);
        DB::table('loan_products')->insert([['country_code'=> 'UGA','product_name' => 'FC8','product_code' => 'FC8','data_prvdr_code' => 'CCA','lender_code' => 'UFLW','cs_model_code' => 'default_model','product_type' => 'regular','flow_fee_type' => 'Flat','flow_fee' => '38000','flow_fee_duration' => '6','duration'=> '30','max_loan_amount'=> '2000000','status'=> 'enabled','created_by'=> 2]]);
        DB::table('loan_products')->insert([['country_code'=> 'UGA','product_name' => 'FC9','product_code' => 'FC9','data_prvdr_code' => 'CCA','lender_code' => 'UFLW','cs_model_code' => 'default_model','product_type' => 'regular','flow_fee_type' => 'Flat','flow_fee' => '55000','flow_fee_duration' => '7','duration'=> '30','max_loan_amount'=> '3000000','status'=> 'enabled','created_by'=> 2]]);

        DB::table('accounts')->insert([['country_code' => 'UGA', 'cust_id' => NULL, 'lender_code' => 'UFLW', 'lender_data_prvdr_code' => 'CCA',  'data_prvdr_code' => null,'acc_prvdr_name' =>'CHAP CHAP' ,'acc_prvdr_code'=>'CCA','acc_purpose'=>'disbursement','type'=>'wallet','balance'=>0,'holder_name'=>'Flow','acc_number' =>'0703463210','branch'=>NULL,'is_primary_acc'=>false,'status' =>  'enabled', 'created_by'=>2]]);
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
