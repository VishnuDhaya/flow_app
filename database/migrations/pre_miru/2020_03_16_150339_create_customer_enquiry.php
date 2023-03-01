<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerEnquiry extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_enquiry', function (Blueprint $table) {
            $table->increments('id');
            $table->string('market_code',4);
            $table->string('data_provider_code',4);
            $table->string('mob_num',20);
            $table->string('data_prvdr_cust_id',20);
            $table->string('message',256);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_enquiry');
    }
}
