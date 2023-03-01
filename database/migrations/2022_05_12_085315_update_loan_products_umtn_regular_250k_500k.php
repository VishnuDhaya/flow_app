<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLoanProductsUmtnRegular250k500k extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $new_model = 'fa_performance_model';
        set_app_session('UGA');
        $product_ids = DB::select("SELECT id FROM loan_products WHERE country_code = 'UGA' AND acc_prvdr_code = 'UMTN' AND product_type = 'regular' AND max_loan_amount IN (250000, 500000)");
        foreach($product_ids as $product_id){
            DB::update("UPDATE loan_products SET cs_model_code = '$new_model' WHERE id = $product_id->id");
        }
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
