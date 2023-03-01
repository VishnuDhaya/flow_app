<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFieldVisitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('field_visits', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cust_id',20);
            $table->string('loan_doc_id', 50)->nullable();
            $table->unsignedInteger('rel_mgr_id')->nullable();
            $table->dateTime('visit_start_time');
            $table->dateTime('visit_end_time')->nullable();
            $table->unsignedInteger('time_spent');
            $table->string('remarks',50)->nullable();
            $table->string('visit_purpose', 50)->nullable();
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
        Schema::dropIfExists('field_visit');
    }
}
