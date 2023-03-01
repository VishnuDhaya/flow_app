<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCsFactorValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('cs_factor_values', function (Blueprint $table) {
                $table->increments('id');
                $table->string('country_code', 4)->nullable();
                $table->string('csf_group', 40)->nullable();
                $table->string('csf_type', 40)->nullable();
                $table->double('value_from', 15, 2)->nullable();
                $table->double('value_to', 15, 2)->nullable();
                $table->double('normal_value', 15, 2)->nullable();
                $table->string('status', 10)->nullable()->default('enabled');
                $table->unsignedInteger('created_by')->nullable();   
                $table->unsignedInteger('updated_by')->nullable();
                $table->dateTime('created_at')->nullable();
                $table->dateTime('updated_at')->nullable();
        });

        DB::table('cs_factor_values')->insert([
              ['country_code' => '*', 'csf_group'=> 'roi','csf_type' => '5_day_avg_roi',
              'value_from' => 0,'value_to' => 0.05,'normal_value' => 2, 'created_by' => '0'],

              ['country_code' => '*', 'csf_group'=> 'roi','csf_type' => '5_day_avg_roi',
              'value_from' => 0.05,'value_to' => 0.06,'normal_value' => 4, 'created_by' => '0'],

              ['country_code' => '*', 'csf_group'=> 'roi','csf_type' => '5_day_avg_roi',
              'value_from' => 0.06,'value_to' => 0.07,'normal_value' => 6, 'created_by' => '0'],

              ['country_code' => '*', 'csf_group'=> 'roi','csf_type' => '5_day_avg_roi',
              'value_from' => 0.07,'value_to' => 0.08 ,'normal_value' => 8, 'created_by' => '0'],

              ['country_code' => '*', 'csf_group'=> 'roi','csf_type' => '5_day_avg_roi',
              'value_from' => 0.08 ,'value_to' => 99.99, 'normal_value' => 10 , 'created_by' => '0'],


              ['country_code' => '*', 'csf_group'=> 'roi','csf_type' => '15_day_avg_roi',
              'value_from' => 0,'value_to' => 0.15,'normal_value' => 2, 'created_by' => '0'],

              ['country_code' => '*', 'csf_group'=> 'roi','csf_type' => '15_day_avg_roi',
              'value_from' => 0.15,'value_to' => 0.18,'normal_value' => 4, 'created_by' => '0'],

              ['country_code' => '*', 'csf_group'=> 'roi','csf_type' => '15_day_avg_roi',
              'value_from' => 0.18,'value_to' => 0.21,'normal_value' => 6, 'created_by' => '0'],

              ['country_code' => '*', 'csf_group'=> 'roi','csf_type' => '15_day_avg_roi',
              'value_from' => 0.21,'value_to' => 0.24 ,'normal_value' => 8, 'created_by' => '0'],

              ['country_code' => '*', 'csf_group'=> 'roi','csf_type' => '15_day_avg_roi',
              'value_from' => 0.24 ,'value_to' => 99.99, 'normal_value' => 10 , 'created_by' => '0'],


              ['country_code' => '*', 'csf_group'=> 'roi','csf_type' => '30_day_avg_roi',
              'value_from' => 0,'value_to' => 0.30,'normal_value' => 2, 'created_by' => '0'],

              ['country_code' => '*', 'csf_group'=> 'roi','csf_type' => '30_day_avg_roi',
              'value_from' => 0.30,'value_to' => 0.36,'normal_value' => 4, 'created_by' => '0'],

              ['country_code' => '*', 'csf_group'=> 'roi','csf_type' => '30_day_avg_roi',
              'value_from' => 0.36,'value_to' => 0.42,'normal_value' => 6, 'created_by' => '0'],

              ['country_code' => '*', 'csf_group'=> 'roi','csf_type' => '30_day_avg_roi',
              'value_from' => 0.42,'value_to' => 0.48 ,'normal_value' => 8, 'created_by' => '0'],

              ['country_code' => '*', 'csf_group'=> 'roi','csf_type' => '30_day_avg_roi',
              'value_from' => 0.48 ,'value_to' => 99.99, 'normal_value' => 10 , 'created_by' => '0'],


              ['country_code' => '*', 'csf_group'=> 'avg_txns','csf_type' => '30_day_avg_txns',
              'value_from' => 0,'value_to' => 500 ,'normal_value' => 2, 'created_by' => '0'],

              ['country_code' => '*', 'csf_group'=> 'avg_txns','csf_type' => '30_day_avg_txns',
              'value_from' => 500 ,'value_to' => 700,'normal_value' => 4, 'created_by' => '0'],

              ['country_code' => '*', 'csf_group'=> 'avg_txns','csf_type' => '30_day_avg_txns',
              'value_from' => 700 ,'value_to' => 800,'normal_value' => 6, 'created_by' => '0'],

              ['country_code' => '*', 'csf_group'=> 'avg_txns','csf_type' => '30_day_avg_txns',
              'value_from' => 800,'value_to' => 900 ,'normal_value' => 8, 'created_by' => '0'],

              ['country_code' => '*', 'csf_group'=> 'avg_txns','csf_type' => '30_day_avg_txns',
              'value_from' => 900 ,'value_to' => 99999, 'normal_value' => 10 , 'created_by' => '0']
          ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cs_factor_values');
    }
}
