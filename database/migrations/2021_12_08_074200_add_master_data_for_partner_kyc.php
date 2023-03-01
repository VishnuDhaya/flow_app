<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMasterDataForPartnerKyc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data_keys')->insert(['country_code' => 'UGA', 'data_key' => 'UEZM_MainContent_ddlNatureOfBusiness', 'parent_data_key' => "", 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);
        DB::table('master_data_keys')->insert(['country_code' => 'UGA', 'data_key' => 'UEZM_MainContent_ddOperatedBy', 'parent_data_key' => "", 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);
        DB::table('master_data_keys')->insert(['country_code' => 'UGA', 'data_key' => 'UEZM_MainContent_ddlBankCode', 'parent_data_key' => "", 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);
        DB::table('master_data_keys')->insert(['country_code' => 'UGA', 'data_key' => 'UEZM_MainContent_ddWallet', 'parent_data_key' => "", 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);
        DB::table('master_data_keys')->insert(['country_code' => 'UGA', 'data_key' => 'UEZM_MainContent_ddlZone', 'parent_data_key' => "", 'data_type' => 'common', 'status' => 'enabled', 'created_at' => now()]);

        DB::table('master_data')->insert([ 
               

            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlNatureOfBusiness', 'data_code' => '12' , 'data_value' => 'Bakery and Cakes' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlNatureOfBusiness', 'data_code' => '1' , 'data_value' => 'Barber and beauty shops' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlNatureOfBusiness', 'data_code' => '15' , 'data_value' => 'Education' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlNatureOfBusiness', 'data_code' => '3' , 'data_value' => 'Laundries' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlNatureOfBusiness', 'data_code' => '2' , 'data_value' => 'Electrical repair shops' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlNatureOfBusiness', 'data_code' => '4' , 'data_value' => 'Medical, dental and health services' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlNatureOfBusiness', 'data_code' => '5' , 'data_value' => 'Parts and accessories for motor vehicles' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlNatureOfBusiness', 'data_code' => '16' , 'data_value' => 'Pay TV' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlNatureOfBusiness', 'data_code' => '6' , 'data_value' => 'Provision store (Kedai Runcit)' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlNatureOfBusiness', 'data_code' => '7' , 'data_value' => 'Repair of motor vehicles and motorcycles' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlNatureOfBusiness', 'data_code' => '8' , 'data_value' => 'Restaurants and Hotels' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlNatureOfBusiness', 'data_code' => '9' , 'data_value' => 'Retail Trade' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlNatureOfBusiness', 'data_code' => '14' , 'data_value' => 'Sport Betting' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlNatureOfBusiness', 'data_code' => '10' , 'data_value' => 'Supermarket' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlNatureOfBusiness', 'data_code' => '11' , 'data_value' => 'Telecommunications' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlNatureOfBusiness', 'data_code' => '13' , 'data_value' => 'Utilities' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddOperatedBy', 'data_code' => '1' , 'data_value' => 'Owner' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddOperatedBy', 'data_code' => '2' , 'data_value' => 'Employee' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlBankCode', 'data_code' => 'TBOA' , 'data_value' => 'Bank of Africa' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlBankCode', 'data_code' => 'TBARCLAYS' , 'data_value' => 'Barclays Bank' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlBankCode', 'data_code' => 'CENTENARY' , 'data_value' => 'Centenary Bank' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlBankCode', 'data_code' => 'CRANE' , 'data_value' => 'Crane Bank' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlBankCode', 'data_code' => 'DFCU' , 'data_value' => 'DFCU Bank' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlBankCode', 'data_code' => 'DTB' , 'data_value' => 'Diamond Trust Bank' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlBankCode', 'data_code' => 'ECO' , 'data_value' => 'ECO Bank' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlBankCode', 'data_code' => 'TEQUITY' , 'data_value' => 'Equity Bank' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlBankCode', 'data_code' => 'HFB' , 'data_value' => 'Housing Finance Bank' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlBankCode', 'data_code' => 'IMP' , 'data_value' => 'Imperial Bank' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlBankCode', 'data_code' => 'TKCB' , 'data_value' => 'KCB' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlBankCode', 'data_code' => 'NCBU' , 'data_value' => 'NC Bank Uganda' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlBankCode', 'data_code' => 'OPPORTUNITY' , 'data_value' => 'Opportunity bank' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlBankCode', 'data_code' => 'TPOSTBANK' , 'data_value' => 'Post Bank' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlBankCode', 'data_code' => 'STANBIC' , 'data_value' => 'Stanbic Bank' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlBankCode', 'data_code' => 'SCB' , 'data_value' => 'Standard Chartered Bank' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlBankCode', 'data_code' => 'TROPICAL' , 'data_value' => 'Tropical Bank' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlBankCode', 'data_code' => 'UBA' , 'data_value' => 'United Bank of Africa' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddWallet', 'data_code' => '1' , 'data_value' => 'SC' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddWallet', 'data_code' => '2' , 'data_value' => 'Merchant Acct.' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddWallet', 'data_code' => '3' , 'data_value' => 'SC + Merchant Acct.' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlZone', 'data_code' => '1' , 'data_value' => 'Telco Phone Shops' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlZone', 'data_code' => '2' , 'data_value' => 'Non Telco Phone Shops' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlZone', 'data_code' => '3' , 'data_value' => 'Urban' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlZone', 'data_code' => '4' , 'data_value' => 'Sub urban' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,
            ['country_code'=> 'UGA', 'data_key' => 'UEZM_MainContent_ddlZone', 'data_code' => '5' , 'data_value' => 'Remote' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled'] ,

        ]);

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
