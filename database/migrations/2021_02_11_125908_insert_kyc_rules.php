<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertKycRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('kyc_rules')->insert([
            ['rule_group_code' => 'ID Proof', 'rule_message' => 'ID Proof is one among Ugandan National ID, Kenyan ID, Ugandan Driving License, Ugandan Passport', 'alias_name' => 'ID Proof - Type Check', 'country_code' => '*', 'data_prvdr_code' => '*', 'status' => 'enabled','created_at' => now()],
['rule_group_code' => 'ID Proof', 'rule_message' => 'ID Proof photo is taken with a white background', 'alias_name' => 'ID Proof - White Background', 'country_code' => '*', 'data_prvdr_code' => '*', 'status' => 'enabled','created_at' => now()],
['rule_group_code' => 'ID Proof', 'rule_message' => 'ID Proof photo is clear, focused, visible & recongnisable ', 'alias_name' => 'ID Proof - Photo Clarity', 'country_code' => '*', 'data_prvdr_code' => '*', 'status' => 'enabled','created_at' => now()],
['rule_group_code' => 'ID Proof', 'rule_message' => 'The text contents on the ID proof are visible', 'alias_name' => 'ID Proof - Text Clarity', 'country_code' => '*', 'data_prvdr_code' => '*', 'status' => 'enabled','created_at' => now()],
['rule_group_code' => 'Passport Size Photo', 'rule_message' => 'The face of the person in the passport size photo is clear, focused, visible and recongnisable', 'alias_name' => 'Passport Photo - Clarity', 'country_code' => '*', 'data_prvdr_code' => '*', 'status' => 'enabled','created_at' => now()],
['rule_group_code' => 'Passport Size Photo', 'rule_message' => 'The face of the person in the passport size photo matches with the customer\'s photo in the ID Proof', 'alias_name' => 'Passport Photo - Person match', 'country_code' => '*', 'data_prvdr_code' => '*', 'status' => 'enabled','created_at' => now()],
['rule_group_code' => 'Establishment Photo', 'rule_message' => 'The establishment photo is clear and is taken outside the shop, so that establishment should be clearly visible. ', 'alias_name' => '', 'country_code' => '*', 'data_prvdr_code' => '*', 'status' => 'enabled','created_at' => now()],
['rule_group_code' => 'Selfie with Customer', 'rule_message' => 'Selfie photo is clear, focused, visible & recongnisable ', 'alias_name' => 'Selfie Photo - Clarity', 'country_code' => '*', 'data_prvdr_code' => '*', 'status' => 'enabled','created_at' => now()],
['rule_group_code' => 'Selfie with Customer', 'rule_message' => 'The face of the person in the selfie photo matches with the customer\'s photo in the ID Proof', 'alias_name' => 'Selfie Photo - Person match', 'country_code' => '*', 'data_prvdr_code' => '*', 'status' => 'enabled','created_at' => now()],
['rule_group_code' => 'Basic Profile', 'rule_message' => 'The name on the customer profile matches with the name on ID proof photo. The order of first name & last name is also the same.', 'alias_name' => 'Name Match with ID Proof', 'country_code' => '*', 'data_prvdr_code' => '*', 'status' => 'enabled','created_at' => now()],
['rule_group_code' => 'Basic Profile', 'rule_message' => 'Date of birth on the profile matches with the date of birth on the ID proof photo', 'alias_name' => 'DOB Match with ID Proof', 'country_code' => '*', 'data_prvdr_code' => '*', 'status' => 'enabled','created_at' => now()],

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
