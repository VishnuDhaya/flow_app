<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPhotoWitnessNationalIdOnCustAgreementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cust_agreements', function (Blueprint $table){
            $table->string("witness_mobile_num",20)->after('witness_name')->nullable();
            $table->string("photo_witness_national_id",50)->after('witness_mobile_num')->nullable();
            $table->string("photo_witness_national_id_back",50)->after('photo_witness_national_id')->nullable();

        });
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
