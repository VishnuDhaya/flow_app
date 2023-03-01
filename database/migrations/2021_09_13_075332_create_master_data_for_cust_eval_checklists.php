<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterDataForCustEvalChecklists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         DB::table('master_data_keys')->insert(['country_code' => '*', 'data_key' => 'eval_biz_addr_prop_type', 'parent_data_key' => "", 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);
         DB::table('master_data_keys')->insert(['country_code' => '*', 'data_key' => 'shop_busyness', 'parent_data_key' => "", 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);
         DB::table('master_data_keys')->insert(['country_code' => '*', 'data_key' => 'shop_apprearance', 'parent_data_key' => "", 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);
         DB::table('master_data_keys')->insert(['country_code' => '*', 'data_key' => 'shop_location', 'parent_data_key' => "", 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);
          DB::table('master_data_keys')->insert(['country_code' => '*', 'data_key' => 'cust_attitude', 'parent_data_key' => "", 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);

          DB::table('master_data_keys')->insert(['country_code' => '*', 'data_key' => 'cust_honesty', 'parent_data_key' => "", 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);

          DB::table('master_data_keys')->insert(['country_code' => '*', 'data_key' => 'biz_engagement', 'parent_data_key' => "", 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);

          DB::table('master_data_keys')->insert(['country_code' => '*', 'data_key' => 'neighbour_feedback', 'parent_data_key' => "", 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);
          

          

       DB::table('master_data')->insert([ 
            
            ['country_code'=> '*', 'data_key' => 'eval_biz_addr_prop_type', 'data_code' => 'permanent' , 'data_value' => 'Permanent' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

             ['country_code'=> '*', 'data_key' => 'eval_biz_addr_prop_type', 'data_code' => 'semi_permanent' , 'data_value' => 'Semi-Permanent' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

             ['country_code'=> '*', 'data_key' => 'eval_biz_addr_prop_type', 'data_code' => 'movable' , 'data_value' => 'Movable' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

             ['country_code'=> '*', 'data_key' => 'shop_busyness', 'data_code' => 'very_busy' , 'data_value' => 'Very busy' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

             ['country_code'=> '*', 'data_key' => 'shop_busyness', 'data_code' => 'so_so_medium' , 'data_value' => 'So so (medium)' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

             ['country_code'=> '*', 'data_key' => 'shop_busyness', 'data_code' => 'not_busy' , 'data_value' => 'Not busy' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

             ['country_code'=> '*', 'data_key' => 'shop_apprearance', 'data_code' => 'clean&orderly' , 'data_value' => 'Clean & Orderly' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

             ['country_code'=> '*', 'data_key' => 'shop_apprearance', 'data_code' => 'messy/disorganised' , 'data_value' => 'Messy / Disorganised' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,


            ['country_code'=> '*', 'data_key' => 'shop_location', 'data_code' => 'trading_center' , 'data_value' => 'Trading center' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

             ['country_code'=> '*', 'data_key' => 'shop_location', 'data_code' => 'cbd' , 'data_value' => 'CBD' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'shop_location', 'data_code' => 'outskirts' , 'data_value' => 'Outskirts' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'cust_attitude', 'data_code' => 'good_attitude' , 'data_value' => 'Good Attitude' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'cust_attitude', 'data_code' => 'bad_attitude' , 'data_value' => 'Bad Attitude' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'cust_honesty', 'data_code' => 'truthful' , 'data_value' => 'Truthful' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'cust_honesty', 'data_code' => 'telling_us_what_he/she_thinks_we_want_to_hear' , 'data_value' => 'Telling us what he/she thinks we want to hear' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'cust_honesty', 'data_code' => 'i_am_not_sure_if_he/she_told_the_truth' , 'data_value' => 'I am not sure if he/she told the truth' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'biz_engagement', 'data_code' => 'good_interaction' , 'data_value' => 'Good interaction' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'biz_engagement', 'data_code' => 'poor_interaction' , 'data_value' => 'Poor interaction' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'biz_engagement', 'data_code' => 'fair_interactions' , 'data_value' => 'Fair interactions' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'neighbour_feedback', 'data_code' => 'good' , 'data_value' => 'Good' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> '*', 'data_key' => 'neighbour_feedback', 'data_code' => 'bad' , 'data_value' => 'Bad' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
        ]);

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
