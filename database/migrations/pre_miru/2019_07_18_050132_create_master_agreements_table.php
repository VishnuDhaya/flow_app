<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMasterAgreementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_agreements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country_code',4)->nullable();
            $table->string('aggr_doc_id',32)->nullable(false);
            $table->string('product_id_csv',512)->nullable(false);
            $table->string('lender_code',4)->nullable();
            $table->string('data_prvdr_code',4)->nullable();
            $table->dateTime('valid_from')->nullable(false);
            $table->dateTime('valid_upto')->nullable();
            $table->string('status',16)->nullable()->default('enabled');
            $table->unsignedInteger('created_by')->nullable();   
            $table->unsignedInteger('updated_by')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_agreements');
    }
}
