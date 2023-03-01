<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanProvisioning extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_loss_provisions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code', 4);
            $table->unsignedInteger('year');
            $table->double('prov_amount',15,2)->default(0);
            $table->double('balance',15,2)->default(0)->nullable();
            $table->double('requested_amount',15,2)->default(0)->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loan_loss_provisions');
    }
}
