<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRiskCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::delete("delete from risk_category_rules");
        DB::table('risk_category_rules')->insert([ 

            ['country_code' => 'UGA', 'data_prvdr_code' => 'UEZM', 'cust_state' => 'current_overdue', 'exposure_from' => '0', 'exposure_upto' => '2000000', 'days_type' => 'curr_od_days', 'late_days_from' => '0', 'late_days_to' => '10', 'fas_from' => '0', 'fas_to' => '0', 'risk_category' => '1_medium_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'UEZM', 'cust_state' => 'current_overdue', 'exposure_from' => '0', 'exposure_upto' => '2000000', 'days_type' => 'curr_od_days', 'late_days_from' => '11', 'late_days_to' => '30', 'fas_from' => '0', 'fas_to' => '0', 'risk_category' => '2_high_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'UEZM', 'cust_state' => 'current_overdue', 'exposure_from' => '0', 'exposure_upto' => '2000000', 'days_type' => 'curr_od_days', 'late_days_from' => '31', 'late_days_to' => '9999', 'fas_from' => '0', 'fas_to' => '0', 'risk_category' => '3_very_high_risk', 'created_at' => now()],

['country_code' => 'UGA', 'data_prvdr_code' => 'UEZM', 'cust_state' => 'current_overdue', 'exposure_from' => '2000001', 'exposure_upto' => '5000000', 'days_type' => 'curr_od_days', 'late_days_from' => '0', 'late_days_to' => '10', 'fas_from' => '0', 'fas_to' => '0', 'risk_category' => '1_medium_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'UEZM', 'cust_state' => 'current_overdue', 'exposure_from' => '2000001', 'exposure_upto' => '5000000', 'days_type' => 'curr_od_days', 'late_days_from' => '11', 'late_days_to' => '30', 'fas_from' => '0', 'fas_to' => '0', 'risk_category' => '2_high_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'UEZM', 'cust_state' => 'current_overdue', 'exposure_from' => '2000001', 'exposure_upto' => '5000000', 'days_type' => 'curr_od_days', 'late_days_from' => '31', 'late_days_to' => '9999', 'fas_from' => '0', 'fas_to' => '0', 'risk_category' => '3_very_high_risk', 'created_at' => now()],

['country_code' => 'UGA', 'data_prvdr_code' => 'UEZM', 'cust_state' => 'current_overdue', 'exposure_from' => '5000001', 'exposure_upto' => '100000000', 'days_type' => 'curr_od_days', 'late_days_from' => '0', 'late_days_to' => '10', 'fas_from' => '0', 'fas_to' => '0', 'risk_category' => '2_high_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'UEZM', 'cust_state' => 'current_overdue', 'exposure_from' => '5000001', 'exposure_upto' => '100000000', 'days_type' => 'curr_od_days', 'late_days_from' => '11', 'late_days_to' => '30', 'fas_from' => '0', 'fas_to' => '0', 'risk_category' => '2_high_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'UEZM', 'cust_state' => 'current_overdue', 'exposure_from' => '5000001', 'exposure_upto' => '100000000', 'days_type' => 'curr_od_days', 'late_days_from' => '31', 'late_days_to' => '9999', 'fas_from' => '0', 'fas_to' => '0', 'risk_category' => '3_very_high_risk', 'created_at' => now()],

['country_code' => 'UGA', 'data_prvdr_code' => 'UEZM', 'cust_state' => 'no_current_overdue', 'exposure_from' => '0', 'exposure_upto' => '2000000', 'days_type' => 'late_days', 'late_days_from' => '0', 'late_days_to' => '0', 'fas_from' => '6', 'fas_to' => '15', 'risk_category' => '0_low_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'UEZM', 'cust_state' => 'no_current_overdue', 'exposure_from' => '0', 'exposure_upto' => '2000000', 'days_type' => 'late_days', 'late_days_from' => '0', 'late_days_to' => '0', 'fas_from' => '1', 'fas_to' => '5', 'risk_category' => '0_low_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'UEZM', 'cust_state' => 'no_current_overdue', 'exposure_from' => '0', 'exposure_upto' => '2000000', 'days_type' => 'late_days', 'late_days_from' => '1', 'late_days_to' => '10', 'fas_from' => '6', 'fas_to' => '15', 'risk_category' => '0_low_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'UEZM', 'cust_state' => 'no_current_overdue', 'exposure_from' => '0', 'exposure_upto' => '2000000', 'days_type' => 'late_days', 'late_days_from' => '1', 'late_days_to' => '10', 'fas_from' => '1', 'fas_to' => '5', 'risk_category' => '1_medium_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'UEZM', 'cust_state' => 'no_current_overdue', 'exposure_from' => '0', 'exposure_upto' => '2000000', 'days_type' => 'late_days', 'late_days_from' => '11', 'late_days_to' => '9999', 'fas_from' => '6', 'fas_to' => '15', 'risk_category' => '1_medium_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'UEZM', 'cust_state' => 'no_current_overdue', 'exposure_from' => '0', 'exposure_upto' => '2000000', 'days_type' => 'late_days', 'late_days_from' => '11', 'late_days_to' => '9999', 'fas_from' => '1', 'fas_to' => '5', 'risk_category' => '2_high_risk', 'created_at' => now()],

['country_code' => 'UGA', 'data_prvdr_code' => 'UEZM', 'cust_state' => 'no_current_overdue', 'exposure_from' => '2000001', 'exposure_upto' => '5000000', 'days_type' => 'late_days', 'late_days_from' => '0', 'late_days_to' => '0', 'fas_from' => '6', 'fas_to' => '15', 'risk_category' => '0_low_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'UEZM', 'cust_state' => 'no_current_overdue', 'exposure_from' => '2000001', 'exposure_upto' => '5000000', 'days_type' => 'late_days', 'late_days_from' => '0', 'late_days_to' => '0', 'fas_from' => '1', 'fas_to' => '5', 'risk_category' => '0_low_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'UEZM', 'cust_state' => 'no_current_overdue', 'exposure_from' => '2000001', 'exposure_upto' => '5000000', 'days_type' => 'late_days', 'late_days_from' => '1', 'late_days_to' => '10', 'fas_from' => '6', 'fas_to' => '15', 'risk_category' => '0_low_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'UEZM', 'cust_state' => 'no_current_overdue', 'exposure_from' => '2000001', 'exposure_upto' => '5000000', 'days_type' => 'late_days', 'late_days_from' => '1', 'late_days_to' => '10', 'fas_from' => '1', 'fas_to' => '5', 'risk_category' => '1_medium_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'UEZM', 'cust_state' => 'no_current_overdue', 'exposure_from' => '2000001', 'exposure_upto' => '5000000', 'days_type' => 'late_days', 'late_days_from' => '11', 'late_days_to' => '9999', 'fas_from' => '6', 'fas_to' => '15', 'risk_category' => '1_medium_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'UEZM', 'cust_state' => 'no_current_overdue', 'exposure_from' => '2000001', 'exposure_upto' => '5000000', 'days_type' => 'late_days', 'late_days_from' => '11', 'late_days_to' => '9999', 'fas_from' => '1', 'fas_to' => '5', 'risk_category' => '2_high_risk', 'created_at' => now()],

['country_code' => 'UGA', 'data_prvdr_code' => 'UEZM', 'cust_state' => 'no_current_overdue', 'exposure_from' => '5000001', 'exposure_upto' => '100000000', 'days_type' => 'late_days', 'late_days_from' => '0', 'late_days_to' => '0', 'fas_from' => '6', 'fas_to' => '15', 'risk_category' => '0_low_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'UEZM', 'cust_state' => 'no_current_overdue', 'exposure_from' => '5000001', 'exposure_upto' => '100000000', 'days_type' => 'late_days', 'late_days_from' => '0', 'late_days_to' => '0', 'fas_from' => '1', 'fas_to' => '5', 'risk_category' => '1_medium_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'UEZM', 'cust_state' => 'no_current_overdue', 'exposure_from' => '5000001', 'exposure_upto' => '100000000', 'days_type' => 'late_days', 'late_days_from' => '1', 'late_days_to' => '10', 'fas_from' => '6', 'fas_to' => '15', 'risk_category' => '1_medium_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'UEZM', 'cust_state' => 'no_current_overdue', 'exposure_from' => '5000001', 'exposure_upto' => '100000000', 'days_type' => 'late_days', 'late_days_from' => '1', 'late_days_to' => '10', 'fas_from' => '1', 'fas_to' => '5', 'risk_category' => '2_high_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'UEZM', 'cust_state' => 'no_current_overdue', 'exposure_from' => '5000001', 'exposure_upto' => '100000000', 'days_type' => 'late_days', 'late_days_from' => '11', 'late_days_to' => '9999', 'fas_from' => '6', 'fas_to' => '15', 'risk_category' => '2_high_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'UEZM', 'cust_state' => 'no_current_overdue', 'exposure_from' => '5000001', 'exposure_upto' => '100000000', 'days_type' => 'late_days', 'late_days_from' => '11', 'late_days_to' => '9999', 'fas_from' => '1', 'fas_to' => '5', 'risk_category' => '3_very_high_risk', 'created_at' => now()],

['country_code' => 'UGA', 'data_prvdr_code' => 'CCA', 'cust_state' => 'current_overdue', 'exposure_from' => '0', 'exposure_upto' => '1000000', 'days_type' => 'curr_od_days', 'late_days_from' => '0', 'late_days_to' => '10', 'fas_from' => '0', 'fas_to' => '0', 'risk_category' => '1_medium_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'CCA', 'cust_state' => 'current_overdue', 'exposure_from' => '0', 'exposure_upto' => '1000000', 'days_type' => 'curr_od_days', 'late_days_from' => '11', 'late_days_to' => '30', 'fas_from' => '0', 'fas_to' => '0', 'risk_category' => '2_high_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'CCA', 'cust_state' => 'current_overdue', 'exposure_from' => '0', 'exposure_upto' => '1000000', 'days_type' => 'curr_od_days', 'late_days_from' => '31', 'late_days_to' => '9999', 'fas_from' => '0', 'fas_to' => '0', 'risk_category' => '3_very_high_risk', 'created_at' => now()],

['country_code' => 'UGA', 'data_prvdr_code' => 'CCA', 'cust_state' => 'current_overdue', 'exposure_from' => '1000001', 'exposure_upto' => '3000000', 'days_type' => 'curr_od_days', 'late_days_from' => '0', 'late_days_to' => '10', 'fas_from' => '0', 'fas_to' => '0', 'risk_category' => '1_medium_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'CCA', 'cust_state' => 'current_overdue', 'exposure_from' => '1000001', 'exposure_upto' => '3000000', 'days_type' => 'curr_od_days', 'late_days_from' => '11', 'late_days_to' => '30', 'fas_from' => '0', 'fas_to' => '0', 'risk_category' => '2_high_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'CCA', 'cust_state' => 'current_overdue', 'exposure_from' => '1000001', 'exposure_upto' => '3000000', 'days_type' => 'curr_od_days', 'late_days_from' => '31', 'late_days_to' => '9999', 'fas_from' => '0', 'fas_to' => '0', 'risk_category' => '3_very_high_risk', 'created_at' => now()],

['country_code' => 'UGA', 'data_prvdr_code' => 'CCA', 'cust_state' => 'current_overdue', 'exposure_from' => '3000001', 'exposure_upto' => '100000000', 'days_type' => 'curr_od_days', 'late_days_from' => '0', 'late_days_to' => '10', 'fas_from' => '0', 'fas_to' => '0', 'risk_category' => '2_high_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'CCA', 'cust_state' => 'current_overdue', 'exposure_from' => '3000001', 'exposure_upto' => '100000000', 'days_type' => 'curr_od_days', 'late_days_from' => '11', 'late_days_to' => '30', 'fas_from' => '0', 'fas_to' => '0', 'risk_category' => '2_high_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'CCA', 'cust_state' => 'current_overdue', 'exposure_from' => '3000001', 'exposure_upto' => '100000000', 'days_type' => 'curr_od_days', 'late_days_from' => '31', 'late_days_to' => '9999', 'fas_from' => '0', 'fas_to' => '0', 'risk_category' => '3_very_high_risk', 'created_at' => now()],

['country_code' => 'UGA', 'data_prvdr_code' => 'CCA', 'cust_state' => 'no_current_overdue', 'exposure_from' => '0', 'exposure_upto' => '1000000', 'days_type' => 'late_days', 'late_days_from' => '0', 'late_days_to' => '0', 'fas_from' => '6', 'fas_to' => '15', 'risk_category' => '0_low_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'CCA', 'cust_state' => 'no_current_overdue', 'exposure_from' => '0', 'exposure_upto' => '1000000', 'days_type' => 'late_days', 'late_days_from' => '0', 'late_days_to' => '0', 'fas_from' => '1', 'fas_to' => '5', 'risk_category' => '0_low_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'CCA', 'cust_state' => 'no_current_overdue', 'exposure_from' => '0', 'exposure_upto' => '1000000', 'days_type' => 'late_days', 'late_days_from' => '1', 'late_days_to' => '10', 'fas_from' => '6', 'fas_to' => '15', 'risk_category' => '0_low_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'CCA', 'cust_state' => 'no_current_overdue', 'exposure_from' => '0', 'exposure_upto' => '1000000', 'days_type' => 'late_days', 'late_days_from' => '1', 'late_days_to' => '10', 'fas_from' => '1', 'fas_to' => '5', 'risk_category' => '1_medium_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'CCA', 'cust_state' => 'no_current_overdue', 'exposure_from' => '0', 'exposure_upto' => '1000000', 'days_type' => 'late_days', 'late_days_from' => '11', 'late_days_to' => '9999', 'fas_from' => '6', 'fas_to' => '15', 'risk_category' => '1_medium_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'CCA', 'cust_state' => 'no_current_overdue', 'exposure_from' => '0', 'exposure_upto' => '1000000', 'days_type' => 'late_days', 'late_days_from' => '11', 'late_days_to' => '9999', 'fas_from' => '1', 'fas_to' => '5', 'risk_category' => '2_high_risk', 'created_at' => now()],

['country_code' => 'UGA', 'data_prvdr_code' => 'CCA', 'cust_state' => 'no_current_overdue', 'exposure_from' => '1000001', 'exposure_upto' => '3000000', 'days_type' => 'late_days', 'late_days_from' => '0', 'late_days_to' => '0', 'fas_from' => '6', 'fas_to' => '15', 'risk_category' => '0_low_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'CCA', 'cust_state' => 'no_current_overdue', 'exposure_from' => '1000001', 'exposure_upto' => '3000000', 'days_type' => 'late_days', 'late_days_from' => '0', 'late_days_to' => '0', 'fas_from' => '1', 'fas_to' => '5', 'risk_category' => '0_low_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'CCA', 'cust_state' => 'no_current_overdue', 'exposure_from' => '1000001', 'exposure_upto' => '3000000', 'days_type' => 'late_days', 'late_days_from' => '1', 'late_days_to' => '10', 'fas_from' => '6', 'fas_to' => '15', 'risk_category' => '0_low_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'CCA', 'cust_state' => 'no_current_overdue', 'exposure_from' => '1000001', 'exposure_upto' => '3000000', 'days_type' => 'late_days', 'late_days_from' => '1', 'late_days_to' => '10', 'fas_from' => '1', 'fas_to' => '5', 'risk_category' => '1_medium_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'CCA', 'cust_state' => 'no_current_overdue', 'exposure_from' => '1000001', 'exposure_upto' => '3000000', 'days_type' => 'late_days', 'late_days_from' => '11', 'late_days_to' => '9999', 'fas_from' => '6', 'fas_to' => '15', 'risk_category' => '1_medium_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'CCA', 'cust_state' => 'no_current_overdue', 'exposure_from' => '1000001', 'exposure_upto' => '3000000', 'days_type' => 'late_days', 'late_days_from' => '11', 'late_days_to' => '9999', 'fas_from' => '1', 'fas_to' => '5', 'risk_category' => '2_high_risk', 'created_at' => now()],

['country_code' => 'UGA', 'data_prvdr_code' => 'CCA', 'cust_state' => 'no_current_overdue', 'exposure_from' => '3000001', 'exposure_upto' => '100000000', 'days_type' => 'late_days', 'late_days_from' => '0', 'late_days_to' => '0', 'fas_from' => '6', 'fas_to' => '15', 'risk_category' => '0_low_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'CCA', 'cust_state' => 'no_current_overdue', 'exposure_from' => '3000001', 'exposure_upto' => '100000000', 'days_type' => 'late_days', 'late_days_from' => '0', 'late_days_to' => '0', 'fas_from' => '1', 'fas_to' => '5', 'risk_category' => '1_medium_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'CCA', 'cust_state' => 'no_current_overdue', 'exposure_from' => '3000001', 'exposure_upto' => '100000000', 'days_type' => 'late_days', 'late_days_from' => '1', 'late_days_to' => '10', 'fas_from' => '6', 'fas_to' => '15', 'risk_category' => '1_medium_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'CCA', 'cust_state' => 'no_current_overdue', 'exposure_from' => '3000001', 'exposure_upto' => '100000000', 'days_type' => 'late_days', 'late_days_from' => '1', 'late_days_to' => '10', 'fas_from' => '1', 'fas_to' => '5', 'risk_category' => '2_high_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'CCA', 'cust_state' => 'no_current_overdue', 'exposure_from' => '3000001', 'exposure_upto' => '100000000', 'days_type' => 'late_days', 'late_days_from' => '11', 'late_days_to' => '9999', 'fas_from' => '6', 'fas_to' => '15', 'risk_category' => '2_high_risk', 'created_at' => now()],
['country_code' => 'UGA', 'data_prvdr_code' => 'CCA', 'cust_state' => 'no_current_overdue', 'exposure_from' => '3000001', 'exposure_upto' => '100000000', 'days_type' => 'late_days', 'late_days_from' => '11', 'late_days_to' => '9999', 'fas_from' => '1', 'fas_to' => '5', 'risk_category' => '3_very_high_risk', 'created_at' => now()],
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
