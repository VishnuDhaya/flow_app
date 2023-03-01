<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class TFPSeeders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('loan_products')->insert([
            'acc_purpose' => 'terminal_financing','country_code' => 'UGA','product_code' => 'TFP1','cs_model_code' => 'tf_products','product_name'=> 'TF 800K','max_loan_amount'=> 800000,'product_json' => json_encode([ ['duration_type'=>'months','duration' => 12,'purchase_cost'=> 1300000, 'flow_fee'=> 30,'flow_fee_type'=> 'percent','repay_type' => 'daily','daily_deductions'=> 2889], ['duration_type'=>'months','duration' => 15,'purchase_cost'=> 1300000,'flow_fee'=> 30,'flow_fee_type'=> 'percent','repay_type' => 'daily','daily_deductions'=> 2444 ],['duration_type'=>'months','duration' => 18,'purchase_cost'=> 1300000,'flow_fee'=> 30,'flow_fee_type'=> 'percent','repay_type' => 'daily','daily_deductions'=> 2148], ['duration_type'=>'months','duration' => 24,'purchase_cost'=> 1300000,'flow_fee'=> 30,'flow_fee_type'=> 'percent','repay_type' => 'daily','daily_deductions'=> 1778] ]) ]);
        DB::table('loan_products')->insert([
            'acc_purpose' => 'terminal_financing','country_code' => 'UGA','product_code' => 'TFP2','cs_model_code' => 'tf_products','product_name'=> 'TF 500K','max_loan_amount'=> 500000,'product_json' => json_encode([ ['duration_type'=>'months','duration' => 12,'purchase_cost'=> 1300000,'flow_fee'=> 30,'flow_fee_type'=> 'percent','repay_type' => 'daily','daily_deductions'=> 1806], ['duration_type'=>'months','duration' => 15,'purchase_cost'=> 1300000,'flow_fee'=> 30,'flow_fee_type'=> 'percent','repay_type' => 'daily','daily_deductions'=> 1528], ['duration_type'=>'months','duration' => 18,'purchase_cost'=> 1300000,'flow_fee'=> 30,'flow_fee_type'=> 'percent','repay_type' => 'daily','daily_deductions'=> 1343], ['duration_type'=>'months','duration' => 24,'purchase_cost'=> 1300000,'flow_fee'=> 30,'flow_fee_type'=> 'percent','repay_type' => 'daily','daily_deductions'=> 1111] ]) ]);
    }
}
