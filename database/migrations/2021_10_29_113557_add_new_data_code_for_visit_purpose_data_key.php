<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewDataCodeForVisitPurposeDataKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_data')->insert([
            ['country_code'=> '*', 'data_key' => 'visit_purpose', 'data_code' => 'introductory_visit' , 'data_value' => 'Introductory Visit' , 'parent_data_code' => '', 'created_at' => now(), 'data_type' => 'common', 'status' => 'enabled']  
        ]);
        DB::update('update master_data set data_value = "Customer Feedback" where id = 516');
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
