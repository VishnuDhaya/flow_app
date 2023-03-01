<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class TFProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $created_at = datetime_db();

        DB::update("update loan_products set status = 'disabled' where product_code in ('TFP1', 'TFP2')");

        DB::table('loan_products')->insert([
            'country_code' => 'UGA', 'product_name'=> 'TF 800K', 'product_code' => 'TFP', 'acc_prvdr_code' => 'UEZM', 'lender_code' => 'UFLW', 'cs_model_code' => 'tf_products', 'loan_purpose' => 'terminal_financing','max_loan_amount'=> 800000,'product_json' => json_encode([ ['duration_type'=>'months','duration' => 12,'purchase_cost'=> 1300000, 'flow_fee'=> 60,'flow_fee_type'=> 'percent','repay_type' => 'daily','daily_deductions'=> 3556], ['duration_type'=>'months','duration' => 15,'purchase_cost'=> 1300000,'flow_fee'=> 60,'flow_fee_type'=> 'percent','repay_type' => 'daily','daily_deductions'=> 3111],['duration_type'=>'months','duration' => 18,'purchase_cost'=> 1300000,'flow_fee'=> 60,'flow_fee_type'=> 'percent','repay_type' => 'daily','daily_deductions'=> 2815], ['duration_type'=>'months','duration' => 24,'purchase_cost'=> 1300000,'flow_fee'=> 60,'flow_fee_type'=> 'percent','repay_type' => 'daily','daily_deductions'=> 2444] ]), 'created_at' => $created_at]);
    }
}