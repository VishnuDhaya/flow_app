<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisbursalAttempts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('disbursal_attempts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('status', 20)->nullable();
            $table->string('loan_doc_id', 50);
            $table->json('flow_request')->nullable();
            $table->json('flow_response')->nullable();
            $table->json('partner_request')->nullable();
            $table->json('partner_response')->nullable();
            $table->json('partner_combined_response')->nullable();
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
        Schema::dropIfExists('disbursal_attempts');
    }
}
