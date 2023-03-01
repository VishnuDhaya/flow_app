<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddValidityTypeFaCountInAgreements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_agreements', function (Blueprint $table) {
            $table->string('duration_type', 5)->nullable()->after("aggr_type");
            
        });

        Schema::table('cust_agreements', function (Blueprint $table) {
            $table->string('duration_type', 5)->nullable()->after("aggr_type");
            
        });

        DB::update("update borrowers set aggr_status ='active' where aggr_valid_upto > CURDATE()");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agreements', function (Blueprint $table) {
            //
        });
    }
}
