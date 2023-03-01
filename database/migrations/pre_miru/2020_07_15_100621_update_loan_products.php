<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateLoanProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update("UPDATE loan_products SET product_code='EMTop up 4',product_name ='EMTop up 4' WHERE id=17");
        DB::update("UPDATE loan_products SET product_code='EMTop up 3',product_name = 'EMTop up 3' WHERE id=16");
        DB::update("UPDATE loan_products SET product_code='EMTop up 1',product_name = 'EMTop up 1' WHERE id=15");
        DB::update("UPDATE loan_products SET product_code='EM18',product_name ='EM18' WHERE id=23");
        DB::update("UPDATE loan_products SET product_code='EM19',product_name ='EM19' WHERE id=24");
        DB::update("UPDATE loan_products SET product_code='EM20',product_name ='EM20' WHERE id=25");
        DB::update("UPDATE loan_products SET product_code='EM21',product_name ='EM21' WHERE id=27");
        DB::update("UPDATE loan_products SET product_code='EM22',product_name ='EM22' WHERE id=28");
        DB::update("UPDATE loan_products SET product_code='EM23',product_name ='EM23' WHERE id=29");
        DB::update("UPDATE loan_products SET product_code='EM24',product_name ='EM24' WHERE id=30");
        DB::update("UPDATE loan_products SET product_code='EM25',product_name ='EM25' WHERE id=31");
        DB::update("UPDATE loan_products SET product_code='EM26',product_name ='EM26' WHERE id=34");
        DB::update("UPDATE loan_products SET product_code='EM27',product_name ='EM27' WHERE id=35");
        DB::update("UPDATE loan_products SET product_code='EM28',product_name ='EM28' WHERE id=36");
        DB::update("UPDATE loan_products SET product_code='EM29',product_name ='EM29' WHERE id=37");
        DB::update("UPDATE loan_products SET product_code='EM30',product_name ='EM30' WHERE id=38");
        DB::update("UPDATE loan_products SET product_code='EM31',product_name ='EM31' WHERE id=39");
        DB::update("UPDATE loan_products SET product_code='EM32',product_name ='EM32' WHERE id=40");
        DB::update("UPDATE loan_products SET product_code='EM33',product_name ='EM33' WHERE id=41");
        DB::update("UPDATE loan_products SET product_code='EM34',product_name ='EM34' WHERE id=42");

        DB::table('loan_products')->insert([['country_code'=> 'UGA','product_name' => 'EMInt','product_code' => 'EMInt','data_prvdr_code' => 'UEZM','lender_code' => 'UFLW','cs_model_code' => 'default_model','product_type' => 'regular','flow_fee_type' => 'Flat','flow_fee' => '0','flow_fee_duration' => '30','duration'=> '30','max_loan_amount'=> '2000000','status'=> 'disabled','created_by'=> 2]]);
        
        DB::table('loan_products')->insert([['country_code'=> 'UGA','product_name' => 'EMTop up 2','product_code' => 'EMTop up 2','data_prvdr_code' => 'UEZM','lender_code' => 'UFLW','cs_model_code' => 'default_model','product_type' => 'regular','flow_fee_type' => 'Flat','flow_fee' => '7500','flow_fee_duration' => '1','duration'=> '1','max_loan_amount'=> '750000','status'=> 'disabled','created_by'=> 2]]);

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
