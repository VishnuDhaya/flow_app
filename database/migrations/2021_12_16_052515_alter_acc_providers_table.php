<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAccProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('acc_providers', function (Blueprint $table) {
            $table->unsignedInteger('contact_person_id')->nullable()->after("org_id");
            $table->double('cust_comm', 15,2)->nullable()->after("contact_person_id");
            $table->double('repay_comm', 15,2)->nullable()->after("cust_comm");
        });

        DB::update("update acc_providers set acc_provider_logo = '1601458332.png', cust_comm =25000.00, repay_comm=500.00 where id =1 ");
        DB::update("update acc_providers set acc_provider_logo = '1601458279.png' where id =4 ");
        DB::update("update acc_providers set acc_provider_logo = '1627630100.png' where id =5 ");
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
