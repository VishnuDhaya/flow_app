<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPersonsVerifyMobileNum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('persons', function (Blueprint $table) {
            $table->string('verified_mobile_num',10)->default(false)->after('phone_num');
            $table->string('verified_alt_biz_mobile_num_1',10)->default(false)->after('verified_mobile_num');
            $table->string('verified_alt_biz_mobile_num_2',10)->default(false)->after('verified_alt_biz_mobile_num_1');
           
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
