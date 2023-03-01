<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterKycRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('kyc_rules')->insert([
            ['rule_group_code' => 'Agreement', 'rule_message' => 'The agreement is duly signed by the customer and the signature is matching with the one on the ID Proof', 'alias_name' => 'Signature match with ID Proof', 'country_code' => '*', 'data_prvdr_code' => '*', 'status' => 'enabled','created_at' => now()]
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
