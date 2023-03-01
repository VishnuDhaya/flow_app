<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAccPrvdrsAddUssdHolderNameUMTN extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::update("update acc_providers set mobile_cred_format = JSON_MERGE_PATCH(`mobile_cred_format`, '{\"ussd_holder_name_code\" : \"*165*2*:recipient*500*2#\"}') where id = 5");
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
