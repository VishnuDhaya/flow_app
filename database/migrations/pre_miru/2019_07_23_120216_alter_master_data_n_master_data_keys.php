<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMasterDataNMasterDataKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data_keys')->insert([
            ['country_code' => '*', 'data_key' => 'data_type', 'parent_data_key' => NULL, 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()],

            ['country_code' => '*', 'data_key' => 'data_group', 'parent_data_key' => NULL, 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()],

            ['country_code' => '*', 'data_key' => 'score_result_code', 'parent_data_key' => NULL, 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]
        ]);

        DB::table('master_data')->insert([
            ['country_code' => '*', 'data_key' => 'data_type', 'data_code' => 'common', 'data_value' => 'Common',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => '*', 'data_key' => 'data_type', 'data_code' => 'address', 'data_value' => 'Address',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => '*', 'data_key' => 'data_group', 'data_code' => 'credit_score', 'data_value' => 'Credit Score Data',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => '*', 'data_key' => 'data_group', 'data_code' => 'loan', 'data_value' => 'Float Advance Data',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => '*', 'data_key' => 'data_group', 'data_code' => 'address', 'data_value' => 'Address Data',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => '*', 'data_key' => 'data_group', 'data_code' => 'entity', 'data_value' => 'Entity Data',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => '*', 'data_key' => 'data_group', 'data_code' => 'person', 'data_value' => 'Person Data',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => '*', 'data_key' => 'data_group', 'data_code' => 'account', 'data_value' => 'Account',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => '*', 'data_key' => 'data_group', 'data_code' => 'others', 'data_value' => 'Other Data',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => '*', 'data_key' => 'data_group', 'data_code' => 'report', 'data_value' => 'Report',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => '*', 'data_key' => 'data_group', 'data_code' => 'core_master_data', 'data_value' => 'Core Master Data',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],





            ['country_code' => '*', 'data_key' => 'csf_group', 'data_code' => 'meta_data', 'data_value' => 'Meta Data',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' =>  '*', "data_key" => "csf_type","data_code" => "meta_txn_start_date","data_value" => "Meta Txn Start Date", 'status' => 'enabled' ,   'parent_data_code' => 'meta_data', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' =>  '*', "data_key" => "csf_type","data_code" => "meta_txn_end_date","data_value" => "Meta Txn End Date", 'status' => 'enabled' ,   'parent_data_code' => 'meta_data', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' =>  '*', "data_key" => "csf_type","data_code" => "meta_txn_days","data_value" => "Meta Txn Days", 'status' => 'enabled' ,   'parent_data_code' => 'meta_data', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' =>  '*', "data_key" => "csf_type","data_code" => "meta_cal_days","data_value" => "Meta Cal Days", 'status' => 'enabled' ,   'parent_data_code' => 'meta_data', 'data_type'  => 'common', 'created_at' => now()],


            ['country_code' => '*', 'data_key' => 'score_result_code', 'data_code' => 'ineligible', 'data_value' => 'Ineligible',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => '*', 'data_key' => 'score_result_code', 'data_code' => 'requires_flow_rm_approval', 'data_value' => 'Requires FLOW RM Approval',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()],
            ['country_code' => '*', 'data_key' => 'score_result_code', 'data_code' => 'eligible', 'data_value' => 'Eligible',  'parent_data_code' => null,   'status' =>  'enabled', 'data_type'  => 'common', 'created_at' => now()]

        ]);

        Schema::table('master_data_keys', function (Blueprint $table) {
            $table->string('data_group',16)->nullable()->after('data_type');
        });

        DB::table('master_data_keys')->where('data_key','csf_model')->update(['country_code'=>'UGA']);
        DB::table('master_data')->where('data_key','csf_model')->update(['country_code'=>'UGA']);

        DB::table('master_data_keys')->whereIn('data_key', ['csf_group','csf_type','csf_model','score_result_code'])->update(['data_group' => 'credit_score']);
        DB::table('master_data_keys')->whereIn('data_key', ['action_reason_code','transaction_mode','loan_appl_status','loan_status'])->update(['data_group' => 'loan']);
        DB::table('master_data_keys')->whereIn('data_key', ['addr_type','biz_address_prop_type','region','county','sub_county', 'district', 'biz_addr_prop_type', 'address_fields'])->update(['data_group' => 'address']);
        DB::table('master_data_keys')->whereIn('data_key', ['borrower_type','lender_type','data_provider_type', 'aggr_status','product_type'])->update(['data_group' => 'entity']);
        DB::table('master_data_keys')->whereIn('data_key', ['gender','designation'])->update(['data_group' => 'person']);
        
        DB::table('master_data_keys')->whereIn('data_key', ['float_kpi_rpt_metrics', 'float_kpi_rpt_sections'])->update(['data_group' => 'report']);

        DB::table('master_data_keys')->whereIn('data_key', ['account_type','account_transaction_type', 'account_purpose'])->update(['data_group' => 'account']);
        DB::table('master_data_keys')->whereIn('data_key', ['status','data_group','data_type'])->update(['data_group' => 'core_master_data']);


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('master_data_keys')->whereIn('data_key',['data_type','data_group'])->delete();
        DB::table('master_data')->whereIn('data_key',['data_type','data_group'])->delete();
        Schema::table('master_data_keys', function (Blueprint $table) {   
               $table->dropColumn('data_group');     
        });
        DB::table('master_data_keys')->where('data_key','csf_model')->update(['country_code'=>'*']);
        DB::table('master_data')->where('data_key','csf_model')->update(['country_code'=>'*']);
       
    }
}
